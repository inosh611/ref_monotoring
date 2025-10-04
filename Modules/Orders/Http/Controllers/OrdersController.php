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
         return ($this->orderRepository->dataTable($request));
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
                    $item['order_id'] = $order->id;
                    $this->itemRepository->create($item);
                }
            }
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Order Successfully Saved.',
                'redirect' => route('order.index')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            return response()->json([
                'error' => true,
                'message' => 'Order Creation Failed. Error: ' . $e->getMessage(),
                'redirect' => route('orders.index')
            ], 500);
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
    public function destroy(Request $request)
    {
        try {
            $this->orderRepository->delete($request->id);
            return response()->json([
                'success' => true,
                'message' => 'Order Successfully Deleted.',
                'redirect' => route('order.index')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Order Creation Failed. Error: ' . $e->getMessage(),
                'redirect' => route('orders.index')
            ], 500);
        }
    }
}
