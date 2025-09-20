<?php

namespace Modules\Employee\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Support\Renderable;
use Modules\Employee\Http\Requests\EmployeeRequest;
use Illuminate\Support\Collection; // Remove When start Backend
use Modules\Employee\Repositories\Interfaces\EmployeeRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator; // Remove When start Backend



class EmployeeController extends Controller
{
    protected $employeeRepository;

    public function __construct(EmployeeRepositoryInterface $employee_repository)
    {
        $this->employeeRepository = $employee_repository;
    }



    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function dataTable(Request $request) // Remove When start Backend
    {
        return ($this->employeeRepository->dataTable($request));
    }
    public function index()
    {
        return Inertia::render("Modules/Employee/EmployeeManagement");
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $roles = Role::all();

        return Inertia::render("Modules/Employee/CreateEmployee", ['roles' => $roles]);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(EmployeeRequest $request)
    {
        $validated = $request->validated();
        $password = Hash::make($validated['email']);
        $payload = [
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'nic' => $validated['nic'],
            'contact_number' => $validated['contact_number'],
            'address' => $validated['address'],
            'position' => $validated['position'],
            'email' => $validated['email'],
            'password' => $password,
        ];
        $user =  $this->employeeRepository->create($payload);
        if ($user) {
            $user->assignRole($validated['roll_name']);
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('employee::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        

        try{
            $user = $this->employeeRepository->find($id);
             return Inertia::render("Modules/Employee/EditEmployee", [
            'user' => $user,
            'role' => $user->getRoleNames()->first(),   
            'roles' => Role::select('id', 'name')->get()
        ]);
        }catch(\Exception $error){
            Log::error('Customer Find Failed: ' . $error->getMessage());
        }
       
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(EmployeeRequest $request)
    {
        $validated = $request->validated();
        
        try{
            $user = $this->employeeRepository->update($validated['id'], $validated);
            $user->syncRoles([$validated['roll_name']]);
            return response()->json([
                'success' => true,
                'message' => 'Employee Successfully Updated.',
                'redirect' => route('employee.index')]);
        }catch(\Exception $error){
             Log::error('Customer Update Failed: ' . $error->getMessage());
        }
        
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(Request $request)
    {
        try{
             $user = $this->employeeRepository->delete($request->id);
             if($user){
                 return response()->json([
                'success' => true,
                'message' => 'Employee Successfully Deleted.',
                 ]);
             }
        }catch(\Exception $error){
             Log::error('Customer Update Failed: ' . $error->getMessage());
        };
    }
}
