<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of products
     */
    public function index(): View
    {
        $products = Product::latest()->paginate(15);
        
        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new product
     */
    public function create(): View
    {
        return view('admin.products.create');
    }

    /**
     * Store a newly created product
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'version' => 'required|string|max:50',
            'type' => 'required|in:plugin,website,addon',
            'price' => 'required|numeric|min:0',
            'max_domains' => 'required|integer|min:-1',
            'file' => 'required|file|mimes:zip,rar,tar,gz|max:51200',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'changelog' => 'nullable|string',
            'requirements' => 'nullable|string',
            'benefits' => 'nullable|array',
            'testimonials' => 'nullable|array',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'paddle_price_id' => 'nullable|string|max:100',
            'paddle_product_id' => 'nullable|string|max:100',
        ]);

        // Generate slug
        $validated['slug'] = Str::slug($validated['name']);

        // Handle file upload
        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            try {
                // Ensure products directory exists
                $productsPath = storage_path('app/public/products');
                if (!file_exists($productsPath)) {
                    mkdir($productsPath, 0755, true);
                }

                $file = $request->file('file');
                
                // Get file info BEFORE moving (temp file will be gone after move)
                $originalName = $file->getClientOriginalName();
                $fileSize = $file->getSize();
                
                $filename = time() . '_' . Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
                $destinationPath = $productsPath . DIRECTORY_SEPARATOR . $filename;
                
                \Log::info('Attempting to upload file', [
                    'filename' => $filename,
                    'original' => $originalName,
                    'size' => $fileSize,
                    'destination' => $destinationPath,
                ]);

                // Use native PHP move_uploaded_file
                if (move_uploaded_file($file->getPathname(), $destinationPath)) {
                    \Log::info('File uploaded successfully', ['path' => $destinationPath]);
                    $validated['file_path'] = 'products/' . $filename;
                    $validated['file_name'] = $originalName;
                    $validated['file_size'] = $fileSize;
                } else {
                    \Log::error('File upload failed - move_uploaded_file returned false');
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['file' => 'Failed to upload file. Please check folder permissions.']);
                }
            } catch (\Exception $e) {
                \Log::error('Product file upload error', [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]);
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['file' => 'Error uploading file: ' . $e->getMessage()]);
            }
        }

        // Handle image upload
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            try {
                // Ensure images directory exists
                $imagesPath = storage_path('app/public/products/images');
                if (!file_exists($imagesPath)) {
                    mkdir($imagesPath, 0755, true);
                }

                $image = $request->file('image');
                $imageName = time() . '_' . Str::slug(pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $image->getClientOriginalExtension();
                $imageDestination = $imagesPath . DIRECTORY_SEPARATOR . $imageName;

                if (move_uploaded_file($image->getPathname(), $imageDestination)) {
                    $validated['image'] = 'products/images/' . $imageName;
                    \Log::info('Product image uploaded', ['path' => $validated['image']]);
                }
            } catch (\Exception $e) {
                \Log::error('Product image upload error: ' . $e->getMessage());
            }
        }

        $validated['is_active'] = $request->has('is_active');

        Product::create($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully');
    }

    /**
     * Show the form for editing the specified product
     */
    public function edit(Product $product): View
    {
        return view('admin.products.edit', compact('product'));
    }

    /**
     * Update the specified product
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'version' => 'required|string|max:50',
            'type' => 'required|in:plugin,website,addon',
            'price' => 'required|numeric|min:0',
            'max_domains' => 'required|integer|min:-1',
            'file' => 'nullable|file|mimes:zip,rar,tar,gz|max:51200',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'changelog' => 'nullable|string',
            'requirements' => 'nullable|string',
            'benefits' => 'nullable|array',
            'testimonials' => 'nullable|array',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'paddle_price_id' => 'nullable|string|max:100',
            'paddle_product_id' => 'nullable|string|max:100',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        // Handle file upload
        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            try {
                // Ensure products directory exists
                $productsPath = storage_path('app/public/products');
                if (!file_exists($productsPath)) {
                    mkdir($productsPath, 0755, true);
                }

                // Delete old file if exists and path is not empty
                if ($product->file_path && !empty($product->file_path)) {
                    $oldFilePath = storage_path('app/public/' . $product->file_path);
                    if (file_exists($oldFilePath)) {
                        try {
                            unlink($oldFilePath);
                            \Log::info('Deleted old file', ['path' => $oldFilePath]);
                        } catch (\Exception $e) {
                            \Log::warning('Could not delete old product file: ' . $e->getMessage());
                        }
                    }
                }

                $file = $request->file('file');
                
                // Get file info BEFORE moving (temp file will be gone after move)
                $originalName = $file->getClientOriginalName();
                $fileSize = $file->getSize();
                
                $filename = time() . '_' . Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
                $destinationPath = $productsPath . DIRECTORY_SEPARATOR . $filename;
                
                \Log::info('Attempting to update file', [
                    'product_id' => $product->id,
                    'filename' => $filename,
                    'destination' => $destinationPath,
                ]);

                // Use native PHP move_uploaded_file
                if (move_uploaded_file($file->getPathname(), $destinationPath)) {
                    \Log::info('File updated successfully', ['path' => $destinationPath]);
                    $validated['file_path'] = 'products/' . $filename;
                    $validated['file_name'] = $originalName;
                    $validated['file_size'] = $fileSize;
                } else {
                    \Log::error('File update failed - move_uploaded_file returned false');
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['file' => 'Failed to upload file. Please check folder permissions.']);
                }
            } catch (\Exception $e) {
                \Log::error('Product file update error', [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]);
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['file' => 'Error uploading file: ' . $e->getMessage()]);
            }
        }

        // Handle image upload
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            try {
                // Ensure images directory exists
                $imagesPath = storage_path('app/public/products/images');
                if (!file_exists($imagesPath)) {
                    mkdir($imagesPath, 0755, true);
                }

                // Delete old image if exists
                if ($product->image && !empty($product->image)) {
                    $oldImagePath = storage_path('app/public/' . $product->image);
                    if (file_exists($oldImagePath)) {
                        try {
                            unlink($oldImagePath);
                            \Log::info('Deleted old product image', ['path' => $oldImagePath]);
                        } catch (\Exception $e) {
                            \Log::warning('Could not delete old product image: ' . $e->getMessage());
                        }
                    }
                }

                $image = $request->file('image');
                $imageName = time() . '_' . Str::slug(pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $image->getClientOriginalExtension();
                $imageDestination = $imagesPath . DIRECTORY_SEPARATOR . $imageName;

                if (move_uploaded_file($image->getPathname(), $imageDestination)) {
                    $validated['image'] = 'products/images/' . $imageName;
                    \Log::info('Product image updated', ['path' => $validated['image']]);
                }
            } catch (\Exception $e) {
                \Log::error('Product image update error: ' . $e->getMessage());
            }
        }

        $validated['is_active'] = $request->has('is_active');

        $product->update($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully');
    }

    /**
     * Remove the specified product
     */
    public function destroy(Product $product): RedirectResponse
    {
        // Delete file only if path exists and is not empty
        if ($product->file_path && !empty($product->file_path)) {
            $filePath = storage_path('app/public/' . $product->file_path);
            if (file_exists($filePath)) {
                try {
                    unlink($filePath);
                    \Log::info('Deleted product file', ['path' => $filePath]);
                } catch (\Exception $e) {
                    \Log::error('Product file deletion error: ' . $e->getMessage());
                }
            }
        }

        // Delete image if exists
        if ($product->image && !empty($product->image)) {
            $imagePath = storage_path('app/public/' . $product->image);
            if (file_exists($imagePath)) {
                try {
                    unlink($imagePath);
                    \Log::info('Deleted product image', ['path' => $imagePath]);
                } catch (\Exception $e) {
                    \Log::error('Product image deletion error: ' . $e->getMessage());
                }
            }
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully');
    }

    /**
     * Download product file
     */
    public function download(Product $product)
    {
        if (!$product->file_path || empty($product->file_path)) {
            abort(404, 'File path not found');
        }

        if (!Storage::disk('public')->exists($product->file_path)) {
            abort(404, 'File not found on server');
        }

        return Storage::disk('public')->download($product->file_path, $product->file_name);
    }
}
