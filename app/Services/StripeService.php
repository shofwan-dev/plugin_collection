<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Plan;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Webhook;
use Exception;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Create checkout session
     */
    public function createCheckoutSession(Plan $plan, array $customerData): array
    {
        try {
            // Create order first
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'plan_id' => $plan->id,
                'customer_name' => $customerData['name'],
                'customer_email' => $customerData['email'],
                'amount' => $plan->price,
                'currency' => 'USD',
                'status' => 'pending',
            ]);

            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => 'CF7 to WhatsApp - ' . $plan->name,
                            'description' => $plan->description,
                        ],
                        'unit_amount' => $plan->price * 100, // Convert to cents
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('checkout.cancel'),
                'customer_email' => $customerData['email'],
                'client_reference_id' => $order->id,
                'metadata' => [
                    'order_id' => $order->id,
                    'plan_id' => $plan->id,
                ],
            ]);

            // Update order with session ID
            $order->update([
                'stripe_session_id' => $session->id,
            ]);

            return [
                'success' => true,
                'session_id' => $session->id,
                'order_id' => $order->id,
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Handle webhook event
     */
    public function handleWebhook(string $payload, string $signature): array
    {
        try {
            $event = Webhook::constructEvent(
                $payload,
                $signature,
                config('services.stripe.webhook_secret')
            );

            switch ($event->type) {
                case 'checkout.session.completed':
                    return $this->handleCheckoutCompleted($event->data->object);

                case 'charge.refunded':
                    return $this->handleChargeRefunded($event->data->object);

                default:
                    return [
                        'success' => true,
                        'message' => 'Unhandled event type',
                    ];
            }

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Handle checkout completed event
     */
    private function handleCheckoutCompleted($session): array
    {
        $orderId = $session->metadata->order_id ?? $session->client_reference_id;
        
        $order = Order::find($orderId);

        if (!$order) {
            return [
                'success' => false,
                'message' => 'Order not found',
            ];
        }

        // Update order
        $order->update([
            'status' => 'completed',
            'stripe_payment_intent' => $session->payment_intent,
            'paid_at' => now(),
        ]);

        // Generate license
        $licenseGenerator = new LicenseGenerator();
        $license = $licenseGenerator->createForOrder($order);

        // TODO: Send email with license key

        return [
            'success' => true,
            'message' => 'Order completed',
            'order_id' => $order->id,
            'license_key' => $license->license_key,
        ];
    }

    /**
     * Handle charge refunded event
     */
    private function handleChargeRefunded($charge): array
    {
        $order = Order::where('stripe_charge_id', $charge->id)->first();

        if (!$order) {
            return [
                'success' => false,
                'message' => 'Order not found',
            ];
        }

        // Update order status
        $order->markAsRefunded();

        // Suspend license
        if ($order->license) {
            $order->license->update(['status' => 'suspended']);
        }

        return [
            'success' => true,
            'message' => 'Order refunded',
        ];
    }

    /**
     * Retrieve session
     */
    public function retrieveSession(string $sessionId): ?object
    {
        try {
            return Session::retrieve($sessionId);
        } catch (Exception $e) {
            return null;
        }
    }
}
