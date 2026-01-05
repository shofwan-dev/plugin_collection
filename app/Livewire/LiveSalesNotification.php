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
            ->with('plan')
            ->latest()
            ->take(10)
            ->get();
    }

    public function showNextNotification()
    {
        if ($this->recentOrders->isNotEmpty()) {
            $this->currentOrder = $this->recentOrders->random();
            $this->showNotification = true;
        }
    }

    public function hideNotification()
    {
        $this->showNotification = false;
        $this->currentOrder = null;
    }

    public function render()
    {
        return view('livewire.live-sales-notification');
    }
}
