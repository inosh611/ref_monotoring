<?php

namespace Modules\Target\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Contracts\Support\Renderable;
use Modules\Target\Repositories\Interfaces\TargetRepositoryInterface;

class TargetController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    protected $targetRepository;

    public function __construct(TargetRepositoryInterface $target_repository)
    {
        $this->targetRepository = $target_repository;
    }

    public function index()
    {
        return Inertia::render('Modules/Target/targetManagement');
    }


    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return Inertia::render('Modules/Target/CreateTarget');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        if (! $this->checkUserByEmployeeRegNo($request->employee_registration_number)) {
            return response()->json([
            'success' => false,
            'message' => 'Employee Registration Number Not Found'
        ], 422); 
        } else {
            $targetData = [
                'employee_reg_no' => $request->employee_registration_number,
                'user_id' => findUserId($request->employee_registration_number),
                'target_value' => $request->target_amount,
                'month' => $request->target_month,
                'year' => $request->target_year,
            ];
            try {
                $this->targetRepository->create($targetData);
                return response()->json([
                    'success' => true,
                    'message' => 'Target Successfully Saved.',
                    'redirect' => route('target.index')
                ]);
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'An error occurred while creating the target: ' . $e->getMessage());
            };
        }

    }
    private function checkUserByEmployeeRegNo($employee_registration_number)
        {
            return \App\Models\User::where('reg_number', $employee_registration_number)->exists();
        }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('target::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('target::edit');
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
