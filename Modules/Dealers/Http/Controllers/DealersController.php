<?php

namespace Modules\Dealers\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Support\Renderable;
use Modules\Dealers\Http\Requests\DealerRequest;
use Modules\Dealers\Repositories\DealerRepository;
use Modules\Dealers\Repositories\Interfaces\OwnerRepositoryInterface;
use Modules\Dealers\Repositories\Interfaces\DealerRepositoryInterface;

class DealersController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    protected $ownerRepository;
    protected $dealerRepository;

    public function __construct(OwnerRepositoryInterface $ownerRepository, DealerRepositoryInterface $dealerRepository)
    {
        $this->dealerRepository = $dealerRepository;
        $this->ownerRepository = $ownerRepository;
    }
    public function dataTable(Request $request) // Remove When start Backend
    {

        return ($this->dealerRepository->dataTable($request));
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

        $validated = $request->validated();
        try {
            DB::beginTransaction();
            $nicCopyPath = null;
            if ($validated['nic_copy']) {
                $nicCopy = $validated['nic_copy'];
                $nicCopyPath = $nicCopy->store('nic_copy', 'public');
                $validated['nic_copy'] = $nicCopyPath;
            };
            $ownerDetails = [
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'nic' => $validated['nic'],
                'contact_number' => $validated['contact_number'],
                'address' => $validated['address'],
                'email' => $validated['email'],
                'owner_position' => $validated['owner_position'],
                'nic_copy' => $validated['nic_copy']
            ];

            $owner = $this->ownerRepository->create($ownerDetails);

            if (!$owner) {
                throw  new \Exception("Owner save failed!");
            }
            $registrationDocPath = null;
            $signApplicationPath = null;
            $photoOfShopPath = null;

            if ($request->hasFile('registration_doc')) {
                $validated['registration_doc'] = $request->file('registration_doc')->store('registration_doc', 'public');
            }

            if ($request->hasFile('sign_application')) {
                $validated['sign_application'] = $request->file('sign_application')->store('sign_application', 'public');
            }

            if ($request->hasFile('nic_copy')) {
                $validated['nic_copy'] = $request->file('nic_copy')->store('nic_copy', 'public');
            }

            if ($request->hasFile('photo_of_the_shop')) {
                $validated['photo_of_the_shop'] = $request->file('photo_of_the_shop')->store('photo_of_the_shop', 'public');
            }

            $dealerDetails = [
                'owner_id' => $owner->id,
                'business_name' => $validated['business_name'],
                'business_address' => $validated['business_address'],
                'business_tel' => $validated['business_tel'],
                'registration_doc' => $validated['registration_doc'],
                'sign_application' => $validated['sign_application'],
                'photo_of_the_shop' => $validated['photo_of_the_shop'],
            ];
            $dealer  = $this->dealerRepository->create($dealerDetails);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Dealer Successfully Saved.',
                'redirect' => route('dealer.index')
            ]);
        } catch (\Exception $error) {
            DB::rollBack();
            if (!empty($nicCopyPath)) {
                Storage::disk('public')->delete($nicCopyPath);
            }
            if (!empty($registrationDocPath)) {
                Storage::disk('public')->delete($registrationDocPath);
            }
            if (!empty($signApplicationPath)) {
                Storage::disk('public')->delete($signApplicationPath);
            }
            if (!empty($photoOfShopPath)) {
                Storage::disk('public')->delete($photoOfShopPath);
            }
            return response()->json(['error' => true, 'message' => $error->getMessage()]);
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        $dealerDetails  = $this->dealerRepository->find($id);
        return Inertia::render('Modules/Dealers/ViewDealers',[
            'dealerDetails' => $dealerDetails
        ]);
    }

    public function all(){
        return $this->dealerRepository->allData();
    }

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
