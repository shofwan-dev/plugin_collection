<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Events\PaymentCompleted;
use App\Events\PaymentFailed;
use App\Events\PaymentPending;
use App\Events\PaymentRefunded;
use Illuminate\Console\Command;

class TestPaymentNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:payment-notification {order_id} {status=completed}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test payment notification by dispatching event for specific order';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $orderId = $this->argument('order_id');
        $status = $this->argument('status');

        // Find order
        $order = Order::find($orderId);

        if (!$order) {
            $this->error("Order #{$orderId} not found!");
            return 1;
        }

        $this->info("Testing {$status} notification for Order #{$orderId}");
        $this->info("Customer: {$order->customer_name}");
        $this->info("Product: {$order->product->name}");
        $this->info("WhatsApp: " . ($order->whatsapp_number ?? 'Not set'));
        
        $this->newLine();
        
        if (!$this->confirm('Send test notification?', true)) {
            $this->info('Cancelled.');
            return 0;
        }

        try {
            // Dispatch appropriate event based on status
            switch ($status) {
                case 'completed':
                case 'success':
                    PaymentCompleted::dispatch($order);
                    $this->info('✓ PaymentCompleted event dispatched');
                    break;

                case 'failed':
                    PaymentFailed::dispatch($order, 'Test failure reason');
                    $this->info('✓ PaymentFailed event dispatched');
                    break;

                case 'pending':
                    PaymentPending::dispatch($order);
                    $this->info('✓ PaymentPending event dispatched');
                    break;

                case 'refunded':
                    PaymentRefunded::dispatch($order);
                    $this->info('✓ PaymentRefunded event dispatched');
                    break;

                default:
                    $this->error("Invalid status: {$status}");
                    $this->error("Valid statuses: completed, failed, pending, refunded");
                    return 1;
            }

            $this->newLine();
            $this->info('Event dispatched successfully!');
            $this->info('Check logs for WhatsApp notification status:');
            $this->line('tail -f storage/logs/laravel.log | grep -i "whatsapp"');

            return 0;

        } catch (\Exception $e) {
            $this->error('Error dispatching event: ' . $e->getMessage());
            return 1;
        }
    }
}
