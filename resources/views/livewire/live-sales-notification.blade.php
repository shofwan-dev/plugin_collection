<div>
    @if($showNotification && $currentOrder)
    <div 
        x-data="{ show: @entangle('showNotification') }"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-y-4 sm:translate-y-0 sm:translate-x-4"
        x-transition:enter-end="opacity-100 transform translate-y-0 sm:translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0 sm:translate-x-0"
        x-transition:leave-end="opacity-0 transform translate-y-4 sm:translate-y-0 sm:translate-x-4"
        @notification-shown.window="setTimeout(() => $wire.hideNotification(), 5000)"
        class="fixed bottom-4 left-4 z-50 max-w-sm w-full bg-white rounded-lg shadow-xl border border-gray-200 overflow-hidden"
    >
        <div class="p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                        <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium text-gray-900">
                        🎉 Someone just purchased!
                    </p>
                    <p class="mt-1 text-sm text-gray-600">
                        <strong>{{ $currentOrder->plan->name }}</strong>
                    </p>
                    <p class="mt-1 text-sm text-gray-500">
                        ${{ number_format($currentOrder->amount, 2) }} • {{ $currentOrder->created_at->diffForHumans() }}
                    </p>
                </div>
                <button 
                    wire:click="hideNotification"
                    class="ml-4 flex-shrink-0 text-gray-400 hover:text-gray-500 focus:outline-none"
                >
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
        <div class="bg-gradient-to-r from-green-500 to-emerald-500 h-1"></div>
    </div>
    @endif

    <script>
        document.addEventListener('livewire:init', () => {
            // Show notification every 20 seconds
            setInterval(() => {
                if (window.Livewire && @this) {
                    @this.call('showNextNotification').then(() => {
                        window.dispatchEvent(new CustomEvent('notification-shown'));
                    });
                }
            }, 20000);

            // Show first notification after 3 seconds
            setTimeout(() => {
                if (window.Livewire && @this) {
                    @this.call('showNextNotification').then(() => {
                        window.dispatchEvent(new CustomEvent('notification-shown'));
                    });
                }
            }, 3000);
        });
    </script>
</div>
