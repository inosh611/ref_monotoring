<?php

namespace Modules\MyCollections\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Support\Renderable;
use Modules\MyCollections\Repositories\Interfaces\CashRepositoryInterface;
use Modules\MyCollections\Repositories\Interfaces\ChequeRepositoryInterface;
use Modules\MyCollections\Repositories\Interfaces\PaymentRepositoryInterface;
use Modules\Orders\Repositories\Interfaces\OrderRepositoryInterface;

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
        return view('mycollections::edit');
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
