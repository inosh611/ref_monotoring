<?php

namespace Modules\Dealers\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Support\Renderable;
use Modules\Dealers\Http\Requests\DealerRequest;
use Modules\Dealers\Repositories\Interfaces\DealerRepositoryInterface;

class DealersController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    protected $dealerRepository;

    // public function __construct(DealerRepositoryInterface $dealerRepository)
    // {
    //     $this->dealerRepository = $dealerRepository;
    // }
    public function dataTable(Request $request) // Remove When start Backend
    {
        // return ($this->dealerRepository->dataTable($request));
    }
    public function index()
    {
        return Inertia::render("Modules/Dealers/DealersManagement");
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        
         return Inertia::render("Modules/Dealers/CreateDealer");
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(DealerRequest $request)
    {
        dd($request->all());
        $validated = $request->validated();
        
        if ($request->hasFile('registration_doc')) {
            $regPath = $request->file('registration_doc')
                ->store('dealers/registration_docs', 'public');
            $validated['registration_doc'] = $regPath;
        }

        if ($request->hasFile('sign_application')) {
            $signPath = $request->file('sign_application')
                ->store('dealers/sign_apps', 'public');
            $validated['sign_application'] = $signPath;
        }

        try {
            $stored = $this->dealerRepository->create($validated);
            if ($stored) {
                return response()->json([
                    'success' => true,
                    'message' => 'Dealer Successfully Saved.',
                    'redirect' => route('dealer.index')
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
        return view('dealers::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('dealers::edit');
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
