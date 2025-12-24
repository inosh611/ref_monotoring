<script setup>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue";
import DataTable from "@/Components/Admin/DataTable.vue";
import { Head } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";
// Define product table columns
const customer_table_columns = [
    { field: "row_num", title: "#", isUnique: true, width: "30px" },
    // { field: "id", title: "ID", isUnique: true },
    { field: "order.order_number", title: "Order Number" },
    { field: "user.id", title: "Ref" },
    { field: "collection_type", title: "Collection Type" },
    { field: "paid_amount", title: "Paid Amount" },
    { field: "paid_amount_text", title: "Paid Amount Text" },
    {
  field: "comment",
  title: "Comment",
  width: 260,                     // important for ellipsis
  tooltip: (cell) => cell.getValue() ?? "", // Tabulator tooltip (optional)
  formatter: (cell) => {
    const val = cell.getValue?.() ?? "";
    const el = document.createElement("span");
    el.className = "truncate";
    el.textContent = val;   // shows preview "...", full text in title/tooltip
    el.title = val;         // native browser tooltip
    return el;
  },
},
    { field: "date", title: "Created At" },
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
                        <h4 class="m-0">MY COLLECTIONS</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <a :href="route('dashboard')">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active">
                                My Collection
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
                        <a :href="route('my.collection.create')"
                            ><button class="btn btn-primary mr-2">
                                Create Collection
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
                                    fetch_url="/admin/my-collection/data-table"
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

<style >
canvas {
    height: 400px !important;
}

.truncate{
  display:inline-block;
  max-width:100%;
  white-space:nowrap;
  overflow:hidden;
  text-overflow:ellipsis;
  cursor:help;
}

/* two-line clamp */
.clamp-2{
  display:-webkit-box;
  -webkit-line-clamp:2;
  -webkit-box-orient: vertical;
  overflow:hidden;
  cursor:help;
}
</style>
