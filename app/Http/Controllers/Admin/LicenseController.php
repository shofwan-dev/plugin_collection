<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\License;
use App\Services\LicenseGenerator;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class LicenseController extends Controller
{
    protected $licenseGenerator;

    public function __construct(LicenseGenerator $licenseGenerator)
    {
        $this->licenseGenerator = $licenseGenerator;
    }

    public function index(Request $request): View
    {
        $query = License::with(['plan', 'order', 'user']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $query->where('license_key', 'like', '%' . $request->search . '%');
        }

        $licenses = $query->latest()->paginate(20);

        return view('admin.licenses.index', compact('licenses'));
    }

    public function show(License $license): View
    {
        $license->load(['plan', 'order', 'user']);
        
        return view('admin.licenses.show', compact('license'));
    }

    public function suspend(License $license): RedirectResponse
    {
        $license->update(['status' => 'suspended']);

        return back()->with('success', 'License suspended successfully');
    }

    public function activate(License $license): RedirectResponse
    {
        $license->update(['status' => 'active']);

        return back()->with('success', 'License activated successfully');
    }

    /**
     * Deactivate a domain from license
     */
    public function deactivateDomain(Request $request, License $license): RedirectResponse
    {
        $request->validate([
            'domain' => 'required|string',
        ]);

        if ($license->deactivateDomain($request->domain)) {
            return back()->with('success', 'Domain deactivated successfully');
        }

        return back()->with('error', 'Failed to deactivate domain');
    }
}
