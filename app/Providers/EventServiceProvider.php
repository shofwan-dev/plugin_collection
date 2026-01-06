<?php

namespace App\Providers;

use App\Events\PaymentCompleted;
use App\Events\PaymentFailed;
use App\Events\PaymentPending;
use App\Events\PaymentRefunded;
use App\Listeners\SendPaymentCompletedNotification;
use App\Listeners\SendPaymentFailedNotification;
use App\Listeners\SendPaymentPendingNotification;
use App\Listeners\SendPaymentRefundedNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        // Payment Events
        PaymentCompleted::class => [
            SendPaymentCompletedNotification::class,
        ],

        PaymentFailed::class => [
            SendPaymentFailedNotification::class,
        ],

        PaymentPending::class => [
            SendPaymentPendingNotification::class,
        ],

        PaymentRefunded::class => [
            SendPaymentRefundedNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
