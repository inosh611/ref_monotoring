<?php

namespace Modules\Product\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Contracts\Support\Renderable;
use Modules\Product\Http\Requests\Unitrequest;
use Modules\Product\Repositories\Interfaces\UnitRepositoryInterface;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */

    protected $unitRepository;
    public function __construct(UnitRepositoryInterface $unitRepository){
            $this->unitRepository = $unitRepository;
    }

    public function dataTable(Request $request) // Remove When start Backend
    {
        return ($this->unitRepository->dataTable($request));
    }

    public function index()
    {
        return Inertia::render('Modules/Product/UnitManagement');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
       return Inertia::render('Modules/Product/CreateUnit');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Unitrequest $request)
    {
        $validated = $request->validated();
        try {
            $unit = $this->unitRepository->create($validated);
            return response()->json([
                'success' => true,
                'message' => 'Unit Successfully Created.',
                'redirect' => route('unit.index')]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create unit: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('product::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('product::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Unitrequest $request)
    {
        $validated = $request->validated();
        try {
            $unit = $this->unitRepository->update($request->id, $validated);
            return response()->json([
                'success' => true,
                'message' => 'Unit Successfully Updated.',
                'redirect' => route('unit.index')]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update unit: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(Request $request)
    {
         try {
            $unit = $this->unitRepository->delete($request->id);
            return response()->json([
                'success' => true,
                'message' => 'Unit Successfully Updated.',
                'redirect' => route('unit.index')]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update unit: ' . $e->getMessage())->withInput();
        }
    }
}
