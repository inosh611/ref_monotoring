<?php

namespace Modules\Orders\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Modules\Orders\Entities\Item;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Orders\Entities\Order;
use Illuminate\Contracts\Support\Renderable;
use Modules\Orders\Http\Requests\OrderRequest;
use Modules\Dealers\Repositories\DealerStockRepository;
use Modules\Orders\Repositories\Interfaces\ItemRepositoryInterface;
use Modules\Orders\Repositories\Interfaces\OrderRepositoryInterface;
use Modules\Dealers\Repositories\Interfaces\DealerRepositoryInterface;

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
                'expected_order_date' => $validated['expected_order_date'],
                'expected_collection_date' => $validated['expected_collection_date'],
                'expected_order_date_comment' => $request->expected_order_date_comment,
                'expected_collection_date_comment' => $request->expected_collection_date_comment
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
        $dealers = $this->dealerRepository->allData();
        $orderCount = $this->orderRepository->orderCount();
        $user = auth()->user();
        $orderDetails =  $this->orderRepository->find($id);
        return Inertia::render('Modules/Orders/EditOrder', [
            'orderDetails' => $orderDetails,
            'dealers' => $dealers,
            'user' => $user,
            'orderCount' => $orderCount
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
    
        if (!$request->has('full_update')) {
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
                   
                    return response()->json([
                        'error' => true,
                        'message' => 'Order Creation Failed. Error: ' . $e->getMessage(),
                        'redirect' => route('order.index')
                    ], 500);
                }
            }
            elseif ($orderStatus != "Delivered") {
                try {
                    $this->orderRepository->update($request->order_id, $request->only(['order_status']));
                    return response()->json([
                        'success' => true,
                        'message' => 'Order Successfully Updated.',
                        'redirect' => route('order.index')
                    ]);
                } catch (\Exception $e) {
                    return response()->json([
                        'error' => true,
                        'message' => 'Order Update Failed. Error: ' . $e->getMessage(),
                        'redirect' => route('order.index')
                    ], 500);
                }
            }
        } else {

            $orderId = (int) $request->input('order_id');
            $payloadItems = json_decode($request->input('item_list', '[]'), true);

            if (!$orderId || !is_array($payloadItems)) {
                return response()->json(['error' => true, 'message' => 'Invalid input'], 422);
            }

            // Load order with current items
            $order = Order::with('items')->find($orderId);
            if (!$order) {
                return response()->json(['error' => true, 'message' => 'Order not found'], 404);
            }

            DB::beginTransaction();
            try {
                // --- 1) Update order header (use provided value or keep existing) ---
                $order->shop_id                         = $request->input('shop_id', $order->shop_id);
                $order->user_id                         = $request->input('user_id', $order->user_id);
                $order->order_status                    = $request->input('order_status', $order->order_status);
                $order->payment_status                  = $request->input('payment_status', $order->payment_status);
                $order->expected_order_date             = $request->input('expected_order_date', $order->expected_order_date);
                $order->expected_order_date_comment     = $request->input('expected_order_date_comment', $order->expected_order_date_comment);
                $order->expected_collection_date        = $request->input('expected_collection_date', $order->expected_collection_date);
                $order->expected_collection_date_comment = $request->input('expected_collection_date_comment', $order->expected_collection_date_comment);
                // save later after items & total computed

                // --- 2) Diff items: update / create / delete ---
                $existing = $order->items->keyBy('id'); // current DB items
                $keepIds  = [];
                $toUpsert = []; // only for rows that have id

                $now = now();

                foreach ($payloadItems as $row) {
                    // sanitize
                    $productId = isset($row['product_id']) ? (int)$row['product_id'] : null;
                    $priceId   = isset($row['price_id'])   ? (int)$row['price_id']   : null;
                    $qty       = isset($row['quantity'])   ? (int)$row['quantity']   : 0;
                    $price     = isset($row['price'])      ? (float)$row['price']    : 0.0;
                    $sub       = isset($row['sub_total'])  ? (float)$row['sub_total'] : ($qty * $price);

                    if (!$productId || !$priceId || $qty <= 0) {
                        // skip invalid rows; you can also return 422 if you want to be strict
                        continue;
                    }

                    // Existing item -> upsert by id
                    if (!empty($row['id'])) {
                        $id = (int)$row['id'];
                        $keepIds[] = $id;

                        $toUpsert[] = [
                            'id'         => $id,
                            'order_id'   => $orderId,
                            'product_id' => $productId,
                            'price_id'   => $priceId,
                            'quantity'   => $qty,
                            'price'      => $price,
                            'sub_total'  => $sub,
                            'updated_at' => $now,
                        ];
                    } else {
                        // New item -> create now
                        $created = Item::create([
                            'order_id'   => $orderId,
                            'product_id' => $productId,
                            'price_id'   => $priceId,
                            'quantity'   => $qty,
                            'price'      => $price,
                            'sub_total'  => $sub,
                        ]);
                        $keepIds[] = $created->id;
                    }
                }

                // Update existing rows in bulk (by primary key id)
                if (!empty($toUpsert)) {
                    Item::upsert(
                        $toUpsert,
                        ['id'],
                        ['product_id', 'price_id', 'quantity', 'price', 'sub_total', 'updated_at']
                    );
                }

                // Delete DB rows that are no longer present in payload
                if (count($keepIds)) {
                    Item::where('order_id', $orderId)
                        ->whereNotIn('id', $keepIds)
                        ->delete();
                } else {
                    // If the payload has no items, clear them all
                    Item::where('order_id', $orderId)->delete();
                }

                // --- 3) Recalculate total from DB and save order header ---
                $order->total_price = Item::where('order_id', $orderId)->sum('sub_total');
                $order->save();

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Order updated',
                    'total'   => $order->total_price,
                    'redirect' => route('order.index')
                ]);
            } catch (\Throwable $e) {
                DB::rollBack();
                return response()->json(['error' => true, 'message' => $e->getMessage()], 500);
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

    public function searchOrder(Request $request)
    {

        $search = (string) $request->query('search', '');
        $dealerId = (string) $request->query('dealer_id', '');
        $results = $this->orderRepository->orderFind($search,  $dealerId);

        return response()->json(['results' => $results]);
    }
}
