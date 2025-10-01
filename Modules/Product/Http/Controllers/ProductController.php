<?php

namespace Modules\Product\Http\Controllers;

use Log;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Support\Renderable;
use Modules\Product\Http\Requests\ProductRequest;
use Modules\Product\Repositories\Interfaces\UnitRepositoryInterface;
use Modules\Product\Repositories\Interfaces\ProductRepositoryInterface;
use Modules\Product\Repositories\ProductPriceRepository;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    protected $unitRepository;
    protected $productRepository;
    protected $productPriceRepository;

    public function __construct(UnitRepositoryInterface $unitRepository, ProductRepositoryInterface $productRepository, ProductPriceRepository $productPriceRepository){
            $this->unitRepository = $unitRepository;
            $this->productRepository = $productRepository;
            $this->productPriceRepository = $productPriceRepository;
    }

     public function dataTable(Request $request) // Remove When start Backend
    {
        return ($this->productRepository->dataTable($request));
    }

    public function index()
    {
        $units = $this->unitRepository->allData();
        return Inertia::render('Modules/Product/ProductManagement', ['units' => $units]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        
        return view('product::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(ProductRequest $request)
    {
        $validated = $request->validated();
       
        try {
             DB::beginTransaction();
             $product = $this->productRepository->create($validated);
             if($product){
                $priceData = [
                    'product_id' => $product->id,
                    'price' => $validated['price'],
                ];
                $this->productPriceRepository->create($priceData);
             }
             DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Product Successfully Saved.',
                'redirect' => route('product.index')]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update unit: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update unit: ' . $e->getMessage())->withInput();
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
    public function update(ProductRequest $request)
    {
       
        $validated = $request->validated();
        try {
             DB::beginTransaction();
             $product = $this->productRepository->update($validated['id'],  $validated,);
             if($product){
                $priceData = [
                    'product_id' => $product->id,
                    'price' => $validated['price'],
                ];
                $this->productPriceRepository->update($request->input('product_price_id'), $priceData);
             }
             DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Product Successfully Updated.',
                'redirect' => route('product.index')]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update unit: ' . $e->getMessage());
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
            $this->productRepository->delete($request->id);
            return response()->json([
                'success' => true,
                'message' => 'Product Successfully Deleted.',
                'redirect' => route('product.index')]);
        } catch (\Exception $e) {
            Log::error('Failed to delete product: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete product: ' . $e->getMessage());
        }
    }

    
public function search(Request $request)
{
    $search = (string) $request->query('search', '');
    $results = $this->productRepository->search($search);

    return response()->json(['results' => $results]);
}
}
