<?php

namespace Modules\Task\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Illuminate\Support\Collection; // Remove When start Backend
use Illuminate\Pagination\LengthAwarePaginator; // Remove When start Backend
class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function dataTable(Request $request) // Remove When start Backend
    {
        // Sample items
        $items = [
            ['id' => 1, 'task_number' => 'TSK-1001', 'task_type' => 'Visit', 'dealer_name' => 'Wijaya Traders', 'rep_name' => 'Kamal Perera', 'due_date' => '2025-09-01', 'status' => 'Pending', 'priority' => 'High', 'location' => 'Colombo 05', 'notes' => 'First visit of week'],
            ['id' => 2, 'task_number' => 'TSK-1002', 'task_type' => 'Stock Check', 'dealer_name' => 'Saman Stores', 'rep_name' => 'Nimal Fernando', 'due_date' => '2025-09-01', 'status' => 'In Progress', 'priority' => 'Medium', 'location' => 'Kandy', 'notes' => 'Capture current biscuit stock'],
            ['id' => 3, 'task_number' => 'TSK-1003', 'task_type' => 'Collection', 'dealer_name' => 'City Super', 'rep_name' => 'Sunil Silva', 'due_date' => '2025-09-01', 'status' => 'Completed', 'priority' => 'High', 'location' => 'Galle', 'notes' => 'Collected 65,000 LKR'],
            ['id' => 4, 'task_number' => 'TSK-1004', 'task_type' => 'Order', 'dealer_name' => 'Metro Mart', 'rep_name' => 'Chathura Jay', 'due_date' => '2025-09-01', 'status' => 'Overdue', 'priority' => 'High', 'location' => 'Kurunegala', 'notes' => 'Order not submitted'],
            ['id' => 5, 'task_number' => 'TSK-1005', 'task_type' => 'Photo', 'dealer_name' => 'Lakshmi Trade', 'rep_name' => 'Ruwan Bandara', 'due_date' => '2025-09-01', 'status' => 'Completed', 'priority' => 'Low', 'location' => 'Anuradhapura', 'notes' => 'Front shop photo'],
            ['id' => 6, 'task_number' => 'TSK-1006', 'task_type' => 'GPS', 'dealer_name' => 'Golden Mart', 'rep_name' => 'Harsha Karu', 'due_date' => '2025-09-01', 'status' => 'Pending', 'priority' => 'Low', 'location' => 'Matara', 'notes' => 'Verify GPS within 20m'],
            ['id' => 7, 'task_number' => 'TSK-1007', 'task_type' => 'Report', 'dealer_name' => 'Nisansala Foods', 'rep_name' => 'Anuradha Dias', 'due_date' => '2025-09-01', 'status' => 'Completed', 'priority' => 'Medium', 'location' => 'Negombo', 'notes' => 'Daily summary submitted'],
            ['id' => 8, 'task_number' => 'TSK-1008', 'task_type' => 'Visit', 'dealer_name' => 'Araliya Stores', 'rep_name' => 'Pradeep Samara', 'due_date' => '2025-09-02', 'status' => 'Pending', 'priority' => 'Medium', 'location' => 'Ratnapura', 'notes' => 'Introduce new promo'],
            ['id' => 9, 'task_number' => 'TSK-1009', 'task_type' => 'Collection', 'dealer_name' => 'Navod Distributors', 'rep_name' => 'Supun Wijes', 'due_date' => '2025-09-02', 'status' => 'Partial', 'priority' => 'High', 'location' => 'Badulla', 'notes' => 'Received 30,000 / 50,000'],
            ['id' => 10, 'task_number' => 'TSK-1010', 'task_type' => 'Stock Check', 'dealer_name' => 'Super Mart', 'rep_name' => 'Lakmal Guna', 'due_date' => '2025-09-02', 'status' => 'Completed', 'priority' => 'Low', 'location' => 'Hambantota', 'notes' => 'Low on biscuits'],
            ['id' => 11, 'task_number' => 'TSK-1011', 'task_type' => 'Order', 'dealer_name' => 'Mega Bazaar', 'rep_name' => 'Dilan Raja', 'due_date' => '2025-09-02', 'status' => 'In Progress', 'priority' => 'Medium', 'location' => 'Jaffna', 'notes' => 'Order drafting'],
            ['id' => 12, 'task_number' => 'TSK-1012', 'task_type' => 'Photo', 'dealer_name' => 'Ceylon Market', 'rep_name' => 'Kasun Herath', 'due_date' => '2025-09-03', 'status' => 'Pending', 'priority' => 'Low', 'location' => 'Polonnaruwa', 'notes' => 'Shelf photo required'],
            ['id' => 13, 'task_number' => 'TSK-1013', 'task_type' => 'GPS', 'dealer_name' => 'Royal Traders', 'rep_name' => 'Dinusha Abey', 'due_date' => '2025-09-03', 'status' => 'Completed', 'priority' => 'Low', 'location' => 'Trincomalee', 'notes' => 'GPS verified 12m'],
            ['id' => 14, 'task_number' => 'TSK-1014', 'task_type' => 'Visit', 'dealer_name' => 'Kavindi Stores', 'rep_name' => 'Gayan Rana', 'due_date' => '2025-09-03', 'status' => 'Cancelled', 'priority' => 'Low', 'location' => 'Batticaloa', 'notes' => 'Dealer closed'],
            ['id' => 15, 'task_number' => 'TSK-1015', 'task_type' => 'Collection', 'dealer_name' => 'Isuru Foods', 'rep_name' => 'Shehan Liyan', 'due_date' => '2025-09-03', 'status' => 'Completed', 'priority' => 'High', 'location' => 'Ampara', 'notes' => 'Cheque received'],
            ['id' => 16, 'task_number' => 'TSK-1016', 'task_type' => 'Stock Check', 'dealer_name' => 'Thilina Mart', 'rep_name' => 'Roshan Dias', 'due_date' => '2025-09-04', 'status' => 'Pending', 'priority' => 'Medium', 'location' => 'Monaragala', 'notes' => 'Count all SKUs'],
            ['id' => 17, 'task_number' => 'TSK-1017', 'task_type' => 'Order', 'dealer_name' => 'Supreme Trade', 'rep_name' => 'Isuru Eka', 'due_date' => '2025-09-04', 'status' => 'Completed', 'priority' => 'High', 'location' => 'Puttalam', 'notes' => 'Order submitted'],
            ['id' => 18, 'task_number' => 'TSK-1018', 'task_type' => 'Report', 'dealer_name' => 'Tharu Stores', 'rep_name' => 'Chaminda Fon', 'due_date' => '2025-09-04', 'status' => 'Overdue', 'priority' => 'Medium', 'location' => 'Kalutara', 'notes' => 'Daily report missing'],
            ['id' => 19, 'task_number' => 'TSK-1019', 'task_type' => 'Visit', 'dealer_name' => 'Devinda Market', 'rep_name' => 'Tharindu Peiris', 'due_date' => '2025-09-04', 'status' => 'In Progress', 'priority' => 'Medium', 'location' => 'Matale', 'notes' => 'Discuss new SKUs'],
            ['id' => 20, 'task_number' => 'TSK-1020', 'task_type' => 'Photo', 'dealer_name' => 'Highland Stores', 'rep_name' => 'Madush Senan', 'due_date' => '2025-09-05', 'status' => 'Completed', 'priority' => 'Low', 'location' => 'Nuwara Eliya', 'notes' => 'POSM placement'],
        ];


        $collection = new Collection($items);

        // Manual pagination example
        $perPage = 10;
        $page = request()->get('page', 1);
        $paginatedResults = new LengthAwarePaginator(
            $collection->forPage($page, $perPage),
            $collection->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        // Return response in your expected format
        return [
            'data' => $paginatedResults->items(),
            'total' => $paginatedResults->total(),
            'current_page' => $paginatedResults->currentPage(),
            'last_page' => $paginatedResults->lastPage(),
        ];
    }
    public function index()
    {
        return Inertia::render('Modules/Task/TaskManagement');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('task::create');
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
        return view('task::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('task::edit');
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
