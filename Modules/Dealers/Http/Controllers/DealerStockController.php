<?php

namespace Modules\Dealers\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Dealers\Repositories\Interfaces\DealerStockRepositoryInterface;

class DealerStockController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */

      protected $dealerStockRepository;
      public function __construct(DealerStockRepositoryInterface $dealer_stock_repository){
        $this->dealerStockRepository = $dealer_stock_repository;
      }
    public function index()
    {
        return view('dealers::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('dealers::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
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
    public function update(Request $request)
    {
    
        try {
            $data = json_decode($request->items);
            $results = $this->dealerStockRepository->update($data);
            return response()->json([
                'success' => true,
                'message' => 'Stock Successfully Updated.',
                'redirect' => route('stock.odit.create')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Stock Update Failed. Error: ' . $e->getMessage(),
                'redirect' => route('stock.odit.create')
            ], 500);
        }
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

    public function search(Request $request){
        $results = $this->dealerStockRepository->find($request->order_id, $request->dealer_id);
        return response()->json(['results' => $results]);
    }
}
