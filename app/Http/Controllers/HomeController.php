<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Show homepage
     */
    public function index(): View
    {
        // Check if there is a specific Landing Page set as homepage
        $lpHomepage = \App\Models\LandingPage::where('is_homepage', true)->active()->first();
        
        if ($lpHomepage) {
            $landingPage = $lpHomepage;
            $products = $landingPage->products()->with('plans')->get();
            return view('landing-page', compact('landingPage', 'products'));
        }

        $homepage = \App\Models\HomepageSetting::current();
        
        // Use featured landing pages if set
        $landingPages = $homepage->featuredLandingPages();
        if ($landingPages->isEmpty()) {
            // Fallback to all active landing pages
            $landingPages = \App\Models\LandingPage::active()->latest()->take(3)->get();
        }

        return view('home', compact('homepage', 'landingPages'));
    }

    /**
     * Show product detail page
     */
    public function product(Product $product): View
    {
        $plans = \App\Models\Plan::where('product_id', $product->id)
                    ->active()
                    ->ordered()
                    ->get();

        return view('product', compact('product', 'plans'));
    }

    /**
     * Show documentation page
     */
    public function documentation(): View
    {
        return view('documentation');
    }

    /**
     * Show custom landing page
     */
    public function landingPage($slug): View
    {
        $landingPage = \App\Models\LandingPage::where('slug', $slug)->active()->firstOrFail();
        $products = $landingPage->products()->with('plans')->get();
        
        return view('landing-page', compact('landingPage', 'products'));
    }

    /**
     * Show contact page
     */
    public function contact(): View
    {
        return view('contact');
    }
}
