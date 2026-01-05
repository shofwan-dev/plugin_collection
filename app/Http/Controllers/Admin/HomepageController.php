<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageSetting;
use App\Models\LandingPage;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class HomepageController extends Controller
{
    public function edit(): View
    {
        $homepage = HomepageSetting::current();
        $landingPages = LandingPage::orderBy('is_active', 'desc')->orderBy('title')->get();
        return view('admin.homepage.edit', compact('homepage', 'landingPages'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'hero_title' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string',
            'hero_cta_text' => 'nullable|string|max:100',
            'hero_cta_link' => 'nullable|string|max:255',
            'hero_secondary_cta_text' => 'nullable|string|max:100',
            'hero_secondary_cta_link' => 'nullable|string|max:255',
            'stats_products_label' => 'nullable|string|max:255',
            'stats_downloads_label' => 'nullable|string|max:255',
            'stats_uptime_label' => 'nullable|string|max:255',
            'stats_support_label' => 'nullable|string|max:255',
            'features_title' => 'nullable|string|max:255',
            'features_subtitle' => 'nullable|string',
            'features_items' => 'nullable|array',
            'products_title' => 'nullable|string|max:255',
            'products_subtitle' => 'nullable|string',
            'cta_title' => 'nullable|string|max:255',
            'cta_subtitle' => 'nullable|string',
            'cta_primary_text' => 'nullable|string|max:100',
            'cta_primary_link' => 'nullable|string|max:255',
            'cta_secondary_text' => 'nullable|string|max:100',
            'cta_secondary_link' => 'nullable|string|max:255',
            'featured_landing_page_ids' => 'nullable|array',
            'featured_landing_page_ids.*' => 'exists:landing_pages,id',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
        ]);

        $homepage = HomepageSetting::current();
        $homepage->update($validated);

        return redirect()->route('admin.homepage.edit')->with('success', 'Homepage settings synchronized and updated!');
    }
}
