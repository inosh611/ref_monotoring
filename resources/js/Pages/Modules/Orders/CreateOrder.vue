<script setup>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue";
import DataTable from "@/Components/Admin/DataTable.vue";
import { Head } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";
import { useToast } from "vue-toastification";
import { store } from "../../../main";

const toast = useToast();
const itemAddFormRef = ref(null);
const itemName = ref("");
const itemQuantity = ref("");
const itemList = ref([]);
const dealer_id = ref("");
const user_details = ref({});
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
        name: itemName.value,
        quantity: itemQuantity.value,
    });
    itemName.value = "";
    itemQuantity.value = "";
    form.classList.remove("was-validated");
    console.log(itemList.value);
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
                                            <div class="form-group">
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
                                                        :disabled="
                                                            !itemList.length
                                                        "
                                                    >
                                                        Confirm Order
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-12">
                                                    <table
                                                        class="table table-striped"
                                                    >
                                                        <tbody
                                                            v-if="
                                                                itemList.length
                                                            "
                                                        >
                                                            <tr
                                                                v-for="(
                                                                    item, index
                                                                ) in itemList"
                                                                :key="index"
                                                            >
                                                                <td>
                                                                    {{
                                                                        index +
                                                                        1
                                                                    }}
                                                                </td>
                                                                <td>
                                                                    {{
                                                                        item.name
                                                                    }}
                                                                </td>
                                                                <td>
                                                                    <input
                                                                        type="number"
                                                                        class="form-control"
                                                                        v-model="
                                                                            itemList[
                                                                                index
                                                                            ]
                                                                                .quantity
                                                                        "
                                                                    />
                                                                </td>
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
                                                        <div
                                                            class="empty-order"
                                                            v-else
                                                        >
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
@media (max-width: 900px) {
    .item-add-btn {
        width: 100%;
    }
    .col-12 {
        padding-left: 0 !important;
        padding-right: 0 !important;
    }
}
</style>
