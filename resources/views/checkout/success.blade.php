@extends('layouts.public')

@section('title', 'Thank You - Purchase Complete')

@section('content')
<section class="py-20">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h1 class="text-4xl font-bold mb-4">Thank You for Your Purchase!</h1>
            <p class="text-xl text-gray-600">Your order has been successfully processed</p>
        </div>

        @if($order && $order->license)
        <div class="card mb-8">
            <h2 class="text-2xl font-semibold mb-4">Your License Key</h2>
            <div class="bg-gray-50 rounded-lg p-6 mb-4">
                <div class="flex items-center justify-between">
                    <code class="text-2xl font-mono font-bold text-primary-600">{{ $order->license->license_key }}</code>
                    <button onclick="copyLicense()" class="btn btn-secondary">
                        <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        Copy
                    </button>
                </div>
            </div>
            <p class="text-gray-600">
                <strong>Important:</strong> Save this license key in a safe place. You'll need it to activate the plugin on your website.
            </p>
        </div>
        @endif

        <div class="card mb-8">
            <h2 class="text-2xl font-semibold mb-4">Next Steps</h2>
            <ol class="space-y-4">
                <li class="flex items-start">
                    <span class="flex-shrink-0 w-8 h-8 bg-primary-600 text-white rounded-full flex items-center justify-center mr-3 font-semibold">1</span>
                    <div>
                        <h3 class="font-semibold mb-1">Download the Plugin</h3>
                        <p class="text-gray-600 mb-2">Download the latest version of CF7 to WhatsApp plugin</p>
                        <a href="#" class="btn btn-primary">
                            <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Download Plugin
                        </a>
                    </div>
                </li>
                <li class="flex items-start">
                    <span class="flex-shrink-0 w-8 h-8 bg-primary-600 text-white rounded-full flex items-center justify-center mr-3 font-semibold">2</span>
                    <div>
                        <h3 class="font-semibold mb-1">Install & Activate</h3>
                        <p class="text-gray-600">Upload the plugin to your WordPress site and activate it</p>
                    </div>
                </li>
                <li class="flex items-start">
                    <span class="flex-shrink-0 w-8 h-8 bg-primary-600 text-white rounded-full flex items-center justify-center mr-3 font-semibold">3</span>
                    <div>
                        <h3 class="font-semibold mb-1">Enter License Key</h3>
                        <p class="text-gray-600">Go to CF7 to WhatsApp → License and enter your license key</p>
                    </div>
                </li>
                <li class="flex items-start">
                    <span class="flex-shrink-0 w-8 h-8 bg-primary-600 text-white rounded-full flex items-center justify-center mr-3 font-semibold">4</span>
                    <div>
                        <h3 class="font-semibold mb-1">Configure Settings</h3>
                        <p class="text-gray-600">Set up your WhatsApp API credentials and message templates</p>
                    </div>
                </li>
            </ol>
        </div>

        <div class="card bg-blue-50 border border-blue-200">
            <div class="flex items-start">
                <svg class="w-6 h-6 text-blue-600 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <h3 class="font-semibold text-blue-900 mb-2">Email Confirmation Sent</h3>
                    <p class="text-blue-800">
                        We've sent a confirmation email to <strong>{{ $order->customer_email ?? 'your email' }}</strong> with your license key and download link.
                    </p>
                </div>
            </div>
        </div>

        <div class="text-center mt-8">
            <a href="{{ route('documentation') }}" class="text-primary-600 hover:text-primary-700 font-semibold">
                View Documentation →
            </a>
        </div>
    </div>
</section>

@push('scripts')
<script>
function copyLicense() {
    const licenseKey = '{{ $order->license->license_key ?? '' }}';
    navigator.clipboard.writeText(licenseKey).then(() => {
        alert('License key copied to clipboard!');
    });
}
</script>
@endpush
@endsection
