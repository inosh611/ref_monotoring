<?php

namespace Modules\MyCollections\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Orders\Entities\Order;
use Modules\MyCollections\Entities\Payment;
use Illuminate\Contracts\Support\Renderable;
use Modules\Orders\Repositories\Interfaces\OrderRepositoryInterface;
use Modules\MyCollections\Repositories\Interfaces\CashRepositoryInterface;
use Modules\MyCollections\Repositories\Interfaces\ChequeRepositoryInterface;
use Modules\MyCollections\Repositories\Interfaces\PaymentRepositoryInterface;

class MyCollectionsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    protected $cashRepository;
    protected $ChequeRepository;
    protected $paymentRepository;
    protected $orderRepository;

    public function __construct(
        CashRepositoryInterface $cashRepository,
        ChequeRepositoryInterface $ChequeRepository,
        PaymentRepositoryInterface $paymentRepository,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->cashRepository = $cashRepository;
        $this->ChequeRepository = $ChequeRepository;
        $this->paymentRepository = $paymentRepository;
        $this->orderRepository = $orderRepository;
    }

    public function dataTable(Request $request)
    {
        return ($this->paymentRepository->dataTable($request));
    }

    public function index()
    {
        return Inertia::render('Modules/MyCollection/MyCollectionManagement');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return Inertia::render('Modules/MyCollection/CreateCollection');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $userId = auth()->user()->id;
        $comment = $request->comment;
        if ($request->collection_type == 1) {
            try {
                $validatedCheque =  $request->validate([
                    'collection_type' => 'required',
                    'order_number' => 'required',
                    'cheque_number' => 'required',
                    'bank' => 'required',
                    'branch' => 'required',
                    'cheque_date' => 'required',
                    'cheque_amount' => 'required',
                    'cheque_amount_text' => 'required',
                    'cheque_type' => 'required',
                    'receipt_number' => 'required',
                ]);


                $paymentData = [
                    'order_id' => findOrderId($validatedCheque['order_number']),
                    'user_id' => $userId,
                    'collection_type' => $validatedCheque['collection_type'],
                    'paid_amount' => $validatedCheque['cheque_amount'],
                    'paid_amount_text' => $validatedCheque['cheque_amount_text'],
                    'comment' => $comment,
                ];

                DB::beginTransaction();
                $paidAmount = $this->orderRepository->find(findOrderId($validatedCheque['order_number']))->paid_amount;
                $updatedOrder = $this->orderRepository->update(findOrderId($validatedCheque['order_number']), ['payment_status' => "processing", 'paid_amount' => $paidAmount + $validatedCheque['cheque_amount']]);
                $payment = $this->paymentRepository->create($paymentData);

                if ($payment) {
                    $chequeData = [
                        'payment_id' => $payment->id,
                        'cheque_number' => $validatedCheque['cheque_number'],
                        'bank' => $validatedCheque['bank'],
                        'branch' => $validatedCheque['branch'],
                        'cheque_date' => $validatedCheque['cheque_date'],
                        'cheque_amount' => $validatedCheque['cheque_amount'],
                        'cheque_amount_text' => $validatedCheque['cheque_amount_text'],
                        'cheque_type' => $validatedCheque['cheque_type'],
                        'receipt_number' => $validatedCheque['receipt_number'],
                    ];
                    $this->ChequeRepository->create($chequeData);
                }

                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Your Collection Successfully Saved.',
                    'redirect' => route('my.collection.index')
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'message' => 'Collection failed',
                    'error' => $e->getMessage(),
                ], 500);
            }
        } else {
            try {
                $validatedCash =  $request->validate([
                    'collection_type' => 'required',
                    'order_number' => 'required',
                    'cash_amount' => 'required',
                    'cash_amount_text' => 'required',
                    'cash_receipt_number' => 'required',
                ]);

                $paymentData = [
                    'order_id' => findOrderId($validatedCash['order_number']),
                    'user_id' => $userId,
                    'collection_type' => $validatedCash['collection_type'],
                    'paid_amount' => $validatedCash['cash_amount'],
                    'paid_amount_text' => $validatedCash['cash_amount_text'],
                    'comment' => $comment,
                ];

                DB::beginTransaction();
                $paidAmount = $this->orderRepository->find(findOrderId($validatedCash['order_number']))->paid_amount;
                $updatedOrder = $this->orderRepository->update(findOrderId($validatedCash['order_number']), ['payment_status' => "processing", 'paid_amount' => $paidAmount + $validatedCash['cash_amount']]);
                $payment = $this->paymentRepository->create($paymentData);
                if ($payment) {
                    $cashData = [
                        'payment_id' => $payment->id,
                        'cash_amount' => $validatedCash['cash_amount'],
                        'cash_amount_text' => $validatedCash['cash_amount_text'],
                        'cash_receipt_number' => $validatedCash['cash_receipt_number'],
                    ];
                    $this->cashRepository->create($cashData);
                }
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Your Collection Successfully Saved.',
                    'redirect' => route('my.collection.index')
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'message' => 'Collection failed',
                    'error' => $e->getMessage(),
                ], 500);
            }
        }
    }

    public function searchOrder(Request $request)
    {
        $order_id = findOrderId($request->search);
        if ($order_id) {
            $order = $this->orderRepository->find($order_id);
            return response()->json([
                'data' => $order,
            ], 200);
        } else {
            return response()->json([
                'data' => null,
            ], 200);
        }
    }
    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('mycollections::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $payment = Payment::with(['order', 'cash', 'cheque'])->findOrFail($id);

        return Inertia::render('Modules/MyCollection/EditCollection', [
            'payment' => $payment,
        ]);
    }

    // ✅ UPDATE without id in URL, get it from request
    public function update(Request $request)
    {
        $validated = $request->validate([
            'id' => ['required', 'integer'],
            'collection_type' => ['required', 'in:1,2'],
            'order_number' => ['required'],
            'comment' => ['nullable', 'string'],
        ]);

        $payment = Payment::with(['cash', 'cheque', 'order'])->findOrFail($validated['id']);

        // ✅ change this if your column name differs
        $order = Order::where('order_number', $validated['order_number'])->first();
        if (!$order) {
            return response()->json([
                'message' => 'Order not found.',
                'redirect' => url()->previous(),
            ], 422);
        }

        DB::transaction(function () use ($request, $validated, $payment, $order) {

            // update payment common
            $payment->update([
                'order_id' => $order->id,
                'collection_type' => (int) $validated['collection_type'],
                'comment' => $validated['comment'] ?? null,
            ]);

            // ✅ cheque update
            if ((int)$validated['collection_type'] === 1) {

                $typeData = $request->validate([
                    'cheque_number' => ['required', 'string'],
                    'bank' => ['required', 'string'],
                    'branch' => ['required', 'string'],
                    'cheque_date' => ['required', 'date'],
                    'cheque_amount' => ['required', 'numeric', 'min:0.01'],
                    'cheque_amount_text' => ['required', 'string'],
                    'cheque_type' => ['required', 'string'],
                    'receipt_number' => ['required', 'string'],
                ]);

                $payment->update([
                    'paid_amount' => $typeData['cheque_amount'],
                    'paid_amount_text' => $typeData['cheque_amount_text'],
                ]);

                $payment->cheque()->updateOrCreate(
                    ['payment_id' => $payment->id],
                    [
                        'cheque_number' => $typeData['cheque_number'],
                        'bank' => $typeData['bank'],
                        'branch' => $typeData['branch'],
                        'cheque_date' => $typeData['cheque_date'],
                        'cheque_amount' => $typeData['cheque_amount'],
                        'cheque_amount_text' => $typeData['cheque_amount_text'],
                        'cheque_type' => $typeData['cheque_type'],
                        'receipt_number' => $typeData['receipt_number'],
                    ]
                );

                // remove cash if existed
                $payment->cash()->delete();
            }

            // ✅ cash update
            if ((int)$validated['collection_type'] === 2) {

                $typeData = $request->validate([
                    'cash_amount' => ['required', 'numeric', 'min:0.01'],
                    'cash_amount_text' => ['required', 'string'],
                    'cash_receipt_number' => ['required', 'string'],
                ]);

                $payment->update([
                    'paid_amount' => $typeData['cash_amount'],
                    'paid_amount_text' => $typeData['cash_amount_text'],
                ]);

                $payment->cash()->updateOrCreate(
                    ['payment_id' => $payment->id],
                    [
                        'cash_amount' => $typeData['cash_amount'],
                        'cash_amount_text' => $typeData['cash_amount_text'],
                        'cash_receipt_number' => $typeData['cash_receipt_number'],
                    ]
                );

                // remove cheque if existed
                $payment->cheque()->delete();
            }

            // ✅ optional: recalc order paid amount
            $order->update([
                'paid_amount' => Payment::where('order_id', $order->id)->sum('paid_amount')
            ]);
        });

        return response()->json([
            'message' => 'Collection updated successfully.',
            'redirect' => route('my.collection.index'),
        ]);
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
