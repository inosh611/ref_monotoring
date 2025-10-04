<?php

namespace Modules\Orders\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Modules\Orders\Entities\Item;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Support\Renderable;
use Modules\Orders\Http\Requests\OrderRequest;
use Modules\Orders\Repositories\ItemRepository;
use Illuminate\Support\Collection; // Remove When start Backend
use Modules\Orders\Repositories\Interfaces\OrderRepositoryInterface;
use Modules\Dealers\Repositories\Interfaces\DealerRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator; // Remove When start Backend

class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    protected $dealerRepository;
    protected $orderRepository;
    protected $itemRepository;
    public function __construct(DealerRepositoryInterface $dealerRepository, OrderRepositoryInterface $orderRepository, ItemRepository $itemRepository)
    {
        $this->dealerRepository = $dealerRepository;
        $this->orderRepository = $orderRepository;
        $this->itemRepository = $itemRepository;
    }



    public function dataTable(Request $request) // Remove When start Backend
    {
        // Sample items
        $items = [
            ['id' => 1,  'order_number' => 'ORD-1001', 'order_price' => 1500.00, 'order_status' => 'Pending',    'payment_status' => 'Unpaid',   'payment' => 0.00,     'payment_type' => 'Cash'],
            ['id' => 2,  'order_number' => 'ORD-1002', 'order_price' => 2200.50, 'order_status' => 'Processing', 'payment_status' => 'Partial',  'payment' => 1000.00,  'payment_type' => 'Credit'],
            ['id' => 3,  'order_number' => 'ORD-1003', 'order_price' => 1800.75, 'order_status' => 'Completed',  'payment_status' => 'Paid',     'payment' => 1800.75,  'payment_type' => 'Cash'],
            ['id' => 4,  'order_number' => 'ORD-1004', 'order_price' => 2500.00, 'order_status' => 'Shipped',    'payment_status' => 'Paid',     'payment' => 2500.00,  'payment_type' => 'Check'],
            ['id' => 5,  'order_number' => 'ORD-1005', 'order_price' => 3000.00, 'order_status' => 'Cancelled',  'payment_status' => 'Refunded', 'payment' => -3000.00, 'payment_type' => 'Credit'],
            ['id' => 6,  'order_number' => 'ORD-1006', 'order_price' => 1200.00, 'order_status' => 'Pending',    'payment_status' => 'Unpaid',   'payment' => 0.00,     'payment_type' => 'Cash'],
            ['id' => 7,  'order_number' => 'ORD-1007', 'order_price' => 2750.00, 'order_status' => 'Processing', 'payment_status' => 'Partial',  'payment' => 1000.00,  'payment_type' => 'Credit'],
            ['id' => 8,  'order_number' => 'ORD-1008', 'order_price' => 1600.25, 'order_status' => 'Completed',  'payment_status' => 'Paid',     'payment' => 1600.25,  'payment_type' => 'Cash'],
            ['id' => 9,  'order_number' => 'ORD-1009', 'order_price' => 1999.99, 'order_status' => 'Shipped',    'payment_status' => 'Paid',     'payment' => 1999.99,  'payment_type' => 'Check'],
            ['id' => 10, 'order_number' => 'ORD-1010', 'order_price' => 1450.50, 'order_status' => 'Cancelled',  'payment_status' => 'Refunded', 'payment' => -1450.50, 'payment_type' => 'Credit'],
            ['id' => 11, 'order_number' => 'ORD-1011', 'order_price' => 2755.00, 'order_status' => 'Pending',    'payment_status' => 'Unpaid',   'payment' => 0.00,     'payment_type' => 'Cash'],
            ['id' => 12, 'order_number' => 'ORD-1012', 'order_price' => 1890.75, 'order_status' => 'Processing', 'payment_status' => 'Partial',  'payment' => 900.00,   'payment_type' => 'Credit'],
            ['id' => 13, 'order_number' => 'ORD-1013', 'order_price' => 3300.00, 'order_status' => 'Completed',  'payment_status' => 'Paid',     'payment' => 3300.00,  'payment_type' => 'Cash'],
            ['id' => 14, 'order_number' => 'ORD-1014', 'order_price' => 2100.10, 'order_status' => 'Shipped',    'payment_status' => 'Paid',     'payment' => 2100.10,  'payment_type' => 'Check'],
            ['id' => 15, 'order_number' => 'ORD-1015', 'order_price' => 1995.00, 'order_status' => 'Cancelled',  'payment_status' => 'Refunded', 'payment' => -1995.00, 'payment_type' => 'Credit'],
            ['id' => 16, 'order_number' => 'ORD-1016', 'order_price' => 2800.00, 'order_status' => 'Completed',  'payment_status' => 'Paid',     'payment' => 2800.00,  'payment_type' => 'Cash'],
            ['id' => 17, 'order_number' => 'ORD-1017', 'order_price' => 2400.00, 'order_status' => 'Processing', 'payment_status' => 'Partial',  'payment' => 1000.00,  'payment_type' => 'Credit'],
            ['id' => 18, 'order_number' => 'ORD-1018', 'order_price' => 3150.50, 'order_status' => 'Shipped',    'payment_status' => 'Paid',     'payment' => 3150.50,  'payment_type' => 'Check'],
            ['id' => 19, 'order_number' => 'ORD-1019', 'order_price' => 1250.00, 'order_status' => 'Pending',    'payment_status' => 'Unpaid',   'payment' => 0.00,     'payment_type' => 'Cash'],
            ['id' => 20, 'order_number' => 'ORD-1020', 'order_price' => 2700.00, 'order_status' => 'Cancelled',  'payment_status' => 'Refunded', 'payment' => -2700.00, 'payment_type' => 'Credit'],
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
        return Inertia::render('Modules/Orders/OrderManagement');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $dealers = $this->dealerRepository->allData();
        $orderCount = $this->orderRepository->orderCount();
        $user = auth()->user();
        return Inertia::render('Modules/Orders/CreateOrder', ['dealers' => $dealers, 'user' => $user, 'orderCount' => $orderCount]);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(OrderRequest $request)
    {

        $validated = $request->validated();
        try {
            DB::beginTransaction();
            $orderDetails = [
                'order_number' => $validated['order_number'],
                'shop_id' => $validated['shop_id'],
                'user_id' => $validated['user_id'],
                'order_status' => $validated['order_status'],
                'payment_status' => $validated['payment_status'],
                'total_price' => $validated['total_price'],
            ];
            $order = $this->orderRepository->create($orderDetails);
            $itemList = $validated['item_list'];
            if ($order && !empty($itemList)) {
                foreach ($itemList as $item) {
                    $itemDetails['order_id'] = $order->id;
                    $itemDetails['product_id'] = $item['product_id'];
                    $itemDetails['price_id'] = $item['price_id'];
                    $itemDetails['quantity'] = $item['quantity'];
                    $itemDetails['price'] = $item['price'];
                    $itemDetails['sub_total'] = $item['sub_total'];
                    $this->itemRepository->create($itemDetails);
                }
               
            }
            DB::commit();
           
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            // return response()->json([
            //     'success' => false,
            //     'message' => 'Order Creation Failed. Error: ' . $e->getMessage(),
            //     'redirect' => route('orders.index')
            // ], 500);
        }
        
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('orders::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('orders::edit');
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
