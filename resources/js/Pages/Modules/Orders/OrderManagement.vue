<script setup>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue";
import DataTable from "@/Components/Admin/DataTable.vue";
import { Head } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";
// Define product table columns
const customer_table_columns = [
    { field: "row_num", title: "#", isUnique: true, width: "30px" },
    // { field: "id", title: "ID", isUnique: true },
    { field: "order_number", title: "Order Number" },
    { field: "business_name", title: "Shop Name" },
     { field: "user.id", title: "Ref" },
    {
        field: "order_status",
        title: "Order Status",
        cellRenderer: (row) => {
            const map = {
                Pending: "badge badge-warning",
                processing: "badge badge-info",
                shipped: "badge badge-primary",
                completed: "badge badge-success",
                cancelled: "badge badge-danger",
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
                Pending: "badge badge-danger",
                unpaid: "badge badge-danger",
                partial: "badge badge-warning",
                paid: "badge badge-success",
                refunded: "badge badge-secondary",
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
    { field: "actions", title: "Actions", cellRenderer: false, width: "50px" },
];

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
                        <a href=""
                            ><button class="btn btn-primary mr-2">
                                Export Orders
                            </button></a
                        >
                        <button class="btn btn-primary mr-2">
                            Import Orders
                        </button>
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
                                />
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
</style>
