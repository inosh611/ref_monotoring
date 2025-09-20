<script setup>
import AdminLayout from '@/Layouts/Admin/AdminLayout.vue';
import DataTable from '@/Components/Admin/DataTable.vue';
import { Head } from "@inertiajs/vue3";
import { onMounted, ref } from 'vue';
// Define product table columns
const task_table_columns = [
  { field: 'id', title: 'ID', isUnique: true },
  { field: 'task_number', title: 'Task No' },
  { field: 'task_type', title: 'Task Type' },          // Visit / Stock Check / Collection / Order / Photo / GPS / Report
  { field: 'dealer_name', title: 'Dealer' },
  { field: 'rep_name', title: 'Rep' },
  { field: 'due_date', title: 'Due Date' },
  {
    field: 'status',
    title: 'Status',
    cellRenderer: (row) => {
      const map = {
        Pending: 'badge badge-warning',
        'In Progress': 'badge badge-info',
        Completed: 'badge badge-success',   // ✅ green
        Overdue: 'badge badge-danger',
        Cancelled: 'badge badge-secondary',
      };
      return `<span class="${map[row.status] || 'badge badge-light'}">${row.status}</span>`;
    },
  },
  {
    field: 'priority',
    title: 'Priority',
    cellRenderer: (row) => {
      const map = {
        High: 'badge badge-danger',
        Medium: 'badge badge-primary',
        Low: 'badge badge-secondary',
      };
      return `<span class="${map[row.priority] || 'badge badge-light'}">${row.priority}</span>`;
    },
  },
  { field: 'location', title: 'Location' },            // city/area
  { field: 'notes', title: 'Notes' },
  { field: 'actions', title: 'Actions', cellRenderer: false, width: '60px' },
];

onMounted(() => {

});
</script>

<template>
    <AdminLayout>

        <Head title="Order-Management" />
        <!-- Dashboard Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4 class="m-0">TASK</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a :href="route('dashboard')">Dashboard</a></li>
                            <li class="breadcrumb-item active">Task Management</li>
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
                        <a href=""><button class="btn btn-primary mr-2">Export Task</button></a>
                        <button class="btn btn-primary mr-2">Import Task</button>
                        <a :href="route('customer.create')"><button class="btn btn-primary mr-2">Create
                                Task</button></a>
                    </div>
                </div>
                <!-- DataTable Row -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card card-default">
                            <div class="card-body" style="padding: 0px;">
                                <DataTable title="TASK TABLE" fetch_url="/admin/task/data-table"
                                    :columns="task_table_columns"
                                    table_icon='<i class="nav-icon fas fa-archive" style="font-size: medium;"></i>'
                                    modal_title="Task" edit_route_name='order.edit'
                                    delete_route_name='task.delete' view_button=false />
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
