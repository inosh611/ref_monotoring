<script setup>
import AdminLayout from '@/Layouts/Admin/AdminLayout.vue';
import { Chart, registerables } from 'chart.js';
import { Head } from "@inertiajs/vue3";
import { onMounted, ref } from 'vue';

Chart.register(...registerables);

// Static KPI data
const totalDealers = 128;
const todaysVisits = 24;
const todaysCollection = '452,500';
const pendingCollections = 11;

// Static table data – Today Route Visits
const staticVisits = [
    {
        id: 1,
        employee_name: 'Inosh Perera',
        dealer_name: 'Ranjan Stores - Kurunegala',
        in_time: '09:10',
        out_time: '09:32',
        collection_amount: 45000,
        status: 'Completed',
    },
    {
        id: 2,
        employee_name: 'Kasun Silva',
        dealer_name: 'Lakmini Traders - Kandy',
        in_time: '10:05',
        out_time: '10:28',
        collection_amount: 32500,
        status: 'Completed',
    },
    {
        id: 3,
        employee_name: 'Nipuni Jayasena',
        dealer_name: 'Sunil Distributors - Matale',
        in_time: '10:45',
        out_time: '11:02',
        collection_amount: 0,
        status: 'Pending Collection',
    },
    {
        id: 4,
        employee_name: 'Ruwan Fernando',
        dealer_name: 'City Super Mart - Colombo 10',
        in_time: '11:30',
        out_time: '11:55',
        collection_amount: 78000,
        status: 'Completed',
    },
    {
        id: 5,
        employee_name: 'Inosh Perera',
        dealer_name: 'Sahan Stores - Kurunegala',
        in_time: '12:20',
        out_time: '12:41',
        collection_amount: 25000,
        status: 'Cheque Received',
    },
];


// Chart refs
const pieChartRef = ref(null);
const barChartRef = ref(null);

// Mount Chart.js charts with static data
onMounted(() => {
    setTimeout(() => {
        // ---------- PIE CHART: COLLECTION BY PAYMENT TYPE ----------
        const pieCtx = pieChartRef.value.getContext('2d');

        const pieGradientColors = {
            cash:   { startColor: '#00b09b', endColor: '#96c93d' },
            cheque: { startColor: '#8e2de2', endColor: '#4a00e0' },
            credit: { startColor: '#f7971e', endColor: '#ffd200' },
        };

        const gradientCash = pieCtx.createLinearGradient(0, 0, 0, 400);
        gradientCash.addColorStop(0, pieGradientColors.cash.startColor);
        gradientCash.addColorStop(1, pieGradientColors.cash.endColor);

        const gradientCheque = pieCtx.createLinearGradient(0, 0, 0, 400);
        gradientCheque.addColorStop(0, pieGradientColors.cheque.startColor);
        gradientCheque.addColorStop(1, pieGradientColors.cheque.endColor);

        const gradientCredit = pieCtx.createLinearGradient(0, 0, 0, 400);
        gradientCredit.addColorStop(0, pieGradientColors.credit.startColor);
        gradientCredit.addColorStop(1, pieGradientColors.credit.endColor);

        new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: ['Cash', 'Cheque', 'Credit'],
                datasets: [{
                    label: 'Collections Split',
                    data: [55, 30, 15], // static %
                    backgroundColor: [
                        gradientCash,
                        gradientCheque,
                        gradientCredit,
                    ],
                    borderColor: [
                        gradientCash,
                        gradientCheque,
                        gradientCredit,
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                animation: {
                    duration: 1000,
                    easing: 'easeOutBounce'
                },
                plugins: {
                    legend: { position: 'bottom' },
                    title: { display: true, text: 'Today\'s Collections by Payment Type' }
                }
            }
        });

        // ---------- BAR CHART: MONTHLY COLLECTION ----------
        const barCtx = barChartRef.value.getContext('2d');
        const colorGradient = { startColor: '#E8175E', endColor: '#FF8A00' };
        const gradientBar = barCtx.createLinearGradient(0, 0, 0, 400);
        gradientBar.addColorStop(0, colorGradient.startColor);
        gradientBar.addColorStop(1, colorGradient.endColor);

        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Collection (LKR \'000)',
                    data: [420, 510, 480, 560, 610, 590],
                    backgroundColor: gradientBar,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                animation: {
                    duration: 1000,
                    easing: 'easeOutQuart'
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { display: false }
                    },
                    x: {
                        grid: { display: false }
                    }
                },
                plugins: {
                    legend: { display: true },
                    title: { display: true, text: 'ARG Monthly Collection Trend' }
                }
            }
        });
    }, 500);
});
</script>

<template>
    <AdminLayout>
        <Head title="Admin Dashboard" />

        <!-- Dashboard Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2 align-items-center">
                    <div class="col-sm-6">
                        <h1 class="m-0 font-weight-bold">ARG Sales Force – Admin Dashboard</h1>
                        <p class="text-muted mb-0 small">
                            Monitor reps, dealers, collections & route activity at a glance.
                        </p>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right mb-0">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dashboard Content -->
        <section class="content">
            <div class="container-fluid">
                <!-- KPI Row -->
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box kpi-box dealers-color">
                            <div class="inner">
                                <p class="kpi-label">Total Dealers</p>
                                <h3 class="kpi-value">{{ totalDealers }}</h3>
                            </div>
                            <div class="icon">
                                <i class="fas fa-store"></i>
                            </div>
                            <a href="#" class="small-box-footer">
                                View all dealers <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box kpi-box visits-color">
                            <div class="inner">
                                <p class="kpi-label">Today’s Visits</p>
                                <h3 class="kpi-value">{{ todaysVisits }}</h3>
                            </div>
                            <div class="icon">
                                <i class="fas fa-route"></i>
                            </div>
                            <a href="#" class="small-box-footer">
                                View route summary <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box kpi-box collection-color">
                            <div class="inner">
                                <p class="kpi-label">Today’s Collection (LKR)</p>
                                <h3 class="kpi-value">{{ todaysCollection }}</h3>
                            </div>
                            <div class="icon">
                                <i class="fas fa-coins"></i>
                            </div>
                            <a href="#" class="small-box-footer">
                                View collection details <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box kpi-box pending-color">
                            <div class="inner">
                                <p class="kpi-label">Pending Collections</p>
                                <h3 class="kpi-value">{{ pendingCollections }}</h3>
                            </div>
                            <div class="icon">
                                <i class="fas fa-exclamation-circle"></i>
                            </div>
                            <a href="#" class="small-box-footer">
                                View pending list <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="row mt-3">
                    <div class="col-12 col-lg-7">
                        <div class="card card-default chart-card">
                            <div class="card-header border-0 d-flex justify-content-between align-items-center">
                                <h3 class="card-title mb-0">
                                    <i class="fas fa-chart-line mr-1"></i> Monthly Collection
                                </h3>
                                <span class="badge badge-pill badge-light text-muted small">
                                    Static demo data
                                </span>
                            </div>
                            <div class="card-body">
                                <canvas ref="barChartRef" class="chart-canvas"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-5">
                        <div class="card card-default chart-card">
                            <div class="card-header border-0 d-flex justify-content-between align-items-center">
                                <h3 class="card-title mb-0">
                                    <i class="fas fa-chart-pie mr-1"></i> Today’s Payment Split
                                </h3>
                                <span class="badge badge-pill badge-light text-muted small">
                                    Cash vs Cheque vs Credit
                                </span>
                            </div>
                            <div class="card-body d-flex justify-content-center">
                                <canvas ref="pieChartRef" class="chart-canvas pie"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Static Table Row: Today Route Visits -->
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card card-default">
                            <div class="card-header border-0 d-flex justify-content-between align-items-center">
                                <h3 class="card-title mb-0">
                                    <i class="fas fa-clipboard-list mr-1"></i> Today Route Visits
                                </h3>
                                <span class="text-muted small">
                                    Static sample data for UI preview.
                                </span>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th style="width: 60px;">#</th>
                                                <th>Rep</th>
                                                <th>Dealer</th>
                                                <th>In Time</th>
                                                <th>Out Time</th>
                                                <th class="text-right">Collection (LKR)</th>
                                                <th>Status</th>
                                                <th style="width: 80px;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="visit in staticVisits" :key="visit.id">
                                                <td>{{ visit.id }}</td>
                                                <td>{{ visit.employee_name }}</td>
                                                <td>{{ visit.dealer_name }}</td>
                                                <td>{{ visit.in_time }}</td>
                                                <td>{{ visit.out_time }}</td>
                                                <td class="text-right">
                                                    {{ Number(visit.collection_amount).toLocaleString('en-LK') }}
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge"
                                                        :class="{
                                                            'badge-success': visit.status === 'Completed',
                                                            'badge-warning': visit.status === 'Cheque Received',
                                                            'badge-danger': visit.status === 'Pending Collection'
                                                        }"
                                                    >
                                                        {{ visit.status }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-xs btn-outline-primary">
                                                        View
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </AdminLayout>
</template>

<style scoped>
/* .content-header {
    background: linear-gradient(120deg, #141E30, #243B55);
    color: #fff;
    border-radius: 0 0 1.5rem 1.5rem;
    padding-bottom: 1.5rem;
    margin-bottom: 1rem;
} */

.content-header h1 {
    font-size: 1.6rem;
}

.kpi-box {
    border-radius: 1rem;
    box-shadow: 0 10px 22px rgba(0, 0, 0, 0.1);
    transition: transform .2s ease, box-shadow .2s ease;
    position: relative;
    overflow: hidden;
}

.kpi-box .inner {
    position: relative;
    z-index: 1;
}

.kpi-box::before {
    content: "";
    position: absolute;
    right: -20%;
    top: -40%;
    width: 160px;
    height: 160px;
    background: rgba(255, 255, 255, 0.15);
    border-radius: 50%;
    z-index: 0;
}

.kpi-box:hover {
    transform: translateY(-4px);
    box-shadow: 0 14px 32px rgba(0, 0, 0, 0.18);
}

.kpi-label {
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.08em;
    margin-bottom: 0.25rem;
    opacity: 0.9;
    font-weight: 600;
}

.kpi-value {
    font-weight: 800;
    letter-spacing: 0.05em;
}

/* Gradient backgrounds */
.dealers-color {
    background: linear-gradient(45deg, #8e2de2, #4a00e0) !important;
    color: #fff !important;
}
.visits-color {
    background: linear-gradient(45deg, #00b09b, #96c93d) !important;
    color: #fff !important;
}
.collection-color {
    background: linear-gradient(45deg, #f7971e, #ffd200) !important;
    color: #fff !important;
}
.pending-color {
    background: linear-gradient(45deg, #C04848, #480048) !important;
    color: #fff !important;
}

.small-box .icon {
    color: rgba(255, 255, 255, 0.7);
}

.small-box-footer {
    background-color: rgba(0, 0, 0, 0.08) !important;
    border-radius: 0 0 1rem 1rem;
    font-size: 0.75rem;
}

/* Charts */
.chart-card {
    border-radius: 1rem;
    box-shadow: 0 10px 22px rgba(0, 0, 0, 0.06);
}

.chart-canvas {
    height: 320px !important;
    max-height: 340px;
}

/* Table card */
.card-default {
    border-radius: 1rem;
}

.card-header {
    background: transparent;
}

.table-hover tbody tr:hover {
    background-color: #f8fafc;
}

.badge-success {
    background-color: #28a745;
}
.badge-warning {
    background-color: #ffc107;
}
.badge-danger {
    background-color: #dc3545;
}

@media (max-width: 767.98px) {
    .content-header {
        border-radius: 0 0 1rem 1rem;
    }
}
</style>
