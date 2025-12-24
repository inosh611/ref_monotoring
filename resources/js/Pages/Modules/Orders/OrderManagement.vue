<script setup>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue";
import DataTable from "@/Components/Admin/DataTable.vue";
import { Head } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";
import { useToast } from "vue-toastification";
import { update } from "../../../main";

const toast = useToast();
const formRef = ref(null);
const selectedOrderStatus = ref("Pending");
const order_id = ref(null);
const orderItems = ref([]);
const shop_id = ref(null);

// Define product table columns
const customer_table_columns = [
    { field: "row_num", title: "#", isUnique: true, width: "30px" },
    // { field: "id", title: "ID", isUnique: true },
    { field: "order_number", title: "Order Number" },
    { field: "business_name", title: "Shop Name" },
    { field: "user.reg_number", title: "Ref" },
    {
        field: "order_status",
        title: "Order Status",
        cellRenderer: (row) => {
            const map = {
                Pending: "badge badge-warning p-2",
                Delivered: "badge badge-info p-2",
                Confirmed: "badge badge-success p-2",
                completed: "badge badge-success p-2",
                cancelled: "badge badge-danger p-2",
            };
            return `<span class="${
                map[row.order_status] || "badge badge-secondary"
            }">
                ${row.order_status}
              </span>`;
        },
    },
    {
        field: "Payment_status",
        title: "Payment Status",
        cellRenderer: (row) => {
            const map = {
                Pending: "badge badge-danger p-2",
                unpaid: "badge badge-danger p-2",
                partial: "badge badge-warning p-2",
                paid: "badge badge-success p-2",
                refunded: "badge badge-secondary p-2",
            };
            return `<span class="${
                map[row.payment_status] || "badge badge-light"
            }">
                ${row.payment_status}
              </span>`;
        },
    },
    { field: "total_price", title: "Total_price" },
    { field: "created_at_formatted", title: "Created At" },
    { field: "expected_order_date", title: "Expected Order Date" },
    { field: "actions", title: "Actions", cellRenderer: false, width: "50px" },
];

const changeOrderStatus = (row) => {
    selectedOrderStatus.value = row.order_status;
    orderItems.value = row.items;
    order_id.value = row.id;
    shop_id.value = row.shop_id;
    console.log("Order Details:", row.order_status);
};

async function handleUpdate() {
    console.log("Updating order with ID:", order_id.value);
    const form = formRef.value;
    if (form.checkValidity() === false) {
        toast.error("Please fill out all required fields.");
        form.classList.add("was-validated");
        return;
    }
    const formData = new FormData();

    formData.append("id", order_id.value);
    formData.append("order_status", selectedOrderStatus.value);
    formData.append("items", JSON.stringify(orderItems.value));
    formData.append("order_id", order_id.value);
    formData.append("shop_id", shop_id.value);
    update("order.update", formData);
}

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
                        <h4 class="m-0">ORDERS</h4>
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
                <div class="row">
                    <div class="col-12">
                        <!-- <a href=""
                            ><button class="btn btn-primary mr-2">
                                Export Orders
                            </button></a
                        >
                        <button class="btn btn-primary mr-2">
                            Import Orders
                        </button> -->
                        <a :href="route('order.create')"
                            ><button class="btn btn-primary mr-2">
                                Create Order
                            </button></a
                        >
                    </div>
                </div>
                <!-- DataTable Row -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card card-default">
                            <div class="card-body" style="padding: 0px">
                                <DataTable
                                    title="ORDER TABLE"
                                    fetch_url="/admin/order/data-table"
                                    :columns="customer_table_columns"
                                    table_icon='<i class="nav-icon fas fa-archive" style="font-size: medium;"></i>'
                                    modal_title="Order"
                                    edit_route_name="order.edit"
                                    delete_route_name="order.delete"
                                    view_button="false"
                                    :use_order_status_button="true"
                                    @change-order-status="changeOrderStatus"
                                    order_status_modal="#change-order-status-modal"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Modal -->
        <div
            class="modal fade"
            id="change-order-status-modal"
            data-backdrop="static"
            data-keyboard="false"
            tabindex="-1"
            aria-labelledby="change-order-status-modal-label"
            aria-hidden="true"
        >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5
                            class="modal-title"
                            id="change-order-status-modal-label"
                        >
                            Change Order Status
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
                            ref="formRef"
                        >
                    <div class="modal-body">
                            <div class="form-group">
                                <label for="order-status"
                                    >Select Order Status</label
                                >
                                <select
                                    class="form-control"
                                    id="order-status"
                                    v-model="selectedOrderStatus"
                                >
                                    <option value="Pending">Pending</option>
                                    <option value="Delivered">Delivered</option>
                                    <option value="Confirmed">Confirm</option>
                                </select>
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-dismiss="modal"
                            @click="() => { formRef.value.classList.remove('was-validated'); }"
                        >
                            Close
                        </button>
                        <button type="submit" class="btn btn-primary">
                           Updated
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
