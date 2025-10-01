<script setup>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue";
import DataTable from "@/Components/Admin/DataTable.vue";
import { Head } from "@inertiajs/vue3";
import { onMounted, ref, watch, nextTick  } from "vue";
import { useToast } from "vue-toastification";
import { store, searchData } from "../../../main";
import $ from "jquery";

const toast = useToast();
const itemAddFormRef = ref(null);
const itemName = ref("");
const itemQuantity = ref("");
const itemList = ref([]);
const dealer_id = ref("");
const user_details = ref({});
const itemSearchResults = ref([]);
const searchWrapRef = ref(null);
const selectedProduct = ref(null);
const searchStatus = ref(true);
const latest_price = ref([]);
const product_prices = ref([]);
const selected_price = ref("");
const selectedProduct_name = ref("");
const selected_product_price = ref([]);

const props = defineProps({
    dealers: {
        type: Array,
        required: true,
    },
    user: {
        type: Object,
        required: true,
    },
});

user_details.value = props.user.first_name + " " + props.user.last_name;

const addItemToList = () => {
    const form = itemAddFormRef.value;
    if (form.checkValidity() === false) {
        toast.error("Please fill out all required fields.");
        form.classList.add("was-validated");
        return;
    }

    itemList.value.push({
        product_id: selectedProduct.value.id,
        name: selectedProduct.value.product_name,
        quantity: itemQuantity.value,
        price_id: selected_product_price.value.id,
        price: selected_product_price.value.price,
        sub_total: itemQuantity.value * selected_product_price.value.price,
    });
    itemName.value = "";
    itemQuantity.value = "";
    // Use nextTick to ensure DOM updates
    nextTick(() => {
        form.classList.remove("was-validated");
    });

    console.log("Class List:", form.classList);
};

const submitOrder = () => {
    if (itemList.value.length === 0) {
        toast.error("Please add at least one item to the order.");
        return;
    } else {
        const formData = new FormData();

        formData.append("item_list", JSON.stringify(itemList.value));
        formData.append("dealer_id", dealer_id.value);
        formData.append("user_id", props.user.id);
        formData.append("order_status", "Pending");

        store("order.store", formData);
    }
};
const searchItem = async (val) => {
    if (val == "") {
        itemSearchResults.value = [];
        return;
    }
    // Logic for searching items can be implemented here
    console.log("Searching for items matching:", val);
    const data = await searchData("products.search", val);
    itemSearchResults.value = data.results;
};
const closeResults = () => {
    itemSearchResults.value = [];
};

const addProduct = (product) => {
    searchStatus.value = false;
    itemName.value = product.product_name;
    selectedProduct_name.value = product.product_name;
    selectedProduct.value = product;
    itemSearchResults.value = [];
    console.log("Selected product Latest Price:", product.latest_price);
    console.log("Selected product  Prices:", product.product_price);
    console.log(
        "Selected product  Prices length:",
        product.product_price.length
    );

    if (product.product_price.length > 0) {
        latest_price.value = product.latest_price;
        product_prices.value = product.product_price;
        $("#price-changing").modal("show");
    }
};

const submitPrice = (price_id) => {
    if (price_id == "") {
        toast.error("Please select a price.");
        return;
    }
    $("#price-changing").modal("hide");

    const id = Number(price_id);
    const list = selectedProduct.value?.product_price ?? [];
    const chosen = list.find((p) => Number(p.id) === id);

    if (!chosen) {
        toast.error("Selected price not found.");
        return;
    }
    selected_product_price.value = chosen;
    console.log("Selected product with price:", chosen);
};
document.addEventListener("click", (event) => {
    if (searchWrapRef.value && !searchWrapRef.value.contains(event.target)) {
        closeResults();
    }
});

watch(itemName, (newVal, oldVal) => {
    if (searchStatus.value) {
        searchItem(newVal);
    } else if (selectedProduct_name.value !== newVal) {
        searchStatus.value = true;
        searchItem(newVal);
    }
});

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
                        <h4 class="m-0">CREATE ORDERS</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <a :href="route('dashboard')">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active">
                                Order Management
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dashboard Content -->
        <section class="content">
            <div class="container-fluid">
                <form
                    class="needs-validation"
                    novalidate
                    @submit.prevent="addItemToList"
                    ref="itemAddFormRef"
                >
                    <div class="col-12">
                        <div class="card card-default">
                            <div class="card-body">
                                <h5 class="w-75 mb-3 text-bold">
                                    CUSTOMER Details
                                    {{ itemList }}
                                </h5>

                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label
                                                for="exampleFormControlSelect1"
                                                >Select Dealer</label
                                            >
                                            <select
                                                class="form-control"
                                                id="exampleFormControlSelect1"
                                                v-model="dealer_id"
                                                required
                                            >
                                                <option value="">
                                                    Select Dealer
                                                </option>
                                                <option
                                                    v-for="dealer in dealers"
                                                    :value="dealer.id"
                                                >
                                                    {{ dealer.business_name }} -
                                                    {{
                                                        dealer.business_address
                                                    }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label
                                                for="exampleFormControlSelect1"
                                                >Ref Name</label
                                            >
                                            <input
                                                type="text"
                                                class="form-control"
                                                v-model="user_details"
                                                disabled
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card card-default">
                            <div class="card-body">
                                <h5 class="w-75 mb-3 text-bold text-uppercase">
                                    Add Item to order
                                </h5>
                                <div class="row">
                                    <div class="col-lg-4 col-12">
                                        <div class="row" ref="searchWrapRef">
                                            <div class="col-12">
                                                <div
                                                    class="form-group item-search-box"
                                                >
                                                    <label
                                                        for="exampleFormControlSelect1"
                                                        >Item Name</label
                                                    >
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        required
                                                        v-model="itemName"
                                                    />
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div
                                                    class="search-result-box"
                                                    v-if="
                                                        itemSearchResults.length
                                                    "
                                                >
                                                    <ul>
                                                        <li
                                                            v-for="result in itemSearchResults"
                                                            @click="
                                                                addProduct(
                                                                    result
                                                                )
                                                            "
                                                        >
                                                            {{
                                                                result.product_name
                                                            }}
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-12">
                                        <div class="form-group">
                                            <label
                                                for="exampleFormControlSelect1"
                                                >Quantity</label
                                            >
                                            <input
                                                type="number"
                                                class="form-control"
                                                required
                                                v-model="itemQuantity"
                                            />
                                        </div>
                                    </div>
                                    <div
                                        class="col-lg-4 col-12 d-flex align-items-center mt-3"
                                    >
                                        <button
                                            type="submit"
                                            class="btn btn-primary item-add-btn"
                                            :disabled="
                                                !itemName || !itemQuantity
                                            "
                                        >
                                            Add Item
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="col-12">
                    <div class="card card-default">
                        <div class="card-body able-card-body">
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="row mb-2">
                                        <div class="col-6">
                                            <h5
                                                class="w-75 mb-3 text-bold text-uppercase"
                                            >
                                                Order Details
                                            </h5>
                                        </div>
                                        <div
                                            class="col-6 d-flex justify-content-end"
                                        >
                                            <button
                                                class="btn btn-primary"
                                                @click="submitOrder"
                                                :disabled="!itemList.length"
                                            >
                                                Confirm Order
                                            </button>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <table class="table table-striped">
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Item Name</th>
                                                        <th>Price (LKR)</th>
                                                        <th>Quantity</th>
                                                        <th>Sub Total (LKR)</th>
                                                        <th>Action</th>
                                                    </tr>
                                                <tbody v-if="itemList.length">
                                                    <tr
                                                        v-for="(
                                                            item, index
                                                        ) in itemList"
                                                        :key="index"
                                                    >
                                                        <td>
                                                            {{ index + 1 }}
                                                        </td>
                                                        <td>
                                                            {{ item.name }}
                                                        </td>
                                                        <td>
                                                            {{ item.price }}
                                                        </td>
                                                        <td>
                                                            <input
                                                                type="number"
                                                                class="form-control"
                                                                v-model="
                                                                    itemList[
                                                                        index
                                                                    ].quantity
                                                                "
                                                            />
                                                        </td>
                                                        <td>{{ item.sub_total }}</td>

                                                        <td>
                                                            <svg
                                                                xmlns="http://www.w3.org/2000/svg"
                                                                height="24px"
                                                                viewBox="0 -960 960 960"
                                                                width="24px"
                                                                fill="#EA3323"
                                                                class="table-trash-icon"
                                                                @click="
                                                                    itemList.splice(
                                                                        index,
                                                                        1
                                                                    )
                                                                "
                                                            >
                                                                <path
                                                                    d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z"
                                                                />
                                                            </svg>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                                <div class="empty-order" v-else>
                                                    <P>No Item Yet</P>
                                                </div>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!--Price Modal -->
        <div
            class="modal fade"
            id="price-changing"
            data-backdrop="static"
            data-keyboard="false"
            tabindex="-1"
            aria-labelledby="price-changing-label"
            aria-hidden="true"
        >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="price-changing-label">
                            {{ selectedProduct_name }}
                        </h5>
                        <button
                            type="button"
                            class="close"
                            data-dismiss="modal"
                            aria-label="Close"
                        >
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="form-group">
                                <label for="latest-price"
                                    >Latest Price (LKR)
                                    {{ latest_price }}</label
                                >
                                <input
                                    type="email"
                                    class="form-control"
                                    id="latest-price"
                                    placeholder="name@example.com"
                                    v-model="latest_price.price"
                                    disabled
                                />
                            </div>
                            <div class="form-group">
                                <label for="exampleFormControlSelect1"
                                    >Select your Price (LKR)</label
                                >
                                <select
                                    class="form-control"
                                    id="exampleFormControlSelect1"
                                    v-model="selected_price"
                                >
                                    <option value="" selected disabled>
                                        Select Your Price
                                    </option>
                                    <option
                                        v-for="product_price in product_prices"
                                        :value="product_price.id"
                                    >
                                        {{ product_price.price }}
                                    </option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-danger text-uppercase"
                            data-dismiss="modal"
                        >
                            Close
                        </button>
                        <button
                            type="button"
                            class="btn btn-primary text-uppercase"
                            @click="
                                submitPrice(
                                    selected_price !== undefined &&
                                        selected_price !== null &&
                                        selected_price !== ''
                                        ? selected_price
                                        : latest_price?.id
                                )
                            "
                        >
                            submit
                        </button>
                    </div>
                </div>
            </div>
        </div>
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
.search-result-box {
    max-height: 300px;
    overflow-y: scroll;
    border: 1px solid #ced4da;
    border-radius: 5px;
    position: absolute;
    z-index: 1;
    width: 94%;
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
@media (max-width: 900px) {
    .item-add-btn {
        width: 100%;
    }
    /* .col-12 {
        padding-left: 0 !important;
        padding-right: 0 !important;
    } */
}
</style>
