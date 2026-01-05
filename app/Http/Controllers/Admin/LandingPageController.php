<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LandingPage;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LandingPageController extends Controller
{
    public function index()
    {
        $landingPages = LandingPage::latest()->paginate(15);
        return view('admin.landing-pages.index', compact('landingPages'));
    }

    public function create()
    {
        $products = Product::active()->get();
        return view('admin.landing-pages.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|unique:landing_pages,slug',
            'hero_title' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string',
            'hero_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'content' => 'nullable|string',
            'is_active' => 'boolean',
            'is_homepage' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id',
            'benefits' => 'nullable|array',
            'testimonials' => 'nullable|array',
        ]);

        if ($request->hasFile('hero_image')) {
            $imagePath = $request->file('hero_image')->store('landing-pages', 'public');
            $validated['hero_image'] = $imagePath;
        }

        // Handle is_homepage singleton logic
        if ($request->has('is_homepage')) {
            LandingPage::where('is_homepage', true)->update(['is_homepage' => false]);
            $validated['is_homepage'] = true;
        }

        $landingPage = LandingPage::create($validated);

        if ($request->has('product_ids')) {
            $syncData = [];
            foreach ($request->product_ids as $index => $productId) {
                $syncData[$productId] = ['sort_order' => $index];
            }
            $landingPage->products()->sync($syncData);
        }

        return redirect()->route('admin.landing-pages.index')->with('success', 'Landing page created successfully.');
    }

    public function edit(LandingPage $landingPage)
    {
        $products = Product::active()->get();
        return view('admin.landing-pages.edit', compact('landingPage', 'products'));
    }

    public function update(Request $request, LandingPage $landingPage)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|unique:landing_pages,slug,' . $landingPage->id,
            'hero_title' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string',
            'hero_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'content' => 'nullable|string',
            'is_active' => 'boolean',
            'is_homepage' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id',
            'benefits' => 'nullable|array',
            'testimonials' => 'nullable|array',
        ]);

        if ($request->hasFile('hero_image')) {
            $imagePath = $request->file('hero_image')->store('landing-pages', 'public');
            $validated['hero_image'] = $imagePath;
        }

        $validated['is_active'] = $request->has('is_active');
        
        // Handle is_homepage singleton logic
        if ($request->has('is_homepage')) {
            LandingPage::where('id', '!=', $landingPage->id)->update(['is_homepage' => false]);
            $validated['is_homepage'] = true;
        } else {
            $validated['is_homepage'] = false;
        }

        $landingPage->update($validated);

        if ($request->has('product_ids')) {
            $syncData = [];
            foreach ($request->product_ids as $index => $productId) {
                $syncData[$productId] = ['sort_order' => $index];
            }
            $landingPage->products()->sync($syncData);
        } else {
            $landingPage->products()->detach();
        }

        return redirect()->route('admin.landing-pages.index')->with('success', 'Landing page updated successfully.');
    }

    public function destroy(LandingPage $landingPage)
    {
        $landingPage->delete();
        return redirect()->route('admin.landing-pages.index')->with('success', 'Landing page deleted successfully.');
    }
}
