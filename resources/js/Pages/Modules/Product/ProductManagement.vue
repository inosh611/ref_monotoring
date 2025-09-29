<script setup>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref } from "vue";
import { useToast } from "vue-toastification";
import { store } from "../../../main";
import DataTable from "@/Components/Admin/DataTable.vue";

const toast = useToast();
const productName = ref("");
const productUnit = ref("");
const productPrice = ref("");
const formRef = ref(null);
const editFormRef = ref(null);
const newPriceFormRef = ref(null);
const edit_product_name = ref("");
const edit_product_id = ref(null);
const edit_product_price = ref(null);
const edit_product_unit = ref(null);
const submitDisabled = ref(false);
const updateSubmitDisabled = ref(false);
const productPriceId = ref(null);
const newPrice = ref(null);
const currentPrice = ref(null);
const product_id = ref(null);
const newPriceSubmit = ref(false);

const props = defineProps({
    roles: Array,
    units: Array,
});

const product_table_columns = [
    { field: "row_num", title: "#", isUnique: true, width: "30px" },
    { field: "id", title: "ID", isUnique: true },
    { field: "product_name", title: "Product name", isUnique: true },
    { field: "unit.unit_name", title: "Unit name", isUnique: true },
    { field: "latest_price.price", title: "price" },
    { field: "actions", title: "Actions", cellRenderer: false, width: "50px" },
];

async function handleSubmit() {
    const form = formRef.value;
    if (form.checkValidity() === false) {
        toast.error("Please fill out all required fields.");
        form.classList.add("was-validated");
        return;
    }
    const formData = new FormData();

    formData.append("product_name", productName.value);
    formData.append("unit_id", productUnit.value);
    formData.append("price", productPrice.value);

    store("product.store", formData);
}

const openEditUnit = (row) => {
    console.log("Editing row:", row);
    edit_product_id.value = row.id;
    edit_product_name.value = row.product_name;
    edit_product_price.value = row.latest_price.price;
    edit_product_unit.value = row.unit_id;
    productPriceId.value = row.latest_price.id;
    submitDisabled.value = false;
};

const changePrice = (row) => {
    console.log("Change price row:", row.latest_price);
    productName.value = row.product_name;
    product_id.value = row.id;
    productPriceId.value = row.latest_price.id;
    currentPrice.value = row.latest_price.price;
    newPrice.value = null;
};
async function handleUpdate() {
    const form = editFormRef.value;
    if (form.checkValidity() === false) {
        toast.error("Please fill out all required fields.");
        form.classList.add("was-validated");
        return;
    }
    const formData = new FormData();

    formData.append("id", edit_product_id.value);
    formData.append("product_name", edit_product_name.value);
    formData.append("unit_id", edit_product_unit.value);
    formData.append("price", edit_product_price.value);
    formData.append("product_price_id", productPriceId.value);
    updateSubmitDisabled.value = true;
    store("product.update", formData);

    updateSubmitDisabled.value = false;
}
async function addNewPrice() {
    
    newPriceSubmit.value = true;
    const form = newPriceFormRef.value;
    if (form.checkValidity() === false) {
        newPriceSubmit.value = false;
        toast.error("Please fill out all required fields");
        form.classList.add("was-validated");
        return;
    }
    const formData = new FormData();

    formData.append("product_id", product_id.value);
    formData.append("price", newPrice.value);

    newPriceSubmit.value = true;
    store("product.price.store", formData);
}
const close = () => {
    newPriceSubmit.value = false;
    const form = newPriceFormRef.value;
    form.classList.remove("was-validated");
}
</script>

<template>
    <AdminLayout>
        <Head title="customer-Management" />

        <!-- Dashboard Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4 class="m-0 text-uppercase">Product Management</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <a :href="route('customer.index')">Product</a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dashboard Content -->
        <section class="content">
            <div class="container-fluid">
                <!-- DataTable Row -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card card-default">
                            <div class="card-body">
                                <h5 class="w-75 mb-3 text-bold">
                                    Product Details
                                </h5>
                                <form
                                    class="needs-validation"
                                    novalidate
                                    @submit.prevent="handleSubmit"
                                    ref="formRef"
                                >
                                    <div class="form-row">
                                        <div class="col-md-3 mb-3">
                                            <label for="product_name"
                                                >Product Name</label
                                            >
                                            <input
                                                type="text"
                                                class="form-control"
                                                id="product_name"
                                                required
                                                placeholder="Product Name"
                                                v-model="productName"
                                            />
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="form-group">
                                                <label for="unit_select"
                                                    >Select Unit</label
                                                >
                                                <select
                                                    class="form-control"
                                                    id="unit_select"
                                                    v-model="productUnit"
                                                    required
                                                >
                                                    <option
                                                        value=""
                                                        selected
                                                        disabled
                                                    >
                                                        Select Unit
                                                    </option>
                                                    <option
                                                        v-for="unit in units"
                                                        :value="unit.id"
                                                    >
                                                        {{ unit.unit_name }}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="customer_contact_number"
                                                >Price</label
                                            >
                                            <input
                                                type="number"
                                                class="form-control"
                                                id="price"
                                                required
                                                placeholder="Price"
                                                step="0.01"
                                                min="0"
                                                v-model="productPrice"
                                            />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div
                                            class="col-12 d-flex justify-content-end"
                                        >
                                            <button
                                                class="btn btn-primary"
                                                type="submit"
                                            >
                                                Create
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- DataTable Row -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card card-default">
                            <div class="card-body" style="padding: 0px">
                                <DataTable
                                    title="PRODUCT TABLE"
                                    fetch_url="/admin/product/data-table"
                                    :columns="product_table_columns"
                                    table_icon='<i class="nav-icon fas fa-archive" style="font-size: medium;"></i>'
                                    modal_title="Product"
                                    edit_route_name="product.edit"
                                    edit_modalId="#product-edit-modal"
                                    :edit_modal="true"
                                    delete_route_name="product.delete"
                                    view_button="false"
                                    @edit-item="openEditUnit"
                                    :use_new_price_button="true"
                                    @change-price="changePrice"
                                    price_modal_id="#change-price-modal"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <div
            class="modal fade"
            id="product-edit-modal"
            data-backdrop="static"
            data-keyboard="false"
            tabindex="-1"
            aria-labelledby="product-edit-modalLabel"
            aria-hidden="true"
        >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5
                            class="modal-title"
                            id="product-edit-modalTitleLabel"
                        >
                            Product Edit
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
                    <form
                        class="needs-validation"
                        novalidate
                        @submit.prevent="handleUpdate"
                        ref="editFormRef"
                    >
                        <div class="modal-body">
                            <div
                                class="row w-100 d-flex justify-content-center"
                            >
                                <div class="col-12">
                                    <div
                                        class="alert alert-warning w-100"
                                        role="alert"
                                    >
                                        ! Warning: Editing the price here will
                                        overwrite the product’s latest price and
                                        update items on older invoices. To add a
                                        new price without changing past
                                        invoices, use the “Change Price” button
                                        in the product table.
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-12 mb-3">
                                    <label for="edit_product_name"
                                        >Product Name</label
                                    >
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="edit_product_name"
                                        required
                                        placeholder="Product Name"
                                        v-model="edit_product_name"
                                    />
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-12 mb-3">
                                    <label for="product_price"
                                        >Product Price</label
                                    >
                                    <input
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        class="form-control"
                                        id="edit_product_price"
                                        required
                                        placeholder="Product Price"
                                        v-model="edit_product_price"
                                    />
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-12 mb-3">
                                    <div class="form-group">
                                        <label for="unit_select"
                                            >Select Unit</label
                                        >
                                        <select
                                            class="form-control"
                                            id="unit_select"
                                            v-model="edit_product_unit"
                                            required
                                        >
                                            <option value="" disabled>
                                                Select Unit
                                            </option>
                                            <option
                                                v-for="unit in units"
                                                :value="unit.id"
                                                :selected="
                                                    unit.id ===
                                                    edit_product_unit
                                                "
                                            >
                                                {{ unit.unit_name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button
                                type="button"
                                class="btn btn-secondary"
                                data-dismiss="modal"
                            >
                                Close
                            </button>
                            <button
                                type="submit"
                                class="btn btn-primary"
                                :disabled="updateSubmitDisabled"
                            >
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div
            class="modal fade"
            id="change-price-modal"
            data-backdrop="static"
            data-keyboard="false"
            tabindex="-1"
            aria-labelledby="change-price-modal-label"
            aria-hidden="true"
        >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="change-price-modal-label">
                            Add New Price
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
                    <form
                        class="needs-validation"
                        novalidate
                        @submit.prevent="addNewPrice"
                        ref="newPriceFormRef"
                    >
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="exampleFormControlInput1"
                                    >Product Name</label
                                >
                                <input
                                    type="text"
                                    class="form-control"
                                    id="product-name"
                                    placeholder="Product Name"
                                    required
                                    v-model="productName"
                                />
                            </div>
                            <div class="form-group">
                                <label for="exampleFormControlInput1"
                                    >Current Price</label
                                >
                                <input
                                    type="text"
                                    class="form-control"
                                    id="curet-price"
                                    placeholder="Current Price"
                                    required
                                    v-model="currentPrice"
                                />
                            </div>
                            <div class="form-group">
                                <label for="exampleFormControlInput1"
                                    >New Price</label
                                >
                                <input
                                    type="text"
                                    class="form-control"
                                    id="new-price"
                                    placeholder="Current Price"
                                    required
                                    v-model="newPrice"
                                />
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button
                                type="button"
                                class="btn btn-secondary"
                                data-dismiss="modal"
                                @click="close"
                            >
                                CLOSE
                            </button>
                            <button
                                type="submit"
                                class="btn btn-primary"
                                :disabled="newPriceSubmit"
                            >
                                SAVE
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
canvas {
    height: 400px !important;
}
</style>
