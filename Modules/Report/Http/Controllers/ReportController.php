<?php

namespace Modules\Report\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Orders\Entities\Order;
use Modules\MyCollections\Entities\Payment;
use Modules\MyVisiting\Entities\MyVisiting;
use Illuminate\Contracts\Support\Renderable;

class ReportController extends Controller
{
    public function index()
    {
        return Inertia::render('Modules/Report/RepotIndex');
    }

    public function customerWisedVisitsReport(Request $request)
    {

        $query = MyVisiting::with(['dealer', 'user']);

        if ($request->selected_dealer && $request->selected_dealer !== 'all') {
            $query->where('dealer_id', $request->selected_dealer);
        }

        if ($request->selected_employee && $request->selected_employee !== 'all') {
            $query->where('ref_id', $request->selected_employee);
        }

        if ($request->selected_status === 'visited') {
            $query->whereNotNull('checkout_time');
        }

        if ($request->selected_status === 'none-visited') {
            $query->whereNull('checkout_time');
        }

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('date', [
                $request->start_date,
                $request->end_date
            ]);
        } elseif ($request->start_date) {
            $query->whereDate('date', '>=', $request->start_date);
        } elseif ($request->end_date) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        if ($request->search) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('location', 'like', "%{$search}%")
                    ->orWhere('ref_id', 'like', "%{$search}%")
                    ->orWhereHas('dealer', function ($dq) use ($search) {
                        $dq->where('business_name', 'like', "%{$search}%")
                            ->orWhere('business_address', 'like', "%{$search}%");
                    })
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('contact_number', 'like', "%{$search}%");
                    });
            });
        }

        $allowedSortColumns = ['id', 'date', 'time', 'checkout_time'];

        $sortColumn = in_array($request->sort_column, $allowedSortColumns)
            ? $request->sort_column
            : 'id';

        $sortDirection = $request->sort_direction === 'desc' ? 'desc' : 'asc';

        $query->orderBy($sortColumn, $sortDirection);

        $perPage = $request->per_page ?? 10;

        $results = $query->paginate($perPage);

        return response()->json([
            'data' => $results->items(),
            'total' => $results->total(),
        ]);
    }

    public function orderReport(Request $request)
{
    $query = Order::with(['shop', 'user']); // use relations that exist

    // Shop filter
    if ($request->selected_shop && $request->selected_shop !== 'all') {
        $query->where('shop_id', $request->selected_shop);
    }

    // Employee filter
    if ($request->selected_employee && $request->selected_employee !== 'all') {
        $query->where('user_id', $request->selected_employee);
    }

    // Order status
    if ($request->selected_order_status && $request->selected_order_status !== 'all') {
        $query->where('order_status', $request->selected_order_status);
    }

    // Payment status
    if ($request->selected_payment_status && $request->selected_payment_status !== 'all') {
        $query->where('payment_status', $request->selected_payment_status);
    }

    // Date range (use expected_order_date)
    if ($request->start_date && $request->end_date) {
        $query->whereBetween('expected_order_date', [$request->start_date, $request->end_date]);
    } elseif ($request->start_date) {
        $query->whereDate('expected_order_date', '>=', $request->start_date);
    } elseif ($request->end_date) {
        $query->whereDate('expected_order_date', '<=', $request->end_date);
    }

    // Search
    if ($request->search) {
        $search = $request->search;

        $query->where(function ($q) use ($search) {
            $q->where('order_number', 'like', "%{$search}%")
              ->orWhere('total_price', 'like', "%{$search}%")
              ->orWhere('paid_amount', 'like', "%{$search}%")
              ->orWhereHas('shop', function ($sq) use ($search) {
                  $sq->where('business_name', 'like', "%{$search}%")
                     ->orWhere('business_address', 'like', "%{$search}%")
                     ->orWhere('business_tel', 'like', "%{$search}%");
              })
              ->orWhereHas('user', function ($uq) use ($search) {
                  $uq->where('reg_number', 'like', "%{$search}%")
                     ->orWhere('first_name', 'like', "%{$search}%")
                     ->orWhere('last_name', 'like', "%{$search}%")
                     ->orWhere('contact_number', 'like', "%{$search}%");
              });
        });
    }

    // Sorting
    $allowedSortColumns = [
        'id', 'order_number', 'expected_order_date',
        'total_price', 'paid_amount', 'order_status', 'payment_status'
    ];

    $sortColumn = in_array($request->sort_column, $allowedSortColumns)
        ? $request->sort_column
        : 'id';

    $sortDirection = $request->sort_direction === 'desc' ? 'desc' : 'asc';
    $query->orderBy($sortColumn, $sortDirection);

    $perPage = (int) ($request->per_page ?? 10);

    $results = $query->paginate($perPage);

    return response()->json([
        'data' => $results->items(),
        'total' => $results->total(),
    ]);
}
 

public function collectionReport(Request $request)
{
    $query = Payment::query()
        ->with([
            'order.shop',
            'order.user',
            'user',
            'cash',
            'cheque',
        ]);

    // -------------------------
    // Shop filter (via order)
    // -------------------------
    if ($request->filled('selected_shop') && $request->selected_shop !== 'all') {
        $query->whereHas('order', function ($q) use ($request) {
            $q->where('shop_id', $request->selected_shop);
        });
    }

    // -------------------------
    // Collector employee filter
    // -------------------------
    if ($request->filled('selected_employee') && $request->selected_employee !== 'all') {
        $query->where('user_id', $request->selected_employee);
    }

    // -------------------------
    // Collection type filter
    // -------------------------
    if ($request->filled('selected_collection_type') && $request->selected_collection_type !== 'all') {
        $query->where('collection_type', $request->selected_collection_type);
    }

    // -------------------------
    // Date range (payments created_at)
    // -------------------------
    if ($request->filled('start_date') && $request->filled('end_date')) {
        $query->whereBetween('created_at', [
            $request->start_date . ' 00:00:00',
            $request->end_date . ' 23:59:59'
        ]);
    } elseif ($request->filled('start_date')) {
        $query->where('created_at', '>=', $request->start_date . ' 00:00:00');
    } elseif ($request->filled('end_date')) {
        $query->where('created_at', '<=', $request->end_date . ' 23:59:59');
    }

    // -------------------------
    // Search
    // -------------------------
    if ($request->filled('search')) {
        $search = $request->search;

        $query->where(function ($q) use ($search) {
            $q->where('paid_amount', 'like', "%{$search}%")
              ->orWhere('paid_amount_text', 'like', "%{$search}%")
              ->orWhere('comment', 'like', "%{$search}%")
              ->orWhere('collection_type', 'like', "%{$search}%")

              ->orWhereHas('order', function ($oq) use ($search) {
                  $oq->where('order_number', 'like', "%{$search}%");
              })

              ->orWhereHas('order.shop', function ($sq) use ($search) {
                  $sq->where('business_name', 'like', "%{$search}%")
                     ->orWhere('business_address', 'like', "%{$search}%")
                     ->orWhere('business_tel', 'like', "%{$search}%");
              })

              ->orWhereHas('user', function ($uq) use ($search) {
                  $uq->where('reg_number', 'like', "%{$search}%")
                     ->orWhere('first_name', 'like', "%{$search}%")
                     ->orWhere('last_name', 'like', "%{$search}%")
                     ->orWhere('contact_number', 'like', "%{$search}%");
              })

              ->orWhereHas('cash', function ($cq) use ($search) {
                  $cq->where('cash_receipt_number', 'like', "%{$search}%");
              })

              ->orWhereHas('cheque', function ($chq) use ($search) {
                  $chq->where('cheque_number', 'like', "%{$search}%")
                      ->orWhere('receipt_number', 'like', "%{$search}%")
                      ->orWhere('bank', 'like', "%{$search}%")
                      ->orWhere('branch', 'like', "%{$search}%");
              });
        });
    }

    // -------------------------
    // Sorting (safe list)
    // -------------------------
    $allowedSortColumns = ['id', 'paid_amount', 'collection_type', 'created_at'];

    $sortColumn = in_array($request->sort_column, $allowedSortColumns)
        ? $request->sort_column
        : 'id';

    $sortDirection = $request->sort_direction === 'desc' ? 'desc' : 'asc';

    $query->orderBy($sortColumn, $sortDirection);

    // -------------------------
    // Pagination
    // -------------------------
    $perPage = (int) ($request->per_page ?? 10);
    $results = $query->paginate($perPage);

    return response()->json([
        'data' => $results->items(),
        'total' => $results->total(),
    ]);
}

}
