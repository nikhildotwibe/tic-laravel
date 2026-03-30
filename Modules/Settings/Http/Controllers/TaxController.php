<?php

namespace Modules\Settings\Http\Controllers;

use App\Http\Controllers\BaseController;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\Settings\Entities\AdditionalTax;
use Modules\Settings\Entities\TaxSetting;
use Modules\Settings\Transformers\AdditionalTaxResource;
use Modules\Settings\Transformers\TaxSettingResource;

class TaxController extends BaseController
{
    // =========================================================================
    // GST SETTINGS
    // =========================================================================

    /**
     * GET /api/tax-settings
     * Retrieve the current active GST settings together with all additional taxes.
     */
    public function index(): JsonResponse
    {
        try {
            $taxSetting     = TaxSetting::latest()->first();
            $additionalTaxes = AdditionalTax::latest()->get();

            return $this->sendResponse([
                'tax_settings'    => $taxSetting ? TaxSettingResource::make($taxSetting) : null,
                'additional_taxes' => AdditionalTaxResource::collection($additionalTaxes),
            ], 'Tax settings fetched successfully', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }

    /**
     * POST /api/tax-settings
     * Upsert the GST settings (always keeps a single active row).
     */
    public function store(Request $request): JsonResponse
    {
        try {
            Validator::make($request->all(), [
                'cgst_percentage' => 'required|numeric|min:0|max:100',
                'sgst_percentage' => 'nullable|numeric|min:0|max:100',
                'igst_percentage' => 'nullable|numeric|min:0|max:100',
                'tcs_percentage'  => 'nullable|numeric|min:0|max:100',
            ])->validate();

            // Always maintain a SINGLE tax-settings record (upsert pattern)
            $taxSetting = TaxSetting::latest()->first() ?? new TaxSetting();

            $taxSetting->cgst_percentage = $request->cgst_percentage;
            $taxSetting->sgst_percentage = $request->sgst_percentage ?? 0;
            $taxSetting->igst_percentage = $request->igst_percentage ?? 0;
            $taxSetting->tcs_percentage  = $request->tcs_percentage  ?? 0;
            $taxSetting->save();

            return $this->sendResponse(
                TaxSettingResource::make($taxSetting),
                'Tax settings saved successfully',
                201
            );
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }

    // =========================================================================
    // ADDITIONAL TAXES
    // =========================================================================

    /**
     * GET /api/additional-taxes
     * List all additional / custom taxes.
     */
    public function indexAdditional(): JsonResponse
    {
        try {
            $taxes = AdditionalTax::latest()->get();
            return $this->sendResponse(
                AdditionalTaxResource::collection($taxes),
                'Additional taxes fetched successfully',
                200
            );
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }

    /**
     * POST /api/additional-taxes
     * Create a new additional tax.
     */
    public function storeAdditional(Request $request): JsonResponse
    {
        try {
            Validator::make($request->all(), [
                'name'       => 'required|string|max:255',
                'percentage' => 'required|numeric|min:0|max:100',
            ])->validate();

            $tax = new AdditionalTax();
            $tax->name       = $request->name;
            $tax->percentage = $request->percentage;
            $tax->save();

            return $this->sendResponse(
                AdditionalTaxResource::make($tax),
                'Additional tax created successfully',
                201
            );
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }

    /**
     * GET /api/additional-taxes/{id}
     * Show a single additional tax.
     */
    public function showAdditional(string $id): JsonResponse
    {
        try {
            $tax = AdditionalTax::findOrFail($id);
            return $this->sendResponse(
                AdditionalTaxResource::make($tax),
                'Additional tax fetched successfully',
                200
            );
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }

    /**
     * PUT /api/additional-taxes/{id}
     * Update an existing additional tax.
     */
    public function updateAdditional(Request $request, string $id): JsonResponse
    {
        try {
            $tax = AdditionalTax::findOrFail($id);

            Validator::make($request->all(), [
                'name'       => 'required|string|max:255',
                'percentage' => 'required|numeric|min:0|max:100',
            ])->validate();

            $tax->name       = $request->name;
            $tax->percentage = $request->percentage;
            $tax->save();

            return $this->sendResponse(
                AdditionalTaxResource::make($tax),
                'Additional tax updated successfully',
                200
            );
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }

    /**
     * DELETE /api/additional-taxes/{id}
     * Soft-delete an additional tax.
     */
    public function destroyAdditional(string $id): JsonResponse
    {
        try {
            AdditionalTax::findOrFail($id)->delete();
            return $this->sendResponse([], 'Additional tax deleted successfully', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }
}
