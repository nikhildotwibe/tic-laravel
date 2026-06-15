<?php

namespace Modules\Settings\Http\Controllers;

use App\Http\Controllers\BaseController;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Settings\Entities\PackageTerm;
use Modules\Settings\Transformers\PackageTermResource;

class PackageTermsController extends BaseController
{
    /**
     * GET /api/package-terms
     * Retrieve the current package terms (single-row settings record).
     */
    public function index(): JsonResponse
    {
        try {
            $record = PackageTerm::latest()->first();

            return $this->sendResponse(
                $record ? PackageTermResource::make($record) : null,
                'Package terms fetched successfully',
                200
            );
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }

    /**
     * PUT /api/package-terms
     * Upsert the package terms (always keeps a single active record).
     * Accepts any subset of: invoice_terms, package_terms, bank_info
     */
    public function update(Request $request): JsonResponse
    {
        try {
            // Get or create the single settings record
            $record = PackageTerm::latest()->first() ?? new PackageTerm();

            if ($request->has('invoice_terms')) {
                $record->invoice_terms = $request->input('invoice_terms');
            }
            if ($request->has('package_terms')) {
                $record->package_terms = $request->input('package_terms');
            }
            if ($request->has('bank_info')) {
                $record->bank_info = $request->input('bank_info');
            }

            $record->save();

            return $this->sendResponse(
                PackageTermResource::make($record),
                'Package terms saved successfully',
                200
            );
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }
}
