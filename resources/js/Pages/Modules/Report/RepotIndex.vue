<script setup>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref } from "vue";
import {fetchData} from "@/main"
import CustomerWisedVisitsReport from "@/Components/Reports/CustomerWisedVisitsReport.vue";
import DealersStockReport from "@/Components/Reports/DealersStockReport.vue";
import CollectionsReports from "@/Components/Reports/CollectionsReports.vue";
import OrderReports from "@/Components/Reports/OrderReports.vue";

const reportCategory = ref("customer_wised_visits_report");

function  reportName() {
    switch (reportCategory.value) {
        case "customer_wised_visits_report":
            return "Customer Wised Visits Report";
        case "order_report":
            return "Order Reports";
        case "dealer_stock_report":
            return "Dealer Stock Report";
        case "collection_report":
            return "Collection Report";
        default:
            return "";
    }
}

const employees = ref([]);
const dealers = ref([]);

fetchData('/admin/employee/all', employees);
fetchData('/admin/dealer/all', dealers);


</script>

<template>
    <AdminLayout>
        <Head title="Reports" />

        <!-- Header -->
        <div class="content-header reports-header">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="m-0 font-weight-bold">Report Center</h1>
                        <p class="text-muted mb-0 small">
                            Generate sales, collection and performance reports
                            from a single place.
                        </p>
                    </div>
                    <div class="col-md-4 mt-3 mt-md-0 text-md-right">
                        <div
                            class="header-chip d-inline-flex align-items-center px-3 py-2"
                        >
                            <i class="fas fa-info-circle mr-2"></i>
                            <div class="text-left">
                                <div class="chip-label">Tip</div>
                                <div class="chip-text">
                                    Select a category to see relevant filters &
                                    data.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-6 col-12">
                        <!-- Category & Filters Card -->
                        <div class="card card-default filters-card">
                            <div
                                class="card-header border-0 d-flex flex-column flex-md-row align-items-md-center"
                            >
                                <div class="flex-grow-1">
                                    <h3 class="card-title mb-1">
                                        <i class="fas fa-filter mr-1"></i>
                                        Report Filters
                                    </h3>
                                </div>
                            </div>

                            <div class="card-body pt-2 pb-3">
                                <!-- Category select -->
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group mb-3">
                                            <label class="small mb-1 text-muted"
                                                >Report category</label
                                            >
                                            <div
                                                class="input-group input-group-sm"
                                            >
                                                <div
                                                    class="input-group-prepend"
                                                >
                                                    <span
                                                        class="input-group-text"
                                                    >
                                                        <i
                                                            class="fas fa-layer-group"
                                                        ></i>
                                                    </span>
                                                </div>
                                                <select
                                                    v-model="reportCategory"
                                                    class="form-control form-control-sm"
                                                >
                                                    <option
                                                        value="customer_wised_visits_report"
                                                    >
                                                        Customer Wised Visits
                                                        Report
                                                    </option>
                                                    <option value="dealer_stock_report">
                                                        Dealers Stock Report 
                                                    </option>
                                                    <option value="collection_report">
                                                        Collections Reports
                                                    </option>
                                                    <option value="order_report">
                                                        Orders Reports
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div
                    class="row">
                    <div class="col-12">
                        <!-- Category & Filters Card -->
                        <div class="card card-default filters-card">
                            <div
                                class="card-header border-0 d-flex flex-column flex-md-row align-items-md-center"
                            >
                                <div class="flex-grow-1">
                                    <h3 class="card-title mb-1">
                                        <i class="fas fa-user mr-1"></i>
                                        {{ reportName() }}
                                    </h3>
                                </div>
                            </div>

                            <div class="card-body pt-2 pb-3">
                                <!-- Category select -->
                                <div class="row">
                                    <div class="col-12" v-if="reportCategory == 'customer_wised_visits_report'">
                                        <CustomerWisedVisitsReport 
                                          :employees="employees"
                                          :dealers="dealers"
                                          :fetch_url="'/admin/report/visiting'"
                                        />
                                    </div>
                                    <div class="col-12" v-if="reportCategory == 'dealer_stock_report'">
                                        <DealersStockReport 
                                          :employees="employees"
                                          :dealers="dealers"
                                          :fetch_url="'/admin/report/visiting'"
                                        />
                                    </div>
                                    <div class="col-12" v-if="reportCategory == 'collection_report'">
                                        <CollectionsReports 
                                          :employees="employees"
                                          :dealers="dealers"
                                          :fetch_url="'/admin/report/collection'"
                                        />
                                    </div>
                                    <div class="col-12" v-if="reportCategory == 'order_report'">
                                        <OrderReports 
                                          :employees="employees"
                                          :dealers="dealers"
                                          :fetch_url="'/admin/report/order'"
                                        />
                                    </div>
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
.reports-header {
    border-radius: 0 0 1.6rem 1.6rem;
    padding-bottom: 1.5rem;
    margin-bottom: 1rem;
}

.reports-header h1 {
    font-size: 1.6rem;
}

.header-chip {
    border-radius: 999px;
    background: rgba(15, 23, 42, 0.95);
    color: #e5e7eb;
    box-shadow: 0 10px 25px rgba(15, 23, 42, 0.45);
    font-size: 0.8rem;
}

.header-chip .chip-label {
    text-transform: uppercase;
    font-size: 0.7rem;
    letter-spacing: 0.06em;
    color: #9ca3af;
}

.header-chip .chip-text {
    font-size: 0.78rem;
}

.filters-card {
    border-radius: 1rem;
    box-shadow: 0 10px 20px rgba(15, 23, 42, 0.08);
}

/* Report cards */
.report-card {
    border-radius: 1rem;
    box-shadow: 0 10px 20px rgba(15, 23, 42, 0.08);
    margin-top: 0.75rem;
}

/* Tables */
.table-hover tbody tr:hover {
    background-color: #f9fafb;
}

/* Progress bar */
.progress.progress-xs {
    height: 6px;
    border-radius: 999px;
    margin-bottom: 2px;
}

/* Badges */
.badge-success {
    background-color: #22c55e;
}
.badge-warning {
    background-color: #f59e0b;
}
.badge-info {
    background-color: #0ea5e9;
}
.badge-light {
    background-color: #e5e7eb;
    color: #374151;
}

.download-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.6rem 1.2rem;
    border-radius: 999px;
    border: none;
    cursor: pointer;

    background: linear-gradient(135deg, #2563eb, #4f46e5);
    color: #ffffff;
    font-size: 0.85rem;
    font-weight: 600;
    letter-spacing: 0.03em;

    box-shadow: 0 8px 18px rgba(37, 99, 235, 0.35);
    transition: all 0.25s ease;
}

.download-btn i {
    font-size: 0.9rem;
}

.download-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 24px rgba(37, 99, 235, 0.45);
    background: linear-gradient(135deg, #1d4ed8, #4338ca);
}

.download-btn:active {
    transform: translateY(0);
    box-shadow: 0 6px 14px rgba(37, 99, 235, 0.3);
}

.download-btn:focus {
    outline: none;
}

/* Responsive */
@media (max-width: 767.98px) {
    .reports-header {
        border-radius: 0 0 1rem 1rem;
    }
}
</style>
