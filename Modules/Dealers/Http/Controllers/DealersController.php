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
                'lat' => $validated['lat'],
                'lng' => $validated['lng']
            ];
            $dealer  = $this->dealerRepository->create($dealerDetails);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Dealer Successfully Saved.',
                'redirect' => route('dealer.index')
            ]);
        } catch (\Exception $error) {
            dd($error);
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
    public function dealerAll(){
         $dealers = $this->dealerRepository->allData();
            return response()->json([
                'data' => $dealers,
            ]);
    }

    public function edit($id)
    {
       $dealerDetails = $this->dealerRepository->find($id);
         return Inertia::render('Modules/Dealers/EditDealer',[
            'dealerDetails' => $dealerDetails
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
   public function update(Request $request)
{
    // Expecting dealer_id and (optionally) the current_* hidden inputs
    $dealerId = $request->input('dealer_id');
    if (!$dealerId) {
        return response()->json(['error' => true, 'message' => 'dealer_id is required'], 422);
    }

    // Current stored paths sent from the frontend (or empty "")
    $currentNicCopy         = $request->input('current_nic_copy');           // OWNER
    $currentRegistrationDoc = $request->input('current_registration_doc');   // DEALER
    $currentSignApplication = $request->input('current_sign_application');   // DEALER
    $currentPhotoOfShop     = $request->input('current_photo_of_the_shop');  // DEALER

    // Fetch existing
    $dealer = $this->dealerRepository->find($dealerId);
    if (!$dealer) {
        return response()->json(['error' => true, 'message' => 'Dealer not found'], 404);
    }

    DB::beginTransaction();
    try {
        // ---- files ----
        $nicCopyPath = $this->storeOrKeep($request, 'nic_copy', 'nic_copy', $currentNicCopy);

        $registrationDocPath = $this->storeOrKeep($request, 'registration_doc', 'registration_doc', $currentRegistrationDoc);
        $signApplicationPath = $this->storeOrKeep($request, 'sign_application', 'sign_application', $currentSignApplication);
        $photoOfShopPath     = $this->storeOrKeep($request, 'photo_of_the_shop', 'photo_of_the_shop', $currentPhotoOfShop);

        // ---- payloads (use provided input if set; otherwise keep existing) ----
        $ownerData = [
            'first_name'     => $request->input('first_name',     $dealer->owner->first_name),
            'last_name'      => $request->input('last_name',      $dealer->owner->last_name),
            'nic'            => $request->input('nic',            $dealer->owner->nic),
            'contact_number' => $request->input('contact_number', $dealer->owner->contact_number),
            'address'        => $request->input('address',        $dealer->owner->address),
            'email'          => $request->input('email',          $dealer->owner->email),
            'owner_position' => $request->input('owner_position', $dealer->owner->owner_position),
            'nic_copy'       => $nicCopyPath,
        ];

        $dealerData = [
            'business_name'     => $request->input('business_name',    $dealer->business_name),
            'business_address'  => $request->input('business_address', $dealer->business_address),
            'business_tel'      => $request->input('business_tel',     $dealer->business_tel),
            'registration_doc'  => $registrationDocPath,
            'sign_application'  => $signApplicationPath,
            'photo_of_the_shop' => $photoOfShopPath,
            'lat'               => $request->input('lat', $dealer->lat),
            'lng'               => $request->input('lng', $dealer->lng),
        ];

        // ---- persist ----
        $this->ownerRepository->update($dealer->owner_id, $ownerData);
        $this->dealerRepository->update($dealer->id, $dealerData);

        DB::commit();

        return response()->json([
            'success'  => true,
            'message'  => 'Dealer successfully updated.',
            'redirect' => route('dealer.index'),
        ]);
    } catch (\Throwable $e) {
        DB::rollBack();
        return response()->json(['error' => true, 'message' => $e->getMessage()], 500);
    }
}

     private function storeOrKeep(Request $request, string $key, string $dir, ?string $currentPath): ?string
    {
        if ($request->hasFile($key)) {
            $newPath = $request->file($key)->store($dir, 'public');
            if ($currentPath && Storage::disk('public')->exists($currentPath)) {
                Storage::disk('public')->delete($currentPath);
            }
            return $newPath;
        }

        // front-end may send empty "" when not changed — just keep current
        return $currentPath;
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

    public function search(Request $request)
    {
        $searchKey = $request->search;
        return $this->dealerRepository->search($searchKey);
    }
}
