<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\License;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class WebhookController extends Controller
{
    /**
     * Handle Stripe webhook
     */
    public function stripe(Request $request): Response
    {
        Stripe::setApiKey(config('services.stripe.secret'));
        $endpoint_secret = config('services.stripe.webhook_secret');

        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            \Log::error('Invalid Stripe webhook payload', ['error' => $e->getMessage()]);
            return response('Invalid payload', 400);
        } catch (SignatureVerificationException $e) {
            // Invalid signature
            \Log::error('Invalid Stripe webhook signature', ['error' => $e->getMessage()]);
            return response('Invalid signature', 400);
        }

        // Handle the event
        switch ($event->type) {
            case 'checkout.session.completed':
                $this->handleCheckoutSessionCompleted($event->data->object);
                break;

            case 'payment_intent.succeeded':
                $this->handlePaymentIntentSucceeded($event->data->object);
                break;

            case 'payment_intent.payment_failed':
                $this->handlePaymentIntentFailed($event->data->object);
                break;

            default:
                \Log::info('Unhandled Stripe webhook event', ['type' => $event->type]);
        }

        return response('Webhook handled', 200);
    }

    /**
     * Handle checkout session completed
     */
    protected function handleCheckoutSessionCompleted($session): void
    {
        $order = Order::where('stripe_session_id', $session->id)->first();

        if (!$order) {
            \Log::error('Order not found for Stripe session', ['session_id' => $session->id]);
            return;
        }

        // Update order
        $order->update([
            'status' => 'completed',
            'payment_status' => 'paid',
            'stripe_payment_intent' => $session->payment_intent,
            'paid_at' => now(),
        ]);

        // Generate license
        $license = $this->generateLicense($order);

        // Send WhatsApp notification to customer
        try {
            $whatsapp = app(WhatsAppService::class);
            $whatsapp->sendPaymentSuccessNotification($order);
        } catch (\Exception $e) {
            \Log::error('Failed to send payment success notification', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }

        // Send WhatsApp notification to admin
        try {
            $whatsapp = app(WhatsAppService::class);
            $whatsapp->sendAdminPaymentSuccessNotification($order);
        } catch (\Exception $e) {
            \Log::error('Failed to send admin payment success notification', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }

        \Log::info('Order completed and license generated', [
            'order_id' => $order->id,
            'license_id' => $license->id,
        ]);
    }

    /**
     * Handle payment intent succeeded
     */
    protected function handlePaymentIntentSucceeded($paymentIntent): void
    {
        $order = Order::where('stripe_payment_intent', $paymentIntent->id)->first();

        if ($order) {
            $order->update([
                'payment_status' => 'paid',
                'stripe_charge_id' => $paymentIntent->charges->data[0]->id ?? null,
            ]);

            \Log::info('Payment intent succeeded', ['order_id' => $order->id]);
        }
    }

    /**
     * Handle payment intent failed
     */
    protected function handlePaymentIntentFailed($paymentIntent): void
    {
        $order = Order::where('stripe_payment_intent', $paymentIntent->id)->first();

        if ($order) {
            $order->update([
                'payment_status' => 'failed',
            ]);

            \Log::warning('Payment intent failed', [
                'order_id' => $order->id,
                'error' => $paymentIntent->last_payment_error->message ?? 'Unknown error',
            ]);
        }
    }

    /**
     * Generate license for order
     */
    protected function generateLicense(Order $order): License
    {
        // Generate unique license key
        $licenseKey = $this->generateLicenseKey();

        // Create license
        $license = License::create([
            'license_key' => $licenseKey,
            'plan_id' => $order->plan_id,
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'status' => 'active',
            'max_domains' => $order->plan->max_domains,
            'activated_domains' => [],
            'expires_at' => now()->addYear(), // 1 year from now
        ]);

        return $license;
    }

    /**
     * Generate unique license key
     */
    protected function generateLicenseKey(): string
    {
        do {
            // Format: XXXX-XXXX-XXXX-XXXX
            $key = strtoupper(substr(md5(uniqid(rand(), true)), 0, 16));
            $formatted = implode('-', str_split($key, 4));
        } while (License::where('license_key', $formatted)->exists());

        return $formatted;
    }

    /**
     * Handle Paddle webhook
     * 
     * This method handles Paddle webhook events (transaction.completed, transaction.refunded, etc.)
     * It dispatches events instead of directly handling them to avoid blocking and to decouple logic
     */
    public function paddle(Request $request): Response
    {
        // Log incoming webhook
        \Log::info('Paddle Webhook Received', [
            'payload' => $request->all(),
            'headers' => $request->headers->all(),
        ]);

        try {
            // Laravel Cashier Paddle will handle webhook verification automatically
            // We just need to process the event data
            
            $eventType = $request->input('alert_name') ?? $request->input('event_type');
            
            if (!$eventType) {
                \Log::warning('Paddle webhook missing event type');
                return response('Missing event type', 400);
            }

            \Log::info('Processing Paddle webhook', ['event_type' => $eventType]);

            // Handle different event types
            switch ($eventType) {
                case 'transaction.completed':
                case 'payment_succeeded':
                case 'subscription_payment_succeeded':
                    $this->handlePaddlePaymentSuccess($request);
                    break;

                case 'transaction.payment_failed':
                case 'payment_failed':
                    $this->handlePaddlePaymentFailed($request);
                    break;

                case 'transaction.created':
                case 'payment_created':
                    $this->handlePaddlePaymentPending($request);
                    break;

                case 'transaction.refunded':
                case 'payment_refunded':
                    $this->handlePaddlePaymentRefunded($request);
                    break;

                default:
                    \Log::info('Unhandled Paddle webhook event type', ['type' => $eventType]);
            }

            return response('Webhook received', 200);

        } catch (\Exception $e) {
            \Log::error('Error processing Paddle webhook', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            // Return 200 to prevent Paddle from retrying
            // We've logged the error for manual investigation
            return response('Error logged', 200);
        }
    }

    /**
     * Handle Paddle payment success
     */
    protected function handlePaddlePaymentSuccess(Request $request): void
    {
        $transactionId = $request->input('p_transaction_id') ?? $request->input('transaction_id');
        
        if (!$transactionId) {
            \Log::warning('Paddle payment success webhook missing transaction ID');
            return;
        }

        $order = Order::where('paddle_transaction_id', $transactionId)->first();
        
        if (!$order) {
            \Log::warning('Order not found for Paddle transaction', ['transaction_id' => $transactionId]);
            return;
        }

        // Update order status if not already completed
        if ($order->payment_status !== 'paid') {
            $order->update([
                'payment_status' => 'paid',
                'status' => 'completed',
                'paid_at' => now(),
            ]);

            \Log::info('Paddle payment success processed', [
                'order_id' => $order->id,
                'transaction_id' => $transactionId,
            ]);

            // Dispatch event untuk trigger notifikasi WhatsApp
            \App\Events\PaymentCompleted::dispatch($order);
        }
    }

    /**
     * Handle Paddle payment failed
     */
    protected function handlePaddlePaymentFailed(Request $request): void
    {
        $transactionId = $request->input('p_transaction_id') ?? $request->input('transaction_id');
        $reason = $request->input('failure_reason') ?? 'Payment failed';
        
        if (!$transactionId) {
            \Log::warning('Paddle payment failed webhook missing transaction ID');
            return;
        }

        $order = Order::where('paddle_transaction_id', $transactionId)->first();
        
        if (!$order) {
            \Log::warning('Order not found for Paddle transaction', ['transaction_id' => $transactionId]);
            return;
        }

        $order->update([
            'payment_status' => 'failed',
            'status' => 'failed',
        ]);

        \Log::info('Paddle payment failed processed', [
            'order_id' => $order->id,
            'transaction_id' => $transactionId,
            'reason' => $reason,
        ]);

        // Dispatch event untuk trigger notifikasi WhatsApp
        \App\Events\PaymentFailed::dispatch($order, $reason);
    }

    /**
     * Handle Paddle payment pending
     */
    protected function handlePaddlePaymentPending(Request $request): void
    {
        $transactionId = $request->input('p_transaction_id') ?? $request->input('transaction_id');
        
        if (!$transactionId) {
            \Log::warning('Paddle payment pending webhook missing transaction ID');
            return;
        }

        $order = Order::where('paddle_transaction_id', $transactionId)->first();
        
        if (!$order) {
            \Log::warning('Order not found for Paddle transaction', ['transaction_id' => $transactionId]);
            return;
        }

        $order->update([
            'payment_status' => 'pending',
            'status' => 'pending',
        ]);

        \Log::info('Paddle payment pending processed', [
            'order_id' => $order->id,
            'transaction_id' => $transactionId,
        ]);

        // Dispatch event untuk trigger notifikasi WhatsApp
        \App\Events\PaymentPending::dispatch($order);
    }

    /**
     * Handle Paddle payment refunded
     */
    protected function handlePaddlePaymentRefunded(Request $request): void
    {
        $transactionId = $request->input('p_transaction_id') ?? $request->input('transaction_id');
        
        if (!$transactionId) {
            \Log::warning('Paddle payment refunded webhook missing transaction ID');
            return;
        }

        $order = Order::where('paddle_transaction_id', $transactionId)->first();
        
        if (!$order) {
            \Log::warning('Order not found for Paddle transaction', ['transaction_id' => $transactionId]);
            return;
        }

        $order->update([
            'payment_status' => 'refunded',
            'status' => 'refunded',
        ]);

        \Log::info('Paddle payment refunded processed', [
            'order_id' => $order->id,
            'transaction_id' => $transactionId,
        ]);

        // Dispatch event untuk trigger notifikasi WhatsApp
        \App\Events\PaymentRefunded::dispatch($order);
    }
}
