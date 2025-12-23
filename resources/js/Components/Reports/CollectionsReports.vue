<!-- <script setup lang="ts">
import { ref, watch, onMounted } from "vue";
import Vue3Datatable from "@bhplugin/vue3-datatable";
import "@bhplugin/vue3-datatable/dist/style.css";
import $ from "jquery";
import { useToast } from "vue-toastification";
import axios from "axios";

function formatToAmPm(timeString) {
    if (!timeString) return null;

    const [hour, minute] = timeString.split(":");
    let h = parseInt(hour);
    const ampm = h >= 12 ? "PM" : "AM";
    h = h % 12 || 12;

    return `${h}:${minute} ${ampm}`;
}

const toast = useToast();
const table_columns = [
    { field: "dealer.business_name", title: "Dealer Name", isUnique: true },
    { field: "dealer.business_address", title: "Address" },
    { field: "dealer.business_tel", title: "Contact number" },
    { field: "user.reg_number", title: "Ref Reg No" },
    { field: "user.first_name", title: "Ref Name" },

    { field: "date", title: "Date" },
    {
        field: "time",
        title: "Check In Time",
        cellRenderer: (row) => 
            row.time
                ? `<span class="badge badge-success p-2">
                    ${formatToAmPm(row.time)}</span>`
                : `<span class="badge badge-danger p-2">
                    Not yet</span>`, 
        
    },
    {
        field: "checkout_time",
        title: "Check Out Time",
        cellRenderer: (row) => 
            row.check_out_time
                ? `<span class="badge badge-success p-2">
                    ${formatToAmPm(row.check_out_time)}</span>`
                : `<span class="badge badge-danger p-2">
                    Not yet</span>`, 
        
        },
];

const emit = defineEmits(["edit-item", "change-price", "change-order-status"]);
const props = defineProps<{
    dealers: Array<any>;
    employees: Array<any>;
    fetch_url: string;
    columns: Array<any>;
}>();

const products = ref([]);
const total = ref(0);
const page = ref(1);
const perPage = ref(10);
const search = ref("");
const loading = ref(false);
const cols = table_columns;
const modal_data = ref(<any | null>null);
const selected_dealer = ref("all");
const selected_employee = ref("all");
const selected_status = ref("all");
const start_date = ref("");
const end_date = ref("");

const params = ref({
    sort_column: "id",
    sort_direction: "asc",
});

axios.defaults.headers.common["X-CSRF-TOKEN"] =
    document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content") || "";
axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

const handleSortChange = (sortData: {
    sortColumn: string;
    sortDirection: string;
}) => {
    params.value.sort_column = sortData.sortColumn;
    params.value.sort_direction = sortData.sortDirection;
    fetchProducts(); // Refetch data with new sorting
};

const fetchProducts = async () => {
    loading.value = true;
    try {
        const response = await axios.post(props.fetch_url, {
            page: page.value,
            per_page: perPage.value,
            search: search.value,
            selected_dealer: selected_dealer.value,
            selected_employee: selected_employee.value,
            selected_status: selected_status.value,
            start_date: start_date.value,
            end_date: end_date.value,
            sort_column: params.value.sort_column,
            sort_direction: params.value.sort_direction,
        });

        products.value = response.data.data;
        total.value = response.data.total;
    } catch (error: any) {
        toast.error(error.response?.data?.message || error.message);
    } finally {
        loading.value = false;
    }
};

const handlePageChange = (newPage: number) => {
    page.value = newPage;
    fetchProducts();
};

const handlePageSizeChange = (newSize: number) => {
    perPage.value = newSize;
    page.value = 1;
    fetchProducts();
};

watch(search, () => {
    page.value = 1;
    fetchProducts();
});

const createFilter = () => {
    console.log("Clicking");
    fetchProducts();
}
const downloadExcel = () => {
  const params = new URLSearchParams({
    selected_dealer: selected_dealer.value,
    selected_employee: selected_employee.value,
    selected_status: selected_status.value,
    start_date: start_date.value ?? "",
    end_date: end_date.value ?? "",
  });

  // IMPORTANT: use the export route (not the fetch_url)
  window.location.href = `/admin/report/visiting/export?${params.toString()}`;
};



onMounted(fetchProducts);

defineExpose({
    reload: fetchProducts,
});
</script>

<template>
    <div class="container-fluid">
        <div class="row mt-3">
            <div class="row"></div>
            <div class="col-2">
                <div class="form-group">
                    <label for="employee">Select Employee</label>
                    <select
                        class="form-control"
                        id="employee"
                        v-model="selected_employee"
                    >
                        <option value="all">All</option>
                        <option
                            v-for="employee in props.employees"
                            :value="employee.id"
                        >
                            {{ employee.reg_number }} -
                            {{ employee.first_name }}
                        </option>
                    </select>
                </div>
            </div>
            <div class="col-4 ml-2">
                <div class="form-group">
                    <label for="dealers">Select Dealers</label>
                    <select
                        class="form-control"
                        id="dealers"
                        v-model="selected_dealer"
                    >
                        <option value="all">All</option>
                        <option
                            v-for="dealers in props.dealers"
                            :value="dealers.id"
                        >
                            {{ dealers.business_name }} -
                            {{ dealers.business_address }}
                        </option>
                    </select>
                </div>
            </div>
            <div class="col-2">
                <label for="start-date">Start Date</label>
                <input
                    v-model="start_date"
                    type="date"
                    class="form-control mb-3"
                    id="start-date"
                />
            </div>
            <div class="col-2">
                <label for="end-date">End Date</label>
                <input
                    v-model="end_date"
                    type="date"
                    class="form-control mb-3"
                    id="end-date"
                />
            </div>
            
            <div class="col-2">
                <div class="form-group">
                    <label for="status">Status</label>
                    <select
                        class="form-control"
                        id="status"
                        v-model="selected_status"
                    >
                        <option value="all">All</option>
                        <option value="pending">Pending</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
            </div>
            <div class="row d-flex align-items-end w-100 mb-4">
                <div class="col-12 d-flex justify-content-end">
                    <button class="btn btn-primary mr-2" @click="createFilter">
                        Filter
                    </button>
                    <button class="btn btn-success" @click="downloadExcel">
                        Download Report
                    </button>
                </div>
            </div>

        </div>
        <vue3-datatable
            :rows="products"
            :columns="cols"
            :totalRows="total"
            :currentPage="page"
            :pageSize="perPage"
            :isServerMode="true"
            :loading="loading"
            :sortable="true"
            :sortColumn="params.sort_column"
            :sortDirection="params.sort_direction"
            @change="handlePageChange"
            @page-size-change="handlePageSizeChange"
            @sort="handleSortChange"
            skin="bh-table-hover"
        >
            <template #loading>
                <div class="text-center py-4">Loading...</div>
            </template>
        </vue3-datatable>
    </div>
</template>

<style scoped>
.container {
    max-width: 900px;
}
</style> -->
<script setup lang="ts">
import { ref, watch, onMounted } from "vue";
import Vue3Datatable from "@bhplugin/vue3-datatable";
import "@bhplugin/vue3-datatable/dist/style.css";
import axios from "axios";
import { useToast } from "vue-toastification";

const toast = useToast();

axios.defaults.headers.common["X-CSRF-TOKEN"] =
  document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "";
axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

const props = defineProps<{
  // You already pass these from Reports page
  employees: Array<any>;
  dealers: Array<any>;     // using as shop list (id, business_name, business_address)
  fetch_url: string;       // "/admin/report/collection"
}>();

/** Datatable columns (adjust titles if needed) */
const table_columns = [
  { field: "id", title: "Payment ID", isUnique: true },
  { field: "order.order_number", title: "Order No" },
  { field: "order.shop.business_name", title: "Shop" },

  { field: "user.reg_number", title: "Collector Reg No" },
  {
    field: "user.first_name",
    title: "Collector Name",
    cellRenderer: (row: any) => {
      const fn = row?.user?.first_name ?? "";
      const ln = row?.user?.last_name ?? "";
      return `${fn} ${ln}`.trim() || "-";
    },
  },
  {
    field: "collection_type",
    title: "Type",
    cellRenderer: (row: any) => {
      const type = (row?.collection_type ?? "").toLowerCase();
      if (type === "cash") return `<span class="badge badge-success p-2">Cash</span>`;
      if (type === "cheque") return `<span class="badge badge-warning p-2">Cheque</span>`;
      return `<span class="badge badge-light p-2">${row?.collection_type ?? "-"}</span>`;
    },
  },
  { field: "paid_amount", title: "Amount" },

  {
    field: "cash.cash_receipt_number",
    title: "Cash Receipt",
    cellRenderer: (row: any) => row?.cash?.cash_receipt_number ?? "-",
  },
  {
    field: "cheque.cheque_number",
    title: "Cheque No",
    cellRenderer: (row: any) => row?.cheque?.cheque_number ?? "-",
  },
  {
    field: "cheque.bank",
    title: "Bank",
    cellRenderer: (row: any) => row?.cheque?.bank ?? "-",
  },
  {
    field: "created_at",
    title: "Paid Date",
    cellRenderer: (row: any) => (row?.created_at ? row.created_at.substring(0, 10) : "-"),
  },
];

const rows = ref<any[]>([]);
const total = ref(0);
const page = ref(1);
const perPage = ref(10);
const search = ref("");
const loading = ref(false);

const selected_shop = ref("all");
const selected_employee = ref("all");
const selected_collection_type = ref("all");
const start_date = ref("");
const end_date = ref("");

const params = ref({
  sort_column: "id",
  sort_direction: "desc",
});

const fetchReport = async () => {
  loading.value = true;
  try {
    const response = await axios.post(props.fetch_url, {
      page: page.value,
      per_page: perPage.value,
      search: search.value,

      selected_shop: selected_shop.value,
      selected_employee: selected_employee.value,
      selected_collection_type: selected_collection_type.value,
      start_date: start_date.value,
      end_date: end_date.value,

      sort_column: params.value.sort_column,
      sort_direction: params.value.sort_direction,
    });

    rows.value = response.data.data;
    total.value = response.data.total;
  } catch (error: any) {
    toast.error(error.response?.data?.message || error.message);
  } finally {
    loading.value = false;
  }
};

// Sorting from datatable
const handleSortChange = (sortData: { sortColumn: string; sortDirection: string }) => {
  params.value.sort_column = sortData.sortColumn;
  params.value.sort_direction = sortData.sortDirection;
  page.value = 1;
  fetchReport();
};

const handlePageChange = (newPage: number) => {
  page.value = newPage;
  fetchReport();
};

const handlePageSizeChange = (newSize: number) => {
  perPage.value = newSize;
  page.value = 1;
  fetchReport();
};

// ✅ Filter button (important: reset page)
const applyFilter = () => {
  page.value = 1;
  fetchReport();
};

// Auto-search
watch(search, () => {
  page.value = 1;
  fetchReport();
});

// Optional: reset page when any filter changes (even without clicking Filter)
// If you prefer only Filter button, remove this watch block.
watch([selected_shop, selected_employee, selected_collection_type, start_date, end_date], () => {
  page.value = 1;
});

const resetFilters = () => {
  selected_shop.value = "all";
  selected_employee.value = "all";
  selected_collection_type.value = "all";
  start_date.value = "";
  end_date.value = "";
  search.value = "";
  page.value = 1;
  fetchReport();
};

onMounted(fetchReport);

// Optional export (if you created export route)
const downloadExcel = () => {
  const q = new URLSearchParams({
    selected_shop: selected_shop.value,
    selected_employee: selected_employee.value,
    selected_collection_type: selected_collection_type.value,
    start_date: start_date.value ?? "",
    end_date: end_date.value ?? "",
    search: search.value ?? "",
    sort_column: params.value.sort_column,
    sort_direction: params.value.sort_direction,
  });

  window.location.href = `/admin/report/collection/export?${q.toString()}`;
};
</script>

<template>
  <div class="container-fluid">
    <div class="row mt-3">
      <!-- Employee -->
      <div class="col-md-3 col-12">
        <div class="form-group">
          <label>Select Employee</label>
          <select class="form-control" v-model="selected_employee">
            <option value="all">All</option>
            <option v-for="e in props.employees" :key="e.id" :value="e.id">
              {{ e.reg_number }} - {{ e.first_name }}
            </option>
          </select>
        </div>
      </div>

      <!-- Shop -->
      <div class="col-md-4 col-12">
        <div class="form-group">
          <label>Select Shop</label>
          <select class="form-control" v-model="selected_shop">
            <option value="all">All</option>
            <option v-for="s in props.dealers" :key="s.id" :value="s.id">
              {{ s.business_name }} - {{ s.business_address }}
            </option>
          </select>
        </div>
      </div>

      <!-- Type -->
      <div class="col-md-2 col-12">
        <div class="form-group">
          <label>Collection Type</label>
          <select class="form-control" v-model="selected_collection_type">
            <option value="all">All</option>
            <option value="cash">Cash</option>
            <option value="cheque">Cheque</option>
          </select>
        </div>
      </div>

      <!-- Dates -->
      <div class="col-md-2 col-6">
        <div class="form-group">
          <label>Start Date</label>
          <input v-model="start_date" type="date" class="form-control" />
        </div>
      </div>

      <div class="col-md-2 col-6">
        <div class="form-group">
          <label>End Date</label>
          <input v-model="end_date" type="date" class="form-control" />
        </div>
      </div>

      <!-- Actions -->
      <div class="col-12 d-flex justify-content-end mb-2">
        <button class="btn btn-primary mr-2" @click="applyFilter">Filter</button>
        <button class="btn btn-secondary mr-2" @click="resetFilters">Reset</button>
        <button class="btn btn-success" @click="downloadExcel">Download</button>
      </div>

      <!-- Search -->
      <div class="col-12 mb-3">
        <input
          v-model="search"
          type="text"
          class="form-control"
          placeholder="Search by order no, shop, employee, receipt, cheque..."
        />
      </div>
    </div>

    <vue3-datatable
      :rows="rows"
      :columns="table_columns"
      :totalRows="total"
      :currentPage="page"
      :pageSize="perPage"
      :isServerMode="true"
      :loading="loading"
      :sortable="true"
      :sortColumn="params.sort_column"
      :sortDirection="params.sort_direction"
      @change="handlePageChange"
      @page-size-change="handlePageSizeChange"
      @sort="handleSortChange"
      skin="bh-table-hover"
    >
      <template #loading>
        <div class="text-center py-4">Loading...</div>
      </template>
    </vue3-datatable>
  </div>
</template>

<style scoped>
.badge-success {
  background-color: #22c55e;
}
.badge-warning {
  background-color: #f59e0b;
}
.badge-light {
  background-color: #e5e7eb;
  color: #374151;
}
</style>
