<script setup>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue";
import DataTable from "@/Components/Admin/DataTable.vue";
import { Head } from "@inertiajs/vue3";
import { onMounted, ref, computed, watch } from "vue";
import { useToast } from "vue-toastification";
import { store, searchData, update } from "../../../main";

const toast = useToast();
const formRef = ref(null);
const dealerKey = ref("");
const orderKey = ref("");
const dealerResults = ref([]);
const orderResults = ref([]);
const dealer_name = ref("--");
const dealer_id = ref(null);
const dealer_address = ref("--");
const dealer_phone = ref("--");
const stock_items = ref([]);
const orders = ref([]);

async function handleSubmit() {
    const form = formRef.value;
    if (form.checkValidity() === false) {
        toast.error("Please fill out all required fields.");
        form.classList.add("was-validated");
        return;
    }
    const formData = new FormData();
    // formData.append("collection_type", collection_type.value);

    store("my.collection.store", formData);
}
const searchOrderData = async (route_name, searchKey, dealer_id) => {
    try {
        const { data } = await axios.get(route(route_name), {
            params: { search: searchKey, shop_id: dealer_id },
        });
        return data;
    } catch (e) {
        console.error(e);
        return [];
    }
};

const searchDealer = async (val) => {
    if (val == "") {
        dealerResults.value = [];
        return;
    }
    console.log("Searching for items matching:", val);
    const data = await searchData("dealer.search", val);
    dealerResults.value = data;
};
const searchOrder = async (val) => {
    if (val == "") {
        orderResults.value = [];
        return;
    }
    try {
        const { data } = await axios.get(route("order.search"), {
            params: { search: val, dealer_id: dealer_id.value },
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
            params: { order_id: order_id, dealer_id: dealer_id.value },
        });
        stock_items.value = data.results.map(item => ({
            id: item.id,
            order_number: item.order.order_number,
            product_name: item.item.product.product_name,
            ordered_quantity: item.item.quantity,
            stock_quantity: item.quantity,
            unit_name: item.item.product.unit.unit_name,
            audit_count : null
        }));
    } catch (e) {
        console.error(e);
        return [];
    }
};

const saveAllAuditCounts = async () => {
    const payload = stock_items.value.map(item => ({
        id: item.id,
        quantity: item.audit_count
    }));

    const formData = new FormData();
    formData.append("items", JSON.stringify(payload));
    update("dealer.stock.update", formData);
};
watch(dealerKey, (newVal) => {
    dealer_id.value = null;
    dealer_name.value = "--";
    dealer_address.value = "--";
    dealer_phone.value = "--";
    dealerResults.value = [];
    if (newVal.length > 2) {
        const data = searchDealer(newVal);
    }
});
watch(orderKey, (newVal) => {
    dealerResults.value = [];
    if (newVal.length > 2) {
        const data = searchOrder(newVal);
    }
});

// watch(search_order, (newVal) => {
//     if (newVal.length > 2) {
//          const data = searchOrder("products.search", val);
//     }
// });
onMounted(() => {});
</script>

<template>
    <AdminLayout>
        <Head title="Order-Management" />
        <!-- Dashboard Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4 class="m-0 text-uppercase">Stock Audit</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <a :href="route('dashboard')">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active">
                                 Stock Audit
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dashboard Content -->
        <section class="content">
            <div class="container-fluid">
                <div class="col-12">
                    <div class="card card-default">
                        <div class="card-body able-card-body">
                            <div class="row mt-2 mml-2">
                                <h4>Stock Details</h4>
                            </div>
                            <!-- <form
                                class="needs-validation"
                                novalidate
                                @submit.prevent="handleSubmit"
                                ref="formRef"
                            > -->
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
                                                placeholder="Search Dealer Name"
                                                aria-label="Dealer Name"
                                                v-model="dealerKey"
                                            />
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="col-12"
                                    v-if="dealerResults.length > 0"
                                >
                                    <div class="row result-row">
                                        <div class="col-12 d-search-result-box">
                                            <ul>
                                                <li
                                                    v-for="(
                                                        dealer, index
                                                    ) in dealerResults"
                                                    :key="index"
                                                    @click="
                                                        dealer_name =
                                                            dealer.business_name;
                                                        dealer_id = dealer.id;
                                                        dealer_address =
                                                            dealer.business_address;
                                                        dealer_phone =
                                                            dealer.business_tel;
                                                        dealerResults = [];
                                                    "
                                                >
                                                    {{ dealer.business_name }}
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 mt-4">
                                    <table class="table table-striped">
                                        <tbody>
                                            <tr>
                                                <td>Dealer Name</td>
                                                <td>{{ dealer_name }}</td>
                                            </tr>
                                            <tr>
                                                <td>Dealer Address</td>
                                                <td>{{ dealer_address }}</td>
                                            </tr>
                                            <tr>
                                                <td>Dealer Tel</td>
                                                <td>{{ dealer_phone }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- <div class="col-lg-6 col-12">
                                    <div class="form-group">
                                        <label for="order-search"
                                            >Order Search</label
                                        >
                                        <input
                                            type="text"
                                            class="form-control"
                                            id="order-search"
                                            placeholder="Search Order"
                                            v-model="search_order"
                                            autocomplete="off"
                                        />
                                    </div>
                                </div> -->
                            </div>
                            <!-- </form> -->
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card card-default">
                        <div class="card-body able-card-body">
                            <div class="row mt-2 mml-2">
                                <h4>Order Items Current Stock</h4>
                            </div>
                            <!-- <form
                                class="needs-validation"
                                novalidate
                                @submit.prevent="handleSubmit"
                                ref="formRef"
                            > -->
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
                                                :disabled="!dealer_id"
                                            />
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="col-12"
                                    v-if="orderResults.length > 0"
                                >
                                    <div class="row result-row">
                                        <div class="col-12 d-search-result-box">
                                            <ul>
                                                <li
                                                    v-for="(
                                                        order, index
                                                    ) in orderResults"
                                                    :key="index"
                                                    @click="
                                                        selectOrder(order.id)
                                                    "
                                                >
                                                    {{ order.order_number }}
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 mt-4">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Order Name</th>
                                                <th scope="col">Item Name</th>
                                                <th scope="col">
                                                    Order Quantity
                                                </th>
                                                <th scope="col">
                                                    Stock Quantity
                                                </th>
                                                <th scope="col">
                                                    Audit Amount
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr
                                                v-for="(
                                                    item, index
                                                ) in stock_items"
                                            >
                                                <th scope="row">
                                                    {{ index + 1 }}
                                                </th>
                                                <td>
                                                    {{
                                                        item.order_number
                                                    }}
                                                </td>
                                                <td>
                                                    {{
                                                        item.product_name
                                                    }}
                                                </td>
                                                <td>
                                                    {{ item.ordered_quantity }} ({{ item.unit_name }})
                                                </td>
                                                <td>
                                                   {{ item.stock_quantity }} ({{ item.unit_name }})
                                                </td>
                                                <td>
                                                    <div class="form-group">
                                                        <input
                                                            type="number"
                                                            class="form-control"
                                                            id="exampleInputEmail1"
                                                            aria-describedby="emailHelp"
                                                            v-model="stock_items[index].audit_count"
                                                        />
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <div class="row">
                                        <div class="col-12 d-flex justify-content-end">
                                            <button class="btn btn-primary" @click="saveAllAuditCounts">UPDATE</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- </form> -->
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </AdminLayout>
</template>

<style scoped>
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
