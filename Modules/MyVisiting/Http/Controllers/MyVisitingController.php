<?php

namespace Modules\MyVisiting\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Support\Renderable;
use Modules\MyVisiting\Http\Requests\MyVisitingRequest;
use Modules\Dealers\Repositories\Interfaces\OwnerRepositoryInterface;
use Modules\Dealers\Repositories\Interfaces\DealerRepositoryInterface;
use Modules\MyVisiting\Http\Requests\CheckoutRequest;
use Modules\MyVisiting\Repositories\Interfaces\MyVisitingRepositoryInterface;

class MyVisitingController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */

    protected $dealerRepository;
    protected $myVisitingRepository;

    public function __construct(DealerRepositoryInterface $dealerRepository, MyVisitingRepositoryInterface $myVisitingRepository)
    {
        $this->dealerRepository = $dealerRepository;
        $this->myVisitingRepository = $myVisitingRepository;
    }

    public function index()
    {
         $dealers = $this->dealerRepository->allData();

        return Inertia::render('Modules/MyVisiting/VisitingManagement', ['dealers' => $dealers]);
    }

    public function checkIn()
    {
        $dealers = $this->dealerRepository->allData();
        return Inertia::render('Modules/MyVisiting/CheckIn', ['dealers' => $dealers]);
    }
    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('myvisiting::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(MyVisitingRequest $request)
    {
        try {
            $validated = $request->validated();
            $ref_id = Auth::user()->id;
            $validated['ref_id'] = $ref_id;
            
            if ($request->hasFile('photo_of_the_shop')) {
                $validated['photo_of_shop'] = $request->file('photo_of_the_shop')->store('my_visiting_shop', 'public');
            }
             $submit = $this->myVisitingRepository->create($validated);
            dd($submit);
        } catch (\Exception $error) {
            dd($error->getMessage());
            return response()->json(['error' => true, 'message' => $error->getMessage()]);
        }
    }

    public function checkOut(CheckoutRequest $request){
        $validated = $request->validated();
        $ref_id = Auth::user()->id;
        $updateData = [
            'checkout_time' => $validated['checkout_time'],
            'checkout_date' => $validated['checkout_date']
        ];
        $updateCheckOut = $this->myVisitingRepository->updateCheckOut($ref_id, $validated['dealer_id'], $updateData);
        dd($updateCheckOut);
    }
    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('myvisiting::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('myvisiting::edit');
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
