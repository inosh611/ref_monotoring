<?php

namespace Modules\Orders\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Modules\Orders\Entities\Item;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Support\Renderable;
use Modules\Dealers\Repositories\DealerStockRepository;
use Modules\Orders\Http\Requests\OrderRequest;
use Modules\Orders\Repositories\Interfaces\OrderRepositoryInterface;
use Modules\Dealers\Repositories\Interfaces\DealerRepositoryInterface;
use Modules\Orders\Repositories\Interfaces\ItemRepositoryInterface;

class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    protected $dealerRepository;
    protected $orderRepository;
    protected $itemRepository;
    protected $dealerStockRepository;
    public function __construct(DealerRepositoryInterface $dealerRepository, OrderRepositoryInterface $orderRepository, ItemRepositoryInterface $itemRepository, DealerStockRepository $dealerStockRepository)
    {
        $this->dealerRepository = $dealerRepository;
        $this->orderRepository = $orderRepository;
        $this->itemRepository = $itemRepository;
        $this->dealerStockRepository = $dealerStockRepository;
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
    public function update(Request $request)
    {
        $orderStatus = $request->order_status;
        if (is_string($request->items)) {
        $items = json_decode($request->items, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            abort(422, 'Invalid items JSON.');
        }
    }
        if ($orderStatus == "Delivered") {
            try {
                DB::beginTransaction();
                $updated = $this->orderRepository->update($request->order_id, $request->only(['order_status']));
                if ($updated) {
                    foreach ($items as $item) {
                        $itemDetails = [
                            'shop_id' => $request->shop_id,
                            'item_id' => $item['id'],
                            'user_id' => auth()->user()->id,
                            'order_id' => $request->order_id,
                            'quantity' => $item['quantity'],
                        ];
                        $this->dealerStockRepository->create($itemDetails);
                    }
                }
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Order Successfully Updated.',
                    'redirect' => route('order.index')
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                dd($e);
                return response()->json([
                    'error' => true,
                    'message' => 'Order Creation Failed. Error: ' . $e->getMessage(),
                    'redirect' => route('order.index')
                ], 500);
            }
        }
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

    public function search(Request $request)
    {

        $search = (string) $request->query('search', '');
        $shopId = (int) $request->query('shop_id', 0);
        $results = $this->orderRepository->search($search, $shopId);
        return response()->json(['results' => $results]);
    }
}
