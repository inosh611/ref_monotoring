<script setup lang="ts">
import { ref, watch, onMounted, computed } from "vue";
import "@bhplugin/vue3-datatable/dist/style.css";
import $ from "jquery";
import { useToast } from "vue-toastification";
import axios from "axios";

const toast = useToast();
const table_columns = [
    { field: "dealer.business_name", title: "Dealer Name", isUnique: true },
    { field: "dealer.business_address", title: "Address" },
    { field: "dealer.business_tel", title: "Contact number" },
    { field: "dealer.business_tel", title: "Order Number" },
    { field: "user.reg_number", title: "Item name" },
    {
        field: "quantity",
        title: "Quantity",
        cellRenderer: (row) => {
            const quantity = row.quantity || 0;
            if (quantity === 0) {
                return `<span class="badge badge-danger p-2">${quantity}</span>`;
            } else {
                return `<span class="badge badge-success p-2">${quantity}</span>`;
            }
        },
    },
    { field: "date", title: "Last Update Date" },
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
const selected_dealer = ref("all");
const selected_order_number = ref("all");
const selected_quantity_status = ref("all");
const orderKey = ref("");
const orderResults = ref([]);
const stock_items = ref([]);
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
            selected_order_number: selected_order_number.value,
            selected_quantity_status: selected_quantity_status.value,
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
        selected_order_number: selected_order_number.value,
        selected_quantity_status: selected_quantity_status.value,
    });

    // IMPORTANT: use the export route (not the fetch_url)
    window.location.href = `/admin/report/stock/export?${params.toString()}`;
};

const searchOrder = async (val) => {
    if (val == "") {
        orderResults.value = [];
        return;
    }
    try {
        const { data } = await axios.get(route("order.search"), {
            params: { search: val, dealer_id: selected_dealer.value },
        });
        orderResults.value = data.results;
        console.log("Order Details : ", orderResults.value);
    } catch (e) {
        console.error(e);
        return [];
    }
};

const selectOrder = async (order_id) => {
    orderResults.value = [];
    try {
        const { data } = await axios.get(route("dealer.stock.search"), {
            params: { order_id: order_id, dealer_id: selected_dealer.value },
        });
        selected_order_number.value = order_id;
        stock_items.value = data.results.map((item) => ({
            id: item.id,
            order_number: item.order.order_number,
            product_name: item.item.product.product_name,
            ordered_quantity: item.item.quantity,
            stock_quantity: item.quantity,
            unit_name: item.item.product.unit.unit_name,
            audit_count: null,
        }));
    } catch (e) {
        console.error(e);
        return [];
    }
};
watch(orderKey, (newVal) => {
    if (newVal.length > 2) {
        const data = searchOrder(newVal);
    }
});

const filteredStockItems = computed(() => {
    if (selected_quantity_status.value === "empty") {
        return stock_items.value.filter(
            (item) => Number(item.stock_quantity) === 0
        );
    }

    if (selected_quantity_status.value === "none-empty") {
        return stock_items.value.filter(
            (item) => Number(item.stock_quantity) > 0
        );
    }

    return stock_items.value; // all
});

onMounted(fetchProducts);

defineExpose({
    reload: fetchProducts,
});
</script>

<template>
    <div class="container-fluid">
        <div class="row mt-3">
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
                <div class="form-group">
                    <label for="status">Quantity Status</label>
                    <select
                        class="form-control"
                        id="selected_quantity_status"
                        v-model="selected_quantity_status"
                    >
                        <option value="all">All</option>
                        <option value="empty">Empty</option>
                        <option value="none-empty">Not Empty</option>
                    </select>
                </div>
            </div>
            <div class="col-2">
                <div class="form-group">
                    <label for="status">Order Number</label>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group dealer-search-form">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span
                                            class="input-group-text"
                                            id="basic-addon1"
                                            ><svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                height="24px"
                                                viewBox="0 -960 960 960"
                                                width="24px"
                                                fill="#e3e3e3"
                                            >
                                                <path
                                                    d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"
                                                />
                                            </svg>
                                        </span>
                                    </div>
                                    <input
                                        type="text"
                                        class="form-control"
                                        placeholder="Search Order"
                                        v-model="orderKey"
                                        :disabled="
                                            !selected_dealer ||
                                            selected_dealer === 'all'
                                        "
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="col-12" v-if="orderResults.length > 0">
                            <div class="row result-row">
                                <div class="col-12 d-search-result-box">
                                    <ul>
                                        <li
                                            v-for="(
                                                order, index
                                            ) in orderResults"
                                            :key="index"
                                            @click="selectOrder(order.id)"
                                        >
                                            {{ order.order_number }}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="row d-flex align-items-end w-100">
                    <div class="col-12 d-flex justify-content-end pt-4">
                        <button
                            class="btn btn-primary mr-2"
                            @click="createFilter"
                        >
                            Filter
                        </button>
                        <button class="btn btn-success" @click="downloadExcel">
                            Download Report
                        </button>
                    </div>
                </div>
            </div>
            <div class="row w-100">
                <div class="col-12 mt-4">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Order Name</th>
                                <th scope="col">Item Name</th>
                                <th scope="col">Order Quantity</th>
                                <th scope="col">Stock Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(item, index) in filteredStockItems"
                                :key="item.id"
                            >
                                <th scope="row">{{ index + 1 }}</th>
                                <td>{{ item.order_number }}</td>
                                <td>{{ item.product_name }}</td>
                                <td>
                                    {{ item.ordered_quantity }} ({{
                                        item.unit_name
                                    }})
                                </td>
                                <td>
                                    <span
                                        :class="[
                                            'badge',
                                            item.stock_quantity === 0
                                                ? 'badge-danger'
                                                : 'badge-success',
                                        ]"
                                    >
                                        {{ item.stock_quantity }} ({{
                                            item.unit_name
                                        }})
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.container {
    max-width: 900px;
}
canvas {
    height: 400px !important;
}
.table-trash-icon {
    cursor: pointer;
}
.table-card-body {
    overflow-x: scroll;
}
.table-card-body {
    max-height: 400px;
    overflow-y: scroll;
}
.d-search-result-box {
    max-height: 300px;
    overflow-y: scroll;
    border: 1px solid #ced4da;
    border-radius: 5px;
    position: absolute;
    z-index: 1;
    width: 98%;
    background: rgb(253 253 253);
}
ul {
    list-style-type: none;
    padding: 0;
}

ul li {
    padding: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
}
Ul li:hover {
    background: #ced4da;
    cursor: pointer;
}
.item-search-box {
    margin-bottom: 0;
}
/* .d-search-result-box{
    height: 300px;
    background-color: #f7f7f7;
} */
.dealer-search-form {
    margin: 0 !important;
}
@media (max-width: 900px) {
    .item-add-btn {
        width: 100%;
    }
    /* .col-12 {
        padding-left: 0 !important;
        padding-right: 0 !important;
    } */
}
.result-row {
    padding: 0px 15px;
}
</style>
