{{-- resources/views/pdf/employee_dashboard_report.blade.php --}}
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Daily Employee Report</title>

    <style>
        @page {
            margin: 16px 14px 18px 14px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #111827;
        }

        /* ===== Header ===== */
        .topbar {
            border-radius: 10px;
            background: #0f172a;
            color: #fff;
            padding: 12px 14px;
        }

        .topbar-table {
            width: 100%;
            border-collapse: collapse;
        }

        .topbar-table td {
            border: none;
            padding: 0;
            vertical-align: middle;
        }

        .logo {
            width: 46px;
            height: 46px;
            border-radius: 8px;
            background: #fff;
            padding: 4px;
        }

        .brand {
            font-weight: 800;
            font-size: 16px;
            line-height: 1.1;
        }

        .report-title {
            font-weight: 800;
            font-size: 14px;
            margin-top: 2px;
        }

        .meta {
            margin-top: 6px;
            font-size: 11px;
            opacity: .95;
        }

        /* ===== KPI Cards ===== */
        .grid {
            margin-top: 10px;
            display: table;
            width: 100%;
            border-spacing: 6px 0;
        }

        .grid .cell {
            display: table-cell;
            width: 25%;
            vertical-align: top;
        }

        .card {
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 8px 10px;
            background: #fff;
        }

        .card .label {
            color: #6b7280;
            font-size: 10px;
        }

        .card .value {
            font-size: 14px;
            font-weight: 800;
            margin-top: 3px;
        }

        .card .sub {
            margin-top: 2px;
            color: #6b7280;
            font-size: 9px;
        }

        /* ===== Sections ===== */
        .section-title {
            margin-top: 14px;
            font-weight: 800;
            font-size: 13px;
            color: #0f172a;
        }

        .section-sub {
            color: #6b7280;
            font-size: 10px;
            margin-top: 3px;
        }

        /* ===== Tables (FIXED WIDTH, NO CLIP) ===== */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            table-layout: fixed;
            /* ✅ critical */
        }

        th,
        td {
            border: 1px solid #e5e7eb;
            padding: 6px 6px;
            vertical-align: top;
            font-size: 10px;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        th {
            background: #f3f4f6;
            text-align: left;
            font-size: 10px;
        }

        .muted {
            color: #6b7280;
            font-size: 9px;
            line-height: 1.2;
        }

        .right {
            text-align: right;
        }

        .nowrap {
            white-space: normal !important;
        }

        /* ✅ allow wrap */

        /* ===== Badges ===== */
        .badge {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 999px;
            font-size: 9px;
            font-weight: 800;
            line-height: 1.2;
            white-space: nowrap;
        }

        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-success {
            background: #dcfce7;
            color: #166534;
        }

        .badge-info {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-light {
            background: #e5e7eb;
            color: #374151;
        }

        /* ===== Images ===== */
        .thumb {
            width: 44px;
            height: 44px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }

        /* Footer */
        .footer {
            margin-top: 14px;
            text-align: center;
            color: #6b7280;
            font-size: 9px;
        }
    </style>
</head>

<body>

    @php
        $companyName = $companyName ?? config('app.name', 'Company');
        $companyLogoPath = $companyLogoPath ?? public_path('images/Admin-panel/panel-logo.png');

        $employeeName = $employeeName ?? 'Employee';
        $date = $date ?? \Carbon\Carbon::today()->format('Y-m-d');

        $todayVisiting = $todayVisiting ?? [];
        $todayOrders = $todayOrders ?? [];
        $todayCollections = $todayCollections ?? [];
        $todayExpectedCollections = $todayExpectedCollections ?? [];

        $pendingTotal =
            $pendingTotal ?? collect($todayExpectedCollections)->sum(fn($x) => (float) data_get($x, 'balance', 0));
        $collectedTodayTotal = collect($todayCollections)->sum(fn($x) => (float) data_get($x, 'paid_amount', 0));
    @endphp

    {{-- ===== Header ===== --}}
    <div class="topbar">
        <table class="topbar-table">
            <tr>
                <td style="width:64px;">
                    @if ($companyLogoPath && file_exists($companyLogoPath))
                        <img class="logo" src="{{ $companyLogoPath }}" alt="Logo">
                    @endif
                </td>
                <td>
                    <div class="brand">{{ $companyName }}</div>
                    <div class="report-title">Daily Field Report</div>
                    <div class="meta">
                        Employee: <b>{{ $employeeName }}</b>
                        &nbsp;&nbsp;|&nbsp;&nbsp;
                        Date: <b>{{ $date }}</b>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- ===== KPI ===== --}}
    <div class="grid">
        <div class="cell">
            <div class="card">
                <div class="label">Today Visits</div>
                <div class="value">{{ is_countable($todayVisiting) ? count($todayVisiting) : 0 }}</div>
                <div class="sub">Shops visited today</div>
            </div>
        </div>
        <div class="cell">
            <div class="card">
                <div class="label">Today Orders</div>
                <div class="value">{{ is_countable($todayOrders) ? count($todayOrders) : 0 }}</div>
                <div class="sub">Orders created today</div>
            </div>
        </div>
        <div class="cell">
            <div class="card">
                <div class="label">Today Collections</div>
                <div class="value">{{ number_format((float) $collectedTodayTotal, 2) }}</div>
                <div class="sub">Total collected today (LKR)</div>
            </div>
        </div>
        <div class="cell">
            <div class="card">
                <div class="label">Pending Amount (LKR)</div>
                <div class="value">{{ number_format((float) $pendingTotal, 2) }}</div>
                <div class="sub">Due today + overdue</div>
            </div>
        </div>
    </div>

    {{-- ===================== Today Visiting ===================== --}}
    <div class="section-title">Today Visiting</div>
    <div class="section-sub">Summary of shop visits recorded today.</div>

    <table>
        <thead>
            <tr>
                <th style="width:26px;">#</th>
                <th style="width:210px;">Dealer</th>
                <th style="width:60px;">In Date</th>
                <th style="width:52px;">In Time</th>
                <th style="width:60px;">Out Date</th>
                <th style="width:52px;">Out Time</th>
                <th style="width:60px;">Shop Img</th>
            </tr>
        </thead>
        <tbody>
            @forelse($todayVisiting as $i => $v)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>
                        <div style="font-weight:800;">{{ data_get($v, 'dealer.business_name', '-') }}</div>
                        <div class="muted">{{ data_get($v, 'dealer.business_address', '-') }}</div>

                        @if (data_get($v, 'dealer.business_tel'))
                            <div class="muted">Tel: {{ data_get($v, 'dealer.business_tel') }}</div>
                        @endif
                    </td>
                    <td>{{ data_get($v, 'date', '-') }}</td>
                    <td>{{ data_get($v, 'time', '-') }}</td>
                    <td>{{ data_get($v, 'checkout_date', '-') }}</td>
                    <td>{{ data_get($v, 'checkout_time', '-') }}</td>
                    <td>
                        @php $imgFile = data_get($v, 'shop_image_file'); @endphp
                        @if ($imgFile && file_exists($imgFile))
                            <img class="thumb" src="{{ $imgFile }}" alt="Shop">
                        @else
                            <span class="muted">-</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="muted">No visits found for today.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- ===================== Today Orders ===================== --}}
    <div class="section-title">Today Orders</div>
    <div class="section-sub">Orders created today by this employee.</div>

    <table>
        <thead>
            <tr>
                <th style="width:26px;">#</th>
                <th style="width:170px;">Shop</th>
                <th style="width:86px;">Order No</th>
                <th style="width:62px;">Created</th>
                <th style="width:62px;" class="right">Total</th>
                <th style="width:62px;" class="right">Paid</th>
                <th style="width:60px;">Order</th>
                <th style="width:60px;">Payment</th>
            </tr>
        </thead>
        <tbody>
            @forelse($todayOrders as $i => $o)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>
                        <div style="font-weight:800;">{{ data_get($o, 'shop.business_name', '-') }}</div>
                        <div class="muted">{{ data_get($o, 'shop.business_address', '-') }}</div>
                    </td>
                    <td>{{ data_get($o, 'order_number', '-') }}</td>
                    <td>{{ \Illuminate\Support\Str::of((string) data_get($o, 'created_at', '-'))->substr(0, 10) }}</td>
                    <td class="right">{{ number_format((float) data_get($o, 'total_price', 0), 2) }}</td>
                    <td class="right">{{ number_format((float) data_get($o, 'paid_amount', 0), 2) }}</td>
                    <td><span class="badge badge-info">{{ data_get($o, 'order_status', '-') }}</span></td>
                    <td><span class="badge badge-light">{{ data_get($o, 'payment_status', '-') }}</span></td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="muted">No orders created today.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- ===================== Today Collections ===================== --}}
    <div class="section-title">Today Collections</div>
    <div class="section-sub">Collections received today by this employee (Cash / Cheque).</div>

    <table>
        <thead>
            <tr>
                <th style="width:26px;">#</th>
                <th style="width:155px;">Shop</th>
                <th style="width:84px;">Invoice</th>
                <th style="width:70px;">Paid</th>
                <th style="width:48px;">Type</th>
                <th style="width:70px;">Cheque</th>
                <th style="width:70px;">Receipt</th>
                <th style="width:62px;" class="right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($todayCollections as $i => $c)
                @php $t = strtolower((string)data_get($c,'payment_type','')); @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>
                        <div style="font-weight:800;">{{ data_get($c, 'shop.business_name', '-') }}</div>
                        <div class="muted">{{ data_get($c, 'shop.business_address', '-') }}</div>
                    </td>
                    <td>{{ data_get($c, 'order_number', '-') }}</td>
                    <td>{{ \Illuminate\Support\Str::of((string) data_get($c, 'paid_date', '-'))->substr(0, 10) }}</td>
                    <td>
                        <span class="badge {{ $t === 'cash' ? 'badge-success' : 'badge-warning' }}">
                            {{ data_get($c, 'payment_type', '-') }}
                        </span>
                    </td>
                    <td>{{ data_get($c, 'cheque_number', '--') ?: '--' }}</td>
                    <td>{{ data_get($c, 'receipt_number', '--') ?: '--' }}</td>
                    <td class="right">{{ number_format((float) data_get($c, 'paid_amount', 0), 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="muted">No collections received today.</td>
                </tr>
            @endforelse
        </tbody>
        @if (count($todayCollections) > 0)
            <tfoot>
                <tr>
                    <th colspan="7" class="right">Total Collected Today (LKR)</th>
                    <th class="right">{{ number_format((float) $collectedTodayTotal, 2) }}</th>
                </tr>
            </tfoot>
        @endif
    </table>

    {{-- ===================== Expected Collections (Pending) ===================== --}}
    {{-- <div class="section-title">Expected Collections (Pending)</div>
    <div class="section-sub">Collections where paid amount is less than total price (Due today + overdue).</div>

    <table>
        <thead>
            <tr>
                <th style="width:26px;">#</th>
                <th style="width:190px;">Dealer</th>
                <th style="width:92px;">Invoice</th>
                <th style="width:62px;">Expected</th>
                <th style="width:62px;" class="right">Total</th>
                <th style="width:62px;" class="right">Paid</th>
                <th style="width:70px;" class="right">Balance</th>
                <th style="width:60px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($todayExpectedCollections as $i => $c)
                @php
                    $st = (string) data_get($c, 'status', 'Pending');
                    $badgeClass = 'badge-warning';
                    if ($st === 'Overdue') {
                        $badgeClass = 'badge-danger';
                    }
                    if ($st === 'Due Today') {
                        $badgeClass = 'badge-warning';
                    }
                @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>
                        <div style="font-weight:800;">{{ data_get($c, 'dealer_name', '-') }}</div>
                        <div class="muted">{{ data_get($c, 'telephone', '-') }}</div>
                    </td>
                    <td>{{ data_get($c, 'order_number', '-') }}</td>
                    <td>{{ data_get($c, 'expected_date', '-') }}</td>
                    <td class="right">{{ number_format((float) data_get($c, 'total_price', 0), 2) }}</td>
                    <td class="right">{{ number_format((float) data_get($c, 'paid_amount', 0), 2) }}</td>
                    <td class="right"><b>{{ number_format((float) data_get($c, 'balance', 0), 2) }}</b></td>
                    <td><span class="badge {{ $badgeClass }}">{{ $st }}</span></td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="muted">No pending collections found.</td>
                </tr>
            @endforelse
        </tbody>

        @if (count($todayExpectedCollections) > 0)
            <tfoot>
                <tr>
                    <th colspan="6" class="right">Total Pending Balance (LKR)</th>
                    <th class="right">{{ number_format((float) $pendingTotal, 2) }}</th>
                    <th></th>
                </tr>
            </tfoot>
        @endif
    </table> --}}

    <div class="footer">
        Generated by System • {{ $date }} • Confidential
    </div>

</body>

</html>
