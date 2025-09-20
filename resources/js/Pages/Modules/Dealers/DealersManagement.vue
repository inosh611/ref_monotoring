<script setup>
import AdminLayout from '@/Layouts/Admin/AdminLayout.vue';
import DataTable from '@/Components/Admin/DataTable.vue';
import { Head } from "@inertiajs/vue3";
import { onMounted, ref } from 'vue';
// Define product table columns
const dealer_table_columns = [
    { field: 'id', title: 'ID', isUnique: true },
    { field: 'first_name', title: 'First Name' },
    { field: 'last_name', title: 'Last Name' },
    { field: 'contact_number', title: 'Contact No' },
    { field: 'address', title: 'Address' },
    { field: 'nic', title: 'NIC' },
    {
        field: 'registration_doc',
        title: 'Registration Doc',
        cellRenderer: (row) =>
            row.registration_doc
                ? `<a href="/storage/${row.registration_doc}" target="_blank" rel="noopener"
           onclick="event.stopPropagation();">View PDF</a>`
                : 'N/A'
    },
    {
        field: 'sign_application',
        title: 'Sign Application',
        cellRenderer: (row) =>
            row.sign_application
                ? `<a href="/storage/${row.sign_application}" target="_blank" rel="noopener"
           onclick="event.stopPropagation();">View PDF</a>`
                : 'N/A'
    },
    { field: 'actions', title: 'Actions', cellRenderer: false, width: '50px' },
];
onMounted(() => {

});
</script>

<template>
    <AdminLayout>

        <Head title="Dealer-Management" />
        <!-- Dashboard Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4 class="m-0">DEALERS</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a :href="route('dashboard')">Dashboard</a></li>
                            <li class="breadcrumb-item active">Dealers Management</li>
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
                        <a href=""><button class="btn btn-primary mr-2">Export Dealers</button></a>
                        <button class="btn btn-primary mr-2">Import Dealers</button>
                        <a :href="route('dealer.create')"><button class="btn btn-primary mr-2">Create
                                Dealers</button></a>
                    </div>
                </div>
                <!-- DataTable Row -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card card-default">
                            <div class="card-body" style="padding: 0px;">
                                <DataTable title="DEALERS TABLE" fetch_url="/admin/dealer/data-table"
                                    :columns="dealer_table_columns"
                                    table_icon='<i class="nav-icon fas fa-archive" style="font-size: medium;"></i>'
                                    modal_title="Dealers" edit_route_name='dealer.edit'
                                    delete_route_name='dealer.delete' view_button=false />
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
