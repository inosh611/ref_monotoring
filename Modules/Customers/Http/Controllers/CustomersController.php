<?php

namespace Modules\Customers\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Support\Renderable;
use Modules\Customers\Http\Requests\CustomerRequest;
use Modules\Customers\Repositories\Interfaces\CustomerRepositoryInterface;

class CustomersController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    protected $customerRepository;

    public function __construct(CustomerRepositoryInterface $customer_repository)
    {
        $this->customerRepository = $customer_repository;
    }

    public function dataTable(Request $request) // Remove When start Backend
    {
        return ($this->customerRepository->dataTable($request));
    }

    public function index()
    {
        return Inertia::render("Modules/Customers/CustomerManagement");
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return Inertia::render("Modules/Customers/CustomerCreate");
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(CustomerRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('registration_doc')) {
            $regPath = $request->file('registration_doc')
                ->store('customers/registration_docs', 'public');
            $validated['registration_doc'] = $regPath;
        }

        if ($request->hasFile('sign_application')) {
            $signPath = $request->file('sign_application')
                ->store('customers/sign_apps', 'public');
            $validated['sign_application'] = $signPath;
        }

        try {
            $stored = $this->customerRepository->create($validated);
            if ($stored) {
                return response()->json([
                    'success' => true,
                    'message' => 'Customer Successfully Saved.',
                    'redirect' => route('customer.index')
                ]);
            }
        } catch (\Exception $error) {
            Log::error("Customer Store Fail" . $error->getMessage());
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('customers::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('customers::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
