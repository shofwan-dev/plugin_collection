<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\LicenseGenerator;
use App\Models\License;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class LicenseController extends Controller
{
    protected $licenseGenerator;

    public function __construct(LicenseGenerator $licenseGenerator)
    {
        $this->licenseGenerator = $licenseGenerator;
    }

    /**
     * Activate license for domain
     * POST /api/v1/licenses/activate
     */
    public function activate(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'license_key' => 'required|string',
                'domain' => 'required|string',
                'plugin_version' => 'nullable|string',
            ]);

            $domain = $this->normalizeDomain($validated['domain']);
            $ip = $request->ip();

            \Log::info('License activation attempt', [
                'license_key' => $validated['license_key'],
                'domain' => $domain,
                'ip' => $ip,
                'plugin_version' => $validated['plugin_version'] ?? 'unknown',
            ]);

            $result = $this->licenseGenerator->activate(
                $validated['license_key'],
                $domain,
                $ip
            );

            return response()->json($result, $result['success'] ? 200 : 400);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e);
        } catch (\Exception $e) {
            return $this->errorResponse('An error occurred during activation', $e);
        }
    }

    /**
     * Validate license
     * POST /api/v1/licenses/validate
     */
    public function validate(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'license_key' => 'required|string',
                'domain' => 'required|string',
            ]);

            $domain = $this->normalizeDomain($validated['domain']);

            \Log::info('License validation attempt', [
                'license_key' => $validated['license_key'],
                'domain' => $domain,
                'ip' => $request->ip(),
            ]);

            $result = $this->licenseGenerator->validate(
                $validated['license_key'],
                $domain
            );

            return response()->json($result, $result['valid'] ? 200 : 400);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e);
        } catch (\Exception $e) {
            return $this->errorResponse('An error occurred during validation', $e);
        }
    }

    /**
     * Deactivate license from domain
     * POST /api/v1/licenses/deactivate
     */
    public function deactivate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'license_key' => 'required|string',
            'domain' => 'required|string',
        ]);

        $domain = $this->normalizeDomain($validated['domain']);

        $result = $this->licenseGenerator->deactivate(
            $validated['license_key'],
            $domain
        );

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Check license status
     * GET /api/v1/licenses/check/{license_key}
     */
    public function check(string $licenseKey): JsonResponse
    {
        $license = \App\Models\License::where('license_key', $licenseKey)->first();

        if (!$license) {
            return response()->json([
                'status' => 'not_found',
                'message' => 'License not found',
            ], 404);
        }

        return response()->json([
            'status' => $license->status,
            'plan' => $license->plan->name,
            'max_domains' => $license->max_domains,
            'activated_domains' => count($license->activated_domains ?? []),
            'remaining_activations' => $license->remaining_activations,
            'expires_at' => $license->expires_at?->toDateString(),
            'is_active' => $license->isActive(),
        ]);
    }

    /**
     * Normalize domain (remove www, protocol, etc)
     */
    private function normalizeDomain(string $domain): string
    {
        // Remove protocol
        $domain = preg_replace('#^https?://#', '', $domain);
        
        // Remove www
        $domain = preg_replace('/^www\./', '', $domain);
        
        // Remove trailing slash and path
        $domain = parse_url('http://' . $domain, PHP_URL_HOST);
        
        return strtolower($domain);
    }

    /**
     * Return validation error response
     */
    private function validationErrorResponse(\Illuminate\Validation\ValidationException $e): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'License key not found or domain not activated'
        ], 422);
    }

    /**
     * Check for plugin updates
     */
    public function checkUpdate(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'license_key' => 'required|string',
                'current_version' => 'required|string',
                'product_slug' => 'required|string',
            ]);

            // Verify license is valid
            $license = License::where('license_key', $validated['license_key'])
                ->where('status', 'active')
                ->first();

            if (!$license) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid license key'
                ], 422);
            }

            // Find product by slug
            $product = Product::where('slug', $validated['product_slug'])
                ->where('is_active', true)
                ->first();

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }

            // Compare versions
            $updateAvailable = version_compare($product->version, $validated['current_version'], '>');

            return response()->json([
                'success' => true,
                'update_available' => $updateAvailable,
                'latest_version' => $product->version,
                'download_url' => $updateAvailable ? route('api.license.download', ['license_key' => $validated['license_key']]) : null,
                'changelog' => $product->changelog,
                'requires' => $product->requirements,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e); // Pass message and exception
        }
    }

    /**
     * Download plugin file
     */
    public function download(Request $request)
    {
        try {
            $licenseKey = $request->input('license_key') ?? $request->query('license_key');

            if (!$licenseKey) {
                return response()->json([
                    'success' => false,
                    'message' => 'License key is required'
                ], 422);
            }

            // Verify license
            $license = License::where('license_key', $licenseKey)
                ->where('status', 'active')
                ->with('plan')
                ->first();

            if (!$license) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or inactive license'
                ], 422);
            }

            // Get product (assume first active product for now)
            $product = Product::where('is_active', true)->first();

            if (!$product || !$product->file_path) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product file not found'
                ], 404);
            }

            $filePath = storage_path('app/public/' . $product->file_path);

            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found on server'
                ], 404);
            }

            // Log download
            \Log::info('Plugin downloaded', [
                'license_key' => $licenseKey,
                'product' => $product->name,
                'version' => $product->version,
            ]);

            return response()->download($filePath, $product->file_name);

        } catch (\Exception $e) {
            \Log::error('Download error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Download failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Return generic error response
     */
    private function errorResponse(string $message, \Exception $e): JsonResponse
    {
        \Log::error('API Error: ' . $message, [
            'exception' => get_class($e),
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return response()->json([
            'success' => false,
            'message' => $message,
            'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
        ], 500);
    }
}
