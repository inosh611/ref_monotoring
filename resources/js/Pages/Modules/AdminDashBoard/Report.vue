<script setup>
import AdminLayout from '@/Layouts/Admin/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

const reportCategory = ref('daily_route');

// Common static filter data
const employees = [
  'All Reps',
  'Inosh Perera',
  'Kasun Silva',
  'Nipuni Jayasena',
  'Ruwan Fernando',
];

const regions = ['All Regions', 'Kurunegala', 'Kandy', 'Colombo', 'Matale'];
const months = ['January', 'February', 'March', 'April', 'May', 'June'];

// Filters state
const filters = ref({
  daily_route: {
    date: '2025-11-18',
    employee: 'All Reps',
    status: 'All',
  },
  collection: {
    from: '2025-11-01',
    to: '2025-11-18',
    employee: 'All Reps',
    type: 'All',
  },
  outstanding: {
    as_of: '2025-11-18',
    region: 'All Regions',
    ageing: 'All',
  },
  target: {
    month: 'November',
    year: '2025',
    employee: 'All Reps',
  },
});

// ---- Static report data ----
const dailyRouteRows = [
  {
    id: 1,
    rep: 'Inosh Perera',
    dealer: 'Ranjan Stores - Kurunegala',
    in_time: '09:10',
    out_time: '09:32',
    collection: 15000,
    status: 'Completed',
  },
  {
    id: 2,
    rep: 'Kasun Silva',
    dealer: 'Lakmini Traders - Kandy',
    in_time: '10:05',
    out_time: '10:28',
    collection: 0,
    status: 'Pending Collection',
  },
  {
    id: 3,
    rep: 'Nipuni Jayasena',
    dealer: 'Sunil Distributors - Matale',
    in_time: '10:45',
    out_time: '11:02',
    collection: 0,
    status: 'Visited',
  },
];

const collectionRows = [
  {
    id: 1,
    date: '2025-11-18',
    rep: 'Inosh Perera',
    dealer: 'Ranjan Stores',
    cash: 10000,
    cheque: 5000,
    total: 15000,
  },
  {
    id: 2,
    date: '2025-11-18',
    rep: 'Kasun Silva',
    dealer: 'Lakmini Traders',
    cash: 0,
    cheque: 25000,
    total: 25000,
  },
];

const outstandingRows = [
  {
    id: 1,
    dealer: 'Ranjan Stores - Kurunegala',
    region: 'Kurunegala',
    days: 18,
    balance: 42000,
  },
  {
    id: 2,
    dealer: 'Lakmini Traders - Kandy',
    region: 'Kandy',
    days: 35,
    balance: 68500,
  },
  {
    id: 3,
    dealer: 'City Super Mart - Colombo 10',
    region: 'Colombo',
    days: 62,
    balance: 124000,
  },
];

const targetRows = [
  {
    id: 1,
    rep: 'Inosh Perera',
    month: 'November',
    target: 500000,
    achieved: 275000,
  },
  {
    id: 2,
    rep: 'Kasun Silva',
    month: 'November',
    target: 450000,
    achieved: 310000,
  },
];

const getTargetProgress = row =>
  Math.round((row.achieved / row.target) * 100);

// ---- Actions ----
function applyFilter() {
  // Only UI demo – just show toast/console for now
  console.log('Apply filter for:', reportCategory.value, filters.value[reportCategory.value]);
}

function downloadReport(format = 'pdf') {
  const cat = reportCategory.value;
  console.log('Download', cat, 'as', format);
  alert(`Downloading ${format.toUpperCase()} for "${prettyCategory(cat)}" (demo only).`);
}

function prettyCategory(cat) {
  switch (cat) {
    case 'daily_route': return 'Daily Route Visits';
    case 'collection': return 'Collection Summary';
    case 'outstanding': return 'Outstanding Dealer Balances';
    case 'target': return 'Target vs Achievement';
    default: return '';
  }
}
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
              Generate sales, collection and performance reports from a single place.
            </p>
          </div>
          <div class="col-md-4 mt-3 mt-md-0 text-md-right">
            <div class="header-chip d-inline-flex align-items-center px-3 py-2">
              <i class="fas fa-info-circle mr-2"></i>
              <div class="text-left">
                <div class="chip-label">Tip</div>
                <div class="chip-text">Select a category to see relevant filters & data.</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Content -->
    <section class="content">
      <div class="container-fluid">

        <!-- Category & Filters Card -->
        <div class="card card-default filters-card">
          <div class="card-header border-0 d-flex flex-column flex-md-row align-items-md-center">
            <div class="flex-grow-1">
              <h3 class="card-title mb-1">
                <i class="fas fa-filter mr-1"></i> Report Filters
              </h3>
              <span class="text-muted small">
                Choose report category and adjust filters before downloading.
              </span>
            </div>
            <div class="mt-3 mt-md-0">
              <button
                type="button"
                class="btn btn-sm btn-outline-primary mr-2"
                @click="downloadReport('pdf')"
              >
                <i class="far fa-file-pdf mr-1"></i> Download PDF
              </button>
              <button
                type="button"
                class="btn btn-sm btn-outline-success"
                @click="downloadReport('excel')"
              >
                <i class="far fa-file-excel mr-1"></i> Download Excel
              </button>
            </div>
          </div>

          <div class="card-body pt-2 pb-3">
            <!-- Category select -->
            <div class="row">
              <div class="col-lg-4">
                <div class="form-group mb-3">
                  <label class="small mb-1 text-muted">Report category</label>
                  <div class="input-group input-group-sm">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="fas fa-layer-group"></i>
                      </span>
                    </div>
                    <select
                      v-model="reportCategory"
                      class="form-control form-control-sm"
                    >
                      <option value="daily_route">Daily Route Visits</option>
                      <option value="collection">Collection Summary</option>
                      <option value="outstanding">Outstanding Dealer Balances</option>
                      <option value="target">Target vs Achievement</option>
                    </select>
                  </div>
                </div>
              </div>

              <!-- Filters – switch by category -->
              <div class="col-lg-8">
                <!-- Daily Route Filters -->
                <div v-if="reportCategory === 'daily_route'" class="row">
                  <div class="col-md-4">
                    <label class="small mb-1 text-muted">Date</label>
                    <input
                      type="date"
                      v-model="filters.daily_route.date"
                      class="form-control form-control-sm"
                    />
                  </div>
                  <div class="col-md-4 mt-2 mt-md-0">
                    <label class="small mb-1 text-muted">Employee</label>
                    <select
                      v-model="filters.daily_route.employee"
                      class="form-control form-control-sm"
                    >
                      <option v-for="emp in employees" :key="emp" :value="emp">
                        {{ emp }}
                      </option>
                    </select>
                  </div>
                  <div class="col-md-4 mt-2 mt-md-0">
                    <label class="small mb-1 text-muted">Status</label>
                    <select
                      v-model="filters.daily_route.status"
                      class="form-control form-control-sm"
                    >
                      <option value="All">All</option>
                      <option value="Completed">Completed</option>
                      <option value="Pending Collection">Pending Collection</option>
                      <option value="Visited">Visited</option>
                    </select>
                  </div>
                </div>

                <!-- Collection Filters -->
                <div v-else-if="reportCategory === 'collection'" class="row">
                  <div class="col-md-3">
                    <label class="small mb-1 text-muted">From</label>
                    <input
                      type="date"
                      v-model="filters.collection.from"
                      class="form-control form-control-sm"
                    />
                  </div>
                  <div class="col-md-3 mt-2 mt-md-0">
                    <label class="small mb-1 text-muted">To</label>
                    <input
                      type="date"
                      v-model="filters.collection.to"
                      class="form-control form-control-sm"
                    />
                  </div>
                  <div class="col-md-3 mt-2 mt-md-0">
                    <label class="small mb-1 text-muted">Employee</label>
                    <select
                      v-model="filters.collection.employee"
                      class="form-control form-control-sm"
                    >
                      <option v-for="emp in employees" :key="emp" :value="emp">
                        {{ emp }}
                      </option>
                    </select>
                  </div>
                  <div class="col-md-3 mt-2 mt-md-0">
                    <label class="small mb-1 text-muted">Collection type</label>
                    <select
                      v-model="filters.collection.type"
                      class="form-control form-control-sm"
                    >
                      <option value="All">All</option>
                      <option value="Cash">Cash</option>
                      <option value="Cheque">Cheque</option>
                    </select>
                  </div>
                </div>

                <!-- Outstanding Filters -->
                <div v-else-if="reportCategory === 'outstanding'" class="row">
                  <div class="col-md-4">
                    <label class="small mb-1 text-muted">As of date</label>
                    <input
                      type="date"
                      v-model="filters.outstanding.as_of"
                      class="form-control form-control-sm"
                    />
                  </div>
                  <div class="col-md-4 mt-2 mt-md-0">
                    <label class="small mb-1 text-muted">Region</label>
                    <select
                      v-model="filters.outstanding.region"
                      class="form-control form-control-sm"
                    >
                      <option v-for="reg in regions" :key="reg" :value="reg">
                        {{ reg }}
                      </option>
                    </select>
                  </div>
                  <div class="col-md-4 mt-2 mt-md-0">
                    <label class="small mb-1 text-muted">Ageing</label>
                    <select
                      v-model="filters.outstanding.ageing"
                      class="form-control form-control-sm"
                    >
                      <option value="All">All</option>
                      <option value="0-30">0 – 30 days</option>
                      <option value="31-60">31 – 60 days</option>
                      <option value="61+">61+ days</option>
                    </select>
                  </div>
                </div>

                <!-- Target Filters -->
                <div v-else-if="reportCategory === 'target'" class="row">
                  <div class="col-md-4">
                    <label class="small mb-1 text-muted">Month</label>
                    <select
                      v-model="filters.target.month"
                      class="form-control form-control-sm"
                    >
                      <option v-for="m in months" :key="m" :value="m">
                        {{ m }}
                      </option>
                    </select>
                  </div>
                  <div class="col-md-3 mt-2 mt-md-0">
                    <label class="small mb-1 text-muted">Year</label>
                    <input
                      type="number"
                      v-model="filters.target.year"
                      class="form-control form-control-sm"
                      min="2020"
                      max="2030"
                    />
                  </div>
                  <div class="col-md-5 mt-2 mt-md-0">
                    <label class="small mb-1 text-muted">Employee</label>
                    <select
                      v-model="filters.target.employee"
                      class="form-control form-control-sm"
                    >
                      <option v-for="emp in employees" :key="emp" :value="emp">
                        {{ emp }}
                      </option>
                    </select>
                  </div>
                </div>
              </div>
            </div>

            <!-- Apply button -->
            <div class="text-right mt-3">
              <button
                type="button"
                class="btn btn-sm btn-primary px-4"
                @click="applyFilter"
              >
                <i class="fas fa-search mr-1"></i> Apply Filters
              </button>
            </div>
          </div>
        </div>

        <!-- Report Result Table (switch by category) -->
        <!-- DAILY ROUTE -->
        <div v-if="reportCategory === 'daily_route'" class="card card-default report-card">
          <div class="card-header border-0 d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">
              <i class="fas fa-route mr-1"></i> Daily Route Visits
            </h3>
            <span class="badge badge-light small">
              {{ dailyRouteRows.length }} records
            </span>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover mb-0">
                <thead class="thead-light">
                  <tr>
                    <th>#</th>
                    <th>Rep</th>
                    <th>Dealer</th>
                    <th>In Time</th>
                    <th>Out Time</th>
                    <th class="text-right">Collection (LKR)</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="row in dailyRouteRows" :key="row.id">
                    <td>{{ row.id }}</td>
                    <td>{{ row.rep }}</td>
                    <td>{{ row.dealer }}</td>
                    <td>{{ row.in_time }}</td>
                    <td>{{ row.out_time }}</td>
                    <td class="text-right">
                      {{ row.collection.toLocaleString('en-LK') }}
                    </td>
                    <td>
                      <span
                        class="badge"
                        :class="{
                          'badge-success': row.status === 'Completed',
                          'badge-warning': row.status === 'Pending Collection',
                          'badge-info': row.status === 'Visited'
                        }"
                      >
                        {{ row.status }}
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- COLLECTION SUMMARY -->
        <div v-else-if="reportCategory === 'collection'" class="card card-default report-card">
          <div class="card-header border-0 d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">
              <i class="fas fa-hand-holding-usd mr-1"></i> Collection Summary
            </h3>
            <span class="badge badge-light small">
              {{ collectionRows.length }} records
            </span>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover mb-0">
                <thead class="thead-light">
                  <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Rep</th>
                    <th>Dealer</th>
                    <th class="text-right">Cash (LKR)</th>
                    <th class="text-right">Cheque (LKR)</th>
                    <th class="text-right">Total (LKR)</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="row in collectionRows" :key="row.id">
                    <td>{{ row.id }}</td>
                    <td>{{ row.date }}</td>
                    <td>{{ row.rep }}</td>
                    <td>{{ row.dealer }}</td>
                    <td class="text-right">
                      {{ row.cash.toLocaleString('en-LK') }}
                    </td>
                    <td class="text-right">
                      {{ row.cheque.toLocaleString('en-LK') }}
                    </td>
                    <td class="text-right font-weight-bold">
                      {{ row.total.toLocaleString('en-LK') }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- OUTSTANDING -->
        <div v-else-if="reportCategory === 'outstanding'" class="card card-default report-card">
          <div class="card-header border-0 d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">
              <i class="fas fa-file-invoice-dollar mr-1"></i> Outstanding Dealer Balances
            </h3>
            <span class="badge badge-light small">
              {{ outstandingRows.length }} dealers
            </span>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover mb-0">
                <thead class="thead-light">
                  <tr>
                    <th>#</th>
                    <th>Dealer</th>
                    <th>Region</th>
                    <th class="text-right">Ageing (days)</th>
                    <th class="text-right">Balance (LKR)</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="row in outstandingRows" :key="row.id">
                    <td>{{ row.id }}</td>
                    <td>{{ row.dealer }}</td>
                    <td>{{ row.region }}</td>
                    <td class="text-right">
                      {{ row.days }}
                    </td>
                    <td class="text-right font-weight-bold">
                      {{ row.balance.toLocaleString('en-LK') }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- TARGET VS ACHIEVEMENT -->
        <div v-else-if="reportCategory === 'target'" class="card card-default report-card">
          <div class="card-header border-0 d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">
              <i class="fas fa-bullseye mr-1"></i> Target vs Achievement
            </h3>
            <span class="badge badge-light small">
              {{ targetRows.length }} employees
            </span>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover mb-0">
                <thead class="thead-light">
                  <tr>
                    <th>#</th>
                    <th>Rep</th>
                    <th>Month</th>
                    <th class="text-right">Target (LKR)</th>
                    <th class="text-right">Achieved (LKR)</th>
                    <th class="text-right">Progress</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="row in targetRows" :key="row.id">
                    <td>{{ row.id }}</td>
                    <td>{{ row.rep }}</td>
                    <td>{{ row.month }}</td>
                    <td class="text-right">
                      {{ row.target.toLocaleString('en-LK') }}
                    </td>
                    <td class="text-right">
                      {{ row.achieved.toLocaleString('en-LK') }}
                    </td>
                    <td class="text-right">
                      <div class="progress progress-xs">
                        <div
                          class="progress-bar bg-success"
                          role="progressbar"
                          :style="{ width: getTargetProgress(row) + '%' }"
                        />
                      </div>
                      <span class="small text-muted">
                        {{ getTargetProgress(row) }}%
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

      </div>
    </section>
  </AdminLayout>
</template>

<style scoped>
.reports-header {
  background: linear-gradient(120deg, #111827, #1f2937);
  color: #fff;
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

/* Responsive */
@media (max-width: 767.98px) {
  .reports-header {
    border-radius: 0 0 1rem 1rem;
  }
}
</style>
