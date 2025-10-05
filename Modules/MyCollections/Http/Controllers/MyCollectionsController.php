<?php

namespace Modules\MyCollections\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;

class MyCollectionsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
       return Inertia::render('Modules/MyCollection/MyCollectionManagement');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return Inertia::render('Modules/MyCollection/CreateCollection');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
       if($request->collection_type == 1){
           $validatedCheque =  $request->validate([
                'collection_type' => 'required',
                'order_number' => 'required',
                'cheque_number' => 'required',
                'bank' => 'required',
                'branch' => 'required',
                'cheque_date' => 'required',
                'cheque_amount' => 'required',
                'cheque_amount_text' => 'required',
                'cheque_type' => 'required',
                'receipt_number' => 'required',
            ]);
            
        }else{
            $validatedCash =  $request->validate([
                'collection_type' => 'required',
                'order_number' => 'required',
                'cash_amount' => 'required',
                'cash_amount_text' => 'required',
                'cash_receipt_number' => 'required',
            ]);
        }

    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('mycollections::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('mycollections::edit');
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
