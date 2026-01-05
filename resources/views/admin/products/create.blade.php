@extends('layouts.admin')

@section('title', 'Add New Product')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold">Add New Product</h1>
</div>

<div class="bg-white rounded-lg shadow p-6">
    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Name -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Product Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Type *</label>
                <select name="type" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="plugin" {{ old('type') == 'plugin' ? 'selected' : '' }}>Plugin</option>
                    <option value="website" {{ old('type') == 'website' ? 'selected' : '' }}>Website</option>
                    <option value="addon" {{ old('type') == 'addon' ? 'selected' : '' }}>Addon</option>
                </select>
                @error('type')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Version -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Version *</label>
                <input type="text" name="version" value="{{ old('version', '1.0.0') }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 @error('version') border-red-500 @enderror">
                @error('version')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Price -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Price ($) *</label>
                <input type="number" name="price" value="{{ old('price', '0.00') }}" step="0.01" required
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 @error('price') border-red-500 @enderror">
                @error('price')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Max Domains -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Max Domains * (-1 for unlimited)</label>
                <input type="number" name="max_domains" value="{{ old('max_domains', '1') }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 @error('max_domains') border-red-500 @enderror">
                @error('max_domains')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Paddle Price ID -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Paddle Price ID (Optional)</label>
                <input type="text" name="paddle_price_id" value="{{ old('paddle_price_id') }}"
                    placeholder="pri_..."
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono @error('paddle_price_id') border-red-500 @enderror">
                @error('paddle_price_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Paddle Product ID -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Paddle Product ID (Optional)</label>
                <input type="text" name="paddle_product_id" value="{{ old('paddle_product_id') }}"
                    placeholder="pro_..."
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono @error('paddle_product_id') border-red-500 @enderror">
                @error('paddle_product_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- File Upload -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Upload File * (Max 50MB)</label>
                <input type="file" name="file" required accept=".zip,.rar,.tar,.gz"
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('file')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Description -->
        <div class="mt-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
            <textarea name="description" rows="4"
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description') }}</textarea>
            @error('description')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Changelog -->
        <div class="mt-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Changelog</label>
            <textarea name="changelog" rows="4" placeholder="- New feature added&#10;- Bug fixes&#10;- Performance improvements"
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('changelog') }}</textarea>
            @error('changelog')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Requirements -->
        <div class="mt-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Requirements</label>
            <textarea name="requirements" rows="3" placeholder="WordPress 5.0+, PHP 7.4+"
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('requirements') }}</textarea>
            @error('requirements')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Active Status -->
        <div class="mt-6">
            <label class="flex items-center">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <span class="ml-2 text-sm text-gray-700">Active (available for download)</span>
            </label>
        </div>

        <!-- Buttons -->
        <div class="mt-6 flex space-x-3">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                Create Product
            </button>
            <a href="{{ route('admin.products.index') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-400">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
