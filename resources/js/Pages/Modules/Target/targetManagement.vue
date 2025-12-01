<script setup>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue";
import DataTable from "@/Components/Admin/DataTable.vue";
import { Head } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";
// Define product table columns
const dealer_table_columns = [
    { field: "id", title: "ID", isUnique: true },
    { field: "employee_reg_no", title: "Employee Reg Number" },
    { field: "year", title: "Year" },
    { field: "month", title: "Month" },
    { field: "target_value", title: "Target Value" },
    { field: "achieved_value", title: "Achieved Value" },
    {
        field: "achieved_value",
        title: "Status",
        cellRenderer: (row) =>
            row.achieved_value == row.target_value
                ? `<span class="badge badge-success">Achieved</span>`
                : `<span class="badge badge-danger">Not Achieved</span>`,
    },
    { field: "actions", title: "Actions", cellRenderer: false, width: "50px" },
];
onMounted(() => {});
</script>

<template>
    <AdminLayout>
        <Head title="Dealer-Management" />
        <!-- Dashboard Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4 class="m-0">EMPLOYEE TARGET</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <a :href="route('dashboard')">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active">
                                Employee Target
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
                        <a :href="route('target.create')"
                            ><button class="btn btn-primary mr-2">
                                Create Target
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
                                    title="TARGET TABLE"
                                    fetch_url="/admin/target/data-table"
                                    :columns="dealer_table_columns"
                                    table_icon='<i class="nav-icon fas fa-archive" style="font-size: medium;"></i>'
                                    modal_title="Dealers"
                                    edit_route_name="target.edit"
                                    delete_route_name="target.delete"
                                    :use_view_button="false"
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
