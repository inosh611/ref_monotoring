<?php

namespace Modules\Product\Http\Controllers;

use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Product\Entities\ProductPrice;
use Modules\Product\Http\Requests\ProductPriceRequest;
use Modules\Product\Repositories\Interfaces\ProductPriceRepositoryInterface;

class ProductPriceController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    protected $productPriceRepository;

    public function __construct(ProductPriceRepositoryInterface $productPriceRepository)
    {
        $this->productPriceRepository = $productPriceRepository;
    }
    public function index()
    {
        return view('product::index');
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
    public function store(ProductPriceRequest $request)
    {
        $validated = $request->validated();
        try{
            $this->productPriceRepository->create($validated);
            return response()->json([
                'success' => true,
                'message' => 'Product Successfully Updated.',
                'redirect' => route('product.index')]);
        }catch(\Exception $e){
            Log::error('Failed to Save New Price: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to save new price ' . $e->getMessage())->withInput();
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
       
        
    }
}
