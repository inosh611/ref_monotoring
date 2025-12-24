<!-- resources/js/Pages/Modules/AdminDashBoard/Dashboard.vue -->
<script setup>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue";
import { Head } from "@inertiajs/vue3";
import { Chart, registerables } from "chart.js";
import { onMounted, ref, computed } from "vue";

Chart.register(...registerables);

const props = defineProps({
  kpis: Object,                 // { totalDealers, todayVisits, todayCollection, pendingCollections }
  todayRouteVisits: Array,      // table rows
  paymentSplit: Object,         // { cash, cheque, credit }
  monthlyCollection: Object,    // { labels:[], data:[] }
});

// ---------- Helpers ----------
const fmtMoney = (v) => Number(v || 0).toLocaleString("en-LK", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
const fmtInt = (v) => Number(v || 0).toLocaleString("en-LK");

// ---------- Chart refs ----------
const pieChartRef = ref(null);
const barChartRef = ref(null);

let pieChartInstance = null;
let barChartInstance = null;

const pieData = computed(() => [
  Number(props.paymentSplit?.cash || 0),
  Number(props.paymentSplit?.cheque || 0),
  Number(props.paymentSplit?.credit || 0),
]);

const pieHasAny = computed(() => pieData.value.reduce((a, b) => a + b, 0) > 0);

onMounted(() => {
  setTimeout(() => {
    // ===== PIE: Today's payment split =====
    if (pieChartRef.value) {
      const pieCtx = pieChartRef.value.getContext("2d");

      if (pieChartInstance) pieChartInstance.destroy();

      pieChartInstance = new Chart(pieCtx, {
        type: "pie",
        data: {
          labels: ["Cash", "Cheque", "Credit"],
          datasets: [
            {
              label: "Today Split",
              data: pieHasAny.value ? pieData.value : [1, 0, 0],
              // don't set custom colors if you don't want; Chart.js default is fine.
              borderWidth: 1,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { position: "bottom" },
            title: { display: true, text: "Today’s Collections by Payment Type" },
            tooltip: {
              callbacks: {
                label: (ctx) => {
                  const value = Number(ctx.raw || 0);
                  return `${ctx.label}: LKR ${fmtMoney(value)}`;
                },
              },
            },
          },
        },
      });
    }

    // ===== BAR: Monthly collection (last 6 months) =====
    if (barChartRef.value) {
      const barCtx = barChartRef.value.getContext("2d");

      if (barChartInstance) barChartInstance.destroy();

      barChartInstance = new Chart(barCtx, {
        type: "bar",
        data: {
          labels: props.monthlyCollection?.labels || [],
          datasets: [
            {
              label: "Collection (LKR)",
              data: props.monthlyCollection?.data || [],
              borderWidth: 0,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              beginAtZero: true,
              grid: { display: false },
              ticks: {
                callback: (v) => fmtInt(v),
              },
            },
            x: { grid: { display: false } },
          },
          plugins: {
            legend: { display: true },
            title: { display: true, text: "ARG Monthly Collection Trend" },
            tooltip: {
              callbacks: {
                label: (ctx) => `LKR ${fmtMoney(ctx.raw)}`,
              },
            },
          },
        },
      });
    }
  }, 250);
});

// ---------- Table computed ----------
const routeRows = computed(() => props.todayRouteVisits || []);

const statusBadgeClass = (status) => {
  if (status === "Completed") return "badge-success";
  if (status === "Cheque Received") return "badge-warning";
  return "badge-danger";
};
</script>

<template>
  <AdminLayout>
    <Head title="Admin Dashboard" />

    <!-- Header -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2 align-items-center">
          <div class="col-sm-8">
            <h1 class="m-0 font-weight-bold">ARG Sales Force – Admin Dashboard</h1>
            <p class="text-muted mb-0 small">
              Monitor reps, dealers, collections & route activity at a glance.
            </p>
          </div>
          <div class="col-sm-4">
            <ol class="breadcrumb float-sm-right mb-0">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Dashboard</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <!-- Content -->
    <section class="content">
      <div class="container-fluid">
        <!-- KPI Row -->
        <div class="row">
          <div class="col-lg-3 col-6">
            <div class="small-box kpi-box dealers-color">
              <div class="inner">
                <p class="kpi-label">Total Dealers</p>
                <h3 class="kpi-value">{{ kpis?.totalDealers ?? 0 }}</h3>
              </div>
              <div class="icon"><i class="fas fa-store"></i></div>
              <a href="#" class="small-box-footer">
                View all dealers <i class="fas fa-arrow-circle-right"></i>
              </a>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box kpi-box visits-color">
              <div class="inner">
                <p class="kpi-label">Today’s Visits</p>
                <h3 class="kpi-value">{{ kpis?.todayVisits ?? 0 }}</h3>
              </div>
              <div class="icon"><i class="fas fa-route"></i></div>
              <a href="#" class="small-box-footer">
                View route summary <i class="fas fa-arrow-circle-right"></i>
              </a>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box kpi-box collection-color">
              <div class="inner">
                <p class="kpi-label">Today’s Collection (LKR)</p>
                <h3 class="kpi-value">{{ fmtInt(kpis?.todayCollection ?? 0) }}</h3>
              </div>
              <div class="icon"><i class="fas fa-coins"></i></div>
              <a href="#" class="small-box-footer">
                View collection details <i class="fas fa-arrow-circle-right"></i>
              </a>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box kpi-box pending-color">
              <div class="inner">
                <p class="kpi-label">Pending Collections</p>
                <h3 class="kpi-value">{{ kpis?.pendingCollections ?? 0 }}</h3>
              </div>
              <div class="icon"><i class="fas fa-exclamation-circle"></i></div>
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
                <span class="badge badge-pill badge-light text-muted small">Live</span>
              </div>
              <div class="card-body">
                <div class="chart-wrap">
                  <canvas ref="barChartRef" class="chart-canvas"></canvas>
                </div>
              </div>
            </div>
          </div>

          <div class="col-12 col-lg-5">
            <div class="card card-default chart-card">
              <div class="card-header border-0 d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">
                  <i class="fas fa-chart-pie mr-1"></i> Today’s Payment Split
                </h3>
                <span class="badge badge-pill badge-light text-muted small">Live</span>
              </div>
              <div class="card-body d-flex justify-content-center">
                <div class="chart-wrap pie-wrap">
                  <canvas ref="pieChartRef" class="chart-canvas"></canvas>
                </div>
              </div>
              <div class="px-3 pb-3 small text-muted">
                Cash: <b>LKR {{ fmtInt(paymentSplit?.cash ?? 0) }}</b> |
                Cheque: <b>LKR {{ fmtInt(paymentSplit?.cheque ?? 0) }}</b> |
                Credit: <b>LKR {{ fmtInt(paymentSplit?.credit ?? 0) }}</b>
              </div>
            </div>
          </div>
        </div>

        <!-- Today Route Visits -->
        <div class="row mt-3">
          <div class="col-12">
            <div class="card card-default">
              <div class="card-header border-0 d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">
                  <i class="fas fa-clipboard-list mr-1"></i> Today Route Visits
                </h3>
                <span class="text-muted small">Live data for today.</span>
              </div>

              <div class="card-body p-0">
                <div class="table-responsive">
                  <table class="table table-hover mb-0">
                    <thead class="thead-light">
                      <tr>
                        <th style="width: 60px">#</th>
                        <th>Rep</th>
                        <th>Dealer</th>
                        <th>In Time</th>
                        <th>Out Time</th>
                        <th class="text-right">Collection (LKR)</th>
                        <th>Status</th>
                      </tr>
                    </thead>

                    <tbody v-if="routeRows.length">
                      <tr v-for="(row, idx) in routeRows" :key="row.id">
                        <td>{{ idx + 1 }}</td>
                        <td>{{ row.employee_name }}</td>
                        <td>{{ row.dealer_name }}</td>
                        <td>{{ row.in_time || "-" }}</td>
                        <td>
                          <span v-if="row.out_time">{{ row.out_time }}</span>
                          <span v-else class="badge badge-danger">Not Yet</span>
                        </td>
                        <td class="text-right">{{ fmtInt(row.collection_amount || 0) }}</td>
                        <td>
                          <span class="badge" :class="statusBadgeClass(row.status)">
                            {{ row.status }}
                          </span>
                        </td>
                      </tr>
                    </tbody>

                    <tbody v-else>
                      <tr>
                        <td colspan="7" class="text-center text-muted py-3">
                          No visits found for today.
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
.content-header h1 {
  font-size: 1.6rem;
}

.kpi-box {
  border-radius: 1rem;
  box-shadow: 0 10px 22px rgba(0, 0, 0, 0.1);
  transition: transform 0.2s ease, box-shadow 0.2s ease;
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
  background: linear-gradient(45deg, #c04848, #480048) !important;
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

.chart-wrap {
  height: 320px;
}
.pie-wrap {
  height: 320px;
  width: 100%;
  max-width: 420px;
}

.chart-canvas {
  width: 100% !important;
  height: 100% !important;
}

/* Table card */
.card-default {
  border-radius: 1rem;
}

.table-hover tbody tr:hover {
  background-color: #f8fafc;
}

.badge-success {
  background-color: #28a745;
}
.badge-warning {
  background-color: #ffc107;
  color: #111827;
}
.badge-danger {
  background-color: #dc3545;
}

@media (max-width: 767.98px) {
  .chart-wrap,
  .pie-wrap {
    height: 260px;
  }
}
</style>
