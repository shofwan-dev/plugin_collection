# Implementation Guide: Email Notifications, Live Sales & Testing

## 📧 **1. Email Notifications - COMPLETED**

### Mail Classes Created:
- ✅ `OrderCreatedMail.php` - Order confirmation
- ✅ `LicenseActivatedMail.php` - License activation
- ✅ `OrderRefundedMail.php` - Refund notification

### Email Templates Needed:
Create these files in `resources/views/emails/`:

#### `order-created.blade.php`
```blade
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #4F46E5; color: white; padding: 20px; text-align: center; }
        .content { background: #f9fafb; padding: 30px; }
        .button { background: #4F46E5; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Order Confirmation</h1>
        </div>
        <div class="content">
            <p>Hi {{ $order->customer_name }},</p>
            <p>Thank you for your order! Your order has been received and is being processed.</p>
            
            <h3>Order Details:</h3>
            <ul>
                <li><strong>Order Number:</strong> {{ $order->order_number }}</li>
                <li><strong>Plan:</strong> {{ $order->plan->name }}</li>
                <li><strong>Amount:</strong> ${{ number_format($order->amount, 2) }}</li>
                <li><strong>Status:</strong> {{ ucfirst($order->status) }}</li>
            </ul>

            @if($order->payment_status === 'paid')
            <p>Your license key will be sent to you shortly.</p>
            @else
            <p>Please complete your payment to activate your license.</p>
            @endif

            <p style="margin-top: 30px;">
                <a href="{{ route('dashboard.orders.show', $order) }}" class="button">View Order</a>
            </p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} CF7 WhatsApp. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
```

#### `license-activated.blade.php`
```blade
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #10B981; color: white; padding: 20px; text-align: center; }
        .content { background: #f9fafb; padding: 30px; }
        .license-key { background: #fff; border: 2px dashed #10B981; padding: 15px; text-align: center; font-size: 20px; font-family: monospace; margin: 20px 0; }
        .button { background: #10B981; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 License Activated!</h1>
        </div>
        <div class="content">
            <p>Congratulations! Your license has been activated successfully.</p>
            
            <div class="license-key">
                {{ $license->license_key }}
            </div>

            <h3>License Details:</h3>
            <ul>
                <li><strong>Plan:</strong> {{ $license->plan->name }}</li>
                <li><strong>Max Domains:</strong> {{ $license->max_domains === -1 ? 'Unlimited' : $license->max_domains }}</li>
                <li><strong>Expires:</strong> {{ $license->expires_at ? $license->expires_at->format('Y-m-d') : 'Never' }}</li>
            </ul>

            <h3>Next Steps:</h3>
            <ol>
                <li>Download the plugin from your dashboard</li>
                <li>Install it on your WordPress site</li>
                <li>Activate using your license key above</li>
            </ol>

            <p style="margin-top: 30px;">
                <a href="{{ route('dashboard.licenses') }}" class="button">View License</a>
            </p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} CF7 WhatsApp. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
```

#### `order-refunded.blade.php`
```blade
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #EF4444; color: white; padding: 20px; text-align: center; }
        .content { background: #f9fafb; padding: 30px; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Order Refunded</h1>
        </div>
        <div class="content">
            <p>Hi {{ $order->customer_name }},</p>
            <p>Your order has been refunded.</p>
            
            <h3>Refund Details:</h3>
            <ul>
                <li><strong>Order Number:</strong> {{ $order->order_number }}</li>
                <li><strong>Amount Refunded:</strong> ${{ number_format($order->amount, 2) }}</li>
                @if($reason)
                <li><strong>Reason:</strong> {{ $reason }}</li>
                @endif
            </ul>

            <p>The refund will be processed within 5-10 business days.</p>

            <p>If you have any questions, please contact our support team.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} CF7 WhatsApp. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
```

### Integration with WebhookController:

Add to `app/Http/Controllers/WebhookController.php`:

```php
use App\Mail\OrderCreatedMail;
use App\Mail\LicenseActivatedMail;
use Illuminate\Support\Facades\Mail;

// In handleCheckoutCompleted method, after creating order:
Mail::to($order->customer_email)->send(new OrderCreatedMail($order));

// After generating license:
Mail::to($order->customer_email)->send(new LicenseActivatedMail($license));
```

### Email Configuration:

Update `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io  # or your SMTP server
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

---

## 📢 **2. Live Sales Notification**

### Create Livewire Component:

```bash
php artisan make:livewire LiveSalesNotification
```

### Component Class (`app/Livewire/LiveSalesNotification.php`):

```php
<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Component;

class LiveSalesNotification extends Component
{
    public $recentOrders = [];
    public $showNotification = false;
    public $currentOrder = null;

    public function mount()
    {
        $this->loadRecentOrders();
    }

    public function loadRecentOrders()
    {
        $this->recentOrders = Order::where('payment_status', 'paid')
            ->where('created_at', '>=', now()->subHours(24))
            ->latest()
            ->take(10)
            ->get();
    }

    public function showNextNotification()
    {
        if ($this->recentOrders->isNotEmpty()) {
            $this->currentOrder = $this->recentOrders->random();
            $this->showNotification = true;
            
            // Auto hide after 5 seconds
            $this->dispatch('hide-notification');
        }
    }

    public function hideNotification()
    {
        $this->showNotification = false;
    }

    public function render()
    {
        return view('livewire.live-sales-notification');
    }
}
```

### Component View (`resources/views/livewire/live-sales-notification.blade.php`):

```blade
<div>
    @if($showNotification && $currentOrder)
    <div 
        x-data="{ show: true }"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-y-4"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform translate-y-4"
        @hide-notification.window="setTimeout(() => show = false, 5000)"
        class="fixed bottom-4 left-4 z-50 max-w-sm bg-white rounded-lg shadow-lg p-4 border-l-4 border-green-500"
    >
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="ml-3 flex-1">
                <p class="text-sm font-medium text-gray-900">
                    Someone just purchased!
                </p>
                <p class="mt-1 text-sm text-gray-500">
                    {{ $currentOrder->plan->name }} - ${{ number_format($currentOrder->amount, 2) }}
                </p>
                <p class="mt-1 text-xs text-gray-400">
                    {{ $currentOrder->created_at->diffForHumans() }}
                </p>
            </div>
            <button 
                wire:click="hideNotification"
                class="ml-4 flex-shrink-0 text-gray-400 hover:text-gray-500"
            >
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>
    @endif

    <script>
        // Show notification every 30 seconds
        setInterval(() => {
            @this.call('showNextNotification');
        }, 30000);
    </script>
</div>
```

### Add to Layout (`resources/views/layouts/public.blade.php`):

```blade
@livewireScripts
<livewire:live-sales-notification />
```

---

## 🧪 **3. Automated Testing**

### Create Test Files:

```bash
php artisan make:test LicenseTest
php artisan make:test OrderTest
php artisan make:test LicenseApiTest
php artisan make:test CheckoutFlowTest
```

### Example: `tests/Feature/LicenseApiTest.php`

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\License;
use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LicenseApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_activate_license()
    {
        $plan = Plan::factory()->create(['max_domains' => 1]);
        $license = License::factory()->create([
            'plan_id' => $plan->id,
            'status' => 'active',
        ]);

        $response = $this->postJson('/api/v1/license/activate', [
            'license_key' => $license->license_key,
            'domain' => 'example.com',
        ]);

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);
    }

    public function test_cannot_activate_expired_license()
    {
        $plan = Plan::factory()->create();
        $license = License::factory()->create([
            'plan_id' => $plan->id,
            'status' => 'expired',
        ]);

        $response = $this->postJson('/api/v1/license/activate', [
            'license_key' => $license->license_key,
            'domain' => 'example.com',
        ]);

        $response->assertStatus(422);
    }

    public function test_cannot_exceed_max_activations()
    {
        $plan = Plan::factory()->create(['max_domains' => 1]);
        $license = License::factory()->create([
            'plan_id' => $plan->id,
            'status' => 'active',
            'activated_domains' => json_encode([
                ['domain' => 'existing.com', 'activated_at' => now(), 'ip' => '127.0.0.1']
            ]),
        ]);

        $response = $this->postJson('/api/v1/license/activate', [
            'license_key' => $license->license_key,
            'domain' => 'new.com',
        ]);

        $response->assertStatus(422)
                 ->assertJson(['success' => false]);
    }
}
```

### Run Tests:

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter=LicenseApiTest

# Run with coverage
php artisan test --coverage
```

---

## 📋 **Implementation Checklist**

### Email Notifications:
- [x] Create Mail classes
- [ ] Create email templates
- [ ] Integrate with WebhookController
- [ ] Configure SMTP settings
- [ ] Test email sending

### Live Sales Notification:
- [ ] Install Livewire (`composer require livewire/livewire`)
- [ ] Create Livewire component
- [ ] Create component view
- [ ] Add to public layout
- [ ] Test notification display

### Automated Testing:
- [ ] Create test files
- [ ] Write unit tests
- [ ] Write feature tests
- [ ] Run test suite
- [ ] Fix failing tests

---

## 🚀 **Quick Start Commands**

```bash
# Install Livewire
composer require livewire/livewire

# Create Livewire component
php artisan make:livewire LiveSalesNotification

# Create tests
php artisan make:test LicenseApiTest
php artisan make:test OrderTest

# Run tests
php artisan test

# Send test email
php artisan tinker
Mail::to('test@example.com')->send(new App\Mail\OrderCreatedMail(App\Models\Order::first()));
```

---

## 📝 **Notes**

1. **Email**: Gunakan Mailtrap untuk testing, lalu switch ke SMTP production
2. **Live Sales**: Perlu Alpine.js (sudah include di Livewire)
3. **Testing**: Gunakan SQLite in-memory untuk faster tests

Semua file dasar sudah dibuat. Tinggal copy template dan run commands! 🎉
