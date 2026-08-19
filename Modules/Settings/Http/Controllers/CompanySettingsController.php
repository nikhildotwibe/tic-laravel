<?php

namespace Modules\Settings\Http\Controllers;

use App\Http\Controllers\BaseController;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\Settings\Entities\Company;
use Modules\Settings\Entities\CompanyBankAccount;
use Modules\Settings\Transformers\CompanyResource;

class CompanySettingsController extends BaseController
{
    /**
     * Display a listing of the resource.
     * @return JsonResponse
     */
    public function index()
    {
        try {
            $companies = Company::with('bankAccounts')->latest()->get();
            return $this->sendResponse(CompanyResource::collection($companies), 'All Companies Fetched', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {
            Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'country' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'nullable|string|max:50',
                'tax_no' => 'nullable|string|max:100',
                'status' => 'required|string|in:active,inactive',
                'address' => 'nullable|string',
            ])->validate();

            $company = DB::transaction(function () use ($request) {
                $id = $request->input('id');
                
                $company = null;
                if ($id) {
                    $company = Company::find($id);
                }
                
                if (!$company) {
                    $company = new Company();
                    if ($id) {
                        $company->id = $id;
                    }
                }

                $company->name = $request->input('name');
                $company->country = $request->input('country');
                $company->address = $request->input('address');
                $company->email = $request->input('email');
                $company->phone = $request->input('phone');
                $company->tax_no = $request->input('tax_no');
                $company->status = $request->input('status', 'active');
                
                $isDefault = filter_var($request->input('is_default'), FILTER_VALIDATE_BOOLEAN);
                $company->is_default = $isDefault;

                // Ensure only one default company
                if ($isDefault) {
                    Company::where('is_default', true)->where('id', '!=', $company->id)->update(['is_default' => false]);
                } else {
                    // If this is the only company, make it default
                    if (Company::count() === 0) {
                        $company->is_default = true;
                    }
                }

                $company->save();

                // Handle Bank Accounts
                $bankAccountsInput = $request->input('bankAccounts');
                if (is_string($bankAccountsInput)) {
                    $bankAccountsData = json_decode($bankAccountsInput, true);
                } else {
                    $bankAccountsData = $bankAccountsInput;
                }

                if (!is_array($bankAccountsData)) {
                    $bankAccountsData = [];
                }

                $bankAccounts = array_map(function ($bank) {
                    return [
                        'id' => $bank['id'] ?? null,
                        'bank_name' => $bank['bankName'] ?? null,
                        'account_name' => $bank['accountName'] ?? null,
                        'account_number' => $bank['accountNumber'] ?? null,
                        'swift_code' => $bank['swiftCode'] ?? null,
                        'branch_name' => $bank['branchName'] ?? null,
                        'is_primary' => filter_var($bank['isPrimary'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    ];
                }, $bankAccountsData);

                $this->updateOrCreateMultiple(new CompanyBankAccount(), $bankAccounts, 'company_id', $company->id);

                // Handle Media Files
                if ($request->hasFile('logo')) {
                    $company->clearMediaCollection('logo');
                    $company->addMediaFromRequest('logo')->toMediaCollection('logo');
                } elseif (empty($request->input('logoUrl'))) {
                    $company->clearMediaCollection('logo');
                }

                if ($request->hasFile('header')) {
                    $company->clearMediaCollection('header');
                    $company->addMediaFromRequest('header')->toMediaCollection('header');
                } elseif (empty($request->input('headerUrl'))) {
                    $company->clearMediaCollection('header');
                }

                if ($request->hasFile('footer')) {
                    $company->clearMediaCollection('footer');
                    $company->addMediaFromRequest('footer')->toMediaCollection('footer');
                } elseif (empty($request->input('footerUrl'))) {
                    $company->clearMediaCollection('footer');
                }

                return $company;
            });

            // Reload relationships and return
            $company->load('bankAccounts');
            return $this->sendResponse(CompanyResource::make($company), 'Company profile saved successfully', 200);

        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {
            $company = Company::findOrFail($id);
            $wasDefault = $company->is_default;
            $company->delete();

            if ($wasDefault) {
                $firstCompany = Company::first();
                if ($firstCompany) {
                    $firstCompany->update(['is_default' => true]);
                }
            }

            return $this->sendResponse(null, 'Company profile deleted successfully', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }

    /**
     * Set company as default.
     * @param int $id
     * @return JsonResponse
     */
    public function setDefault($id)
    {
        try {
            DB::transaction(function () use ($id) {
                Company::where('is_default', true)->update(['is_default' => false]);
                $company = Company::findOrFail($id);
                $company->is_default = true;
                $company->save();
            });

            return $this->sendResponse(null, 'Default company updated successfully', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }
}
