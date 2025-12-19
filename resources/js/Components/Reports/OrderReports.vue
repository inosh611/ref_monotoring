<script setup lang="ts">
import { ref, watch, onMounted } from "vue";
import Vue3Datatable from "@bhplugin/vue3-datatable";
import "@bhplugin/vue3-datatable/dist/style.css";
import $ from "jquery";
import { useToast } from "vue-toastification";
import axios from "axios";

const toast = useToast();
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
const modal_data = ref(<any | null>null);
const selected_dealer = ref("all");
const selected_employee = ref("all");
const selected_order_status = ref("all");
const start_date = ref("");
const end_date = ref("");
const selected_payment_status = ref("all");
const params = ref({
    sort_column: "id",
    sort_direction: "asc",
});

axios.defaults.headers.common["X-CSRF-TOKEN"] =
    document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content") || "";
axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

const table_columns = [
    { field: "order_number", title: "Order Number", isUnique: true },

    { field: "shop.business_name", title: "Shop Name" },
    { field: "shop.business_address", title: "Shop Address" },
    { field: "shop.business_tel", title: "Shop Contact" },

    { field: "user.reg_number", title: "Ref Reg" },
    { field: "user.first_name", title: "Ref Name" },

    { field: "total_price", title: "Total Price" },
    { field: "paid_amount", title: "Paid Amount" },

    {
        field: "payment_status",
        title: "Payment Status",
        cellRenderer: (row) => {
            const total = Number(row.total_price ?? 0);
            const paid = Number(row.paid_amount ?? 0);

            // protect against weird data
            if (total <= 0) {
                return `<span class="badge badge-secondary p-2">N/A</span>`;
            }

            if (paid >= total) {
                return `<span class="badge badge-success p-2">Paid</span>`;
            }

            if (paid > 0 && paid < total) {
                return `<span class="badge badge-info p-2">Partially Paid</span>`;
            }

            return `<span class="badge badge-warning p-2">Pending</span>`;
        },
    },
    {
        field: "order_status",
        title: "Order Status",
        cellRenderer: (row) => {
            const status = (row.order_status || "").toLowerCase();

            if (status === "delivered" || status === "delevered") {
                return `<span class="badge badge-success p-2">Delivered</span>`;
            }

            if (status === "confirm" || status === "confirmed") {
                return `<span class="badge badge-info p-2">Confirmed</span>`;
            }

            // default
            return `<span class="badge badge-warning p-2">Pending</span>`;
        },
    },

    { field: "expected_order_date", title: "Expected Order Date" },
];
const cols = table_columns;

function formatToAmPm(timeString) {
    if (!timeString) return null;
    const [hour, minute] = timeString.split(":");
    let h = parseInt(hour);
    const ampm = h >= 12 ? "PM" : "AM";
    h = h % 12 || 12;
    return `${h}:${minute} ${ampm}`;
}

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
            selected_order_status: selected_order_status.value,
            selected_payment_status: selected_payment_status.value,
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
};
const downloadExcel = () => {
    const params = new URLSearchParams({
        selected_dealer: selected_dealer.value,
        selected_employee: selected_employee.value,
        selected_order_status: selected_order_status.value,
        selected_payment_status: selected_payment_status.value,
        start_date: start_date.value,
        end_date: end_date.value,
    });

    // IMPORTANT: use the export route (not the fetch_url)
    window.location.href = `/admin/report/order/export?${params.toString()}`;
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
            <div class="col-2">
                <div class="form-group">
                    <label for="status">Order Status</label>
                    <select
                        class="form-control"
                        id="status"
                        v-model="selected_order_status"
                    >
                        <option value="all">All</option>
                        <option value="Pending">Pending</option>
                        <option value="Confirm">Confirm</option>
                        <option value="Delivered">Delivered</option>
                    </select>
                </div>
            </div>
            <div class="col-2 p-0">
                <div class="form-group">
                    <label for="status">Payment Status</label>
                    <select
                        class="form-control"
                        id="status"
                        v-model="selected_payment_status"
                        ;
                    >
                        <option value="all">All</option>
                        <option value="done">Done</option>
                        <option value="pending">Pending</option>
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
</style>
