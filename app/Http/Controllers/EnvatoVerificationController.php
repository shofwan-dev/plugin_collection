<?php

namespace App\Http\Controllers;

use App\Models\License;
use App\Models\Plan;
use App\Services\EnvatoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class EnvatoVerificationController extends Controller
{
    protected $envatoService;

    public function __construct(EnvatoService $envatoService)
    {
        $this->middleware('auth');
        $this->envatoService = $envatoService;
    }

    /**
     * Show Envato verification form
     */
    public function show(): View
    {
        return view('envato.verify');
    }

    /**
     * Verify Envato purchase code and generate license
     */
    public function verify(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'purchase_code' => 'required|string|size:36', // Envato purchase codes are 36 chars
            'plan_id' => 'required|exists:plans,id',
        ]);

        $purchaseCode = $validated['purchase_code'];
        $planId = $validated['plan_id'];

        // Check if purchase code already used
        if ($this->envatoService->isPurchaseCodeUsed($purchaseCode)) {
            return back()->with('error', 'This purchase code has already been used to generate a license.');
        }

        // Verify with Envato API
        $result = $this->envatoService->verifyPurchaseCode($purchaseCode);

        if (!$result['success']) {
            return back()->with('error', $result['message'] ?? 'Invalid purchase code. Please check and try again.');
        }

        // Get plan
        $plan = Plan::findOrFail($planId);

        // Generate license key
        $licenseKey = $this->generateLicenseKey();

        // Calculate expiration (1 year from now)
        $expiresAt = now()->addYear();

        // Create license
        $license = License::create([
            'license_key' => $licenseKey,
            'purchase_source' => 'envato',
            'envato_purchase_code' => $purchaseCode,
            'envato_buyer_username' => $result['buyer'] ?? null,
            'envato_verified_at' => now(),
            'envato_purchase_data' => json_encode($result['data'] ?? []),
            'plan_id' => $plan->id,
            'user_id' => Auth::id(),
            'status' => 'active',
            'max_domains' => $plan->max_domains,
            'activated_domains' => [],
            'expires_at' => $expiresAt,
        ]);

        // Send notification (optional)
        try {
            $whatsapp = app(\App\Services\WhatsAppService::class);
            $whatsapp->sendMessage(
                Auth::user()->email,
                "*License Generated Successfully!* 🎉\n\n" .
                "Your Envato purchase has been verified.\n\n" .
                "📋 *License Key:*\n`{$license->license_key}`\n\n" .
                "🎯 *Plan:* {$plan->name}\n" .
                "📅 *Expires:* {$expiresAt->format('d M Y')}\n\n" .
                "You can now activate this license on your domain."
            );
        } catch (\Exception $e) {
            \Log::error('Failed to send WhatsApp notification for Envato license', [
                'license_id' => $license->id,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()->route('customer.licenses.show', $license)
            ->with('success', 'License generated successfully! Your Envato purchase has been verified.');
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
}
