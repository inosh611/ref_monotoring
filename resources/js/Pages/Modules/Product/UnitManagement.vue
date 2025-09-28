<script setup>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue";
import DataTable from "@/Components/Admin/DataTable.vue";
import { Head } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";
import { useToast } from "vue-toastification";
import { store, update } from "../../../main";
const formRef = ref(null);
const toast = useToast();

const unit_name = ref("");
const unit_id = ref(null);
const submitDisabled = ref(false);

// Define product table columns
const employee_table_columns = [
    { field: "row_num", title: "#", isUnique: true, width: "30px" },
    { field: "id", title: "ID", isUnique: true },
    { field: "unit_name", title: "Unit Name" },
    { field: "actions", title: "Actions", cellRenderer: false, width: "50px" },
];

async function handleSubmit() {
    const form = formRef.value;
    if (form.checkValidity() === false) {
        toast.error("Please fill out all required fields.");
        form.classList.add("was-validated");
        return;
    }
    submitDisabled.value = true;
    const formData = new FormData();

    formData.append("unit_name", unit_name.value);
    if (unit_id.value) {
        formData.append("id", unit_id.value);
        await update("unit.update", formData);
        unit_name.value = "";
        unit_id.value = "";
    } else {
        await store("unit.store", formData);
        setTimeout(() => {
            submitDisabled.value = false;
        }, 3000);
    }
}

const resetInputs = () => {
    unit_name.value = " ";
    submitDisabled.value = true;
    formRef.value.classList.remove("was-validated");
};

const openEditUnit = (row) => {
    unit_name.value = row.unit_name;
    unit_id.value = row.id;
    submitDisabled.value = false;
};

onMounted(() => {});
</script>

<template>
    <AdminLayout>
        <Head title="Employee-Management" />
        <!-- Dashboard Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4 class="m-0 text-uppercase">Unit management</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <a :href="route('dashboard')">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active">
                                Unit Management
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
                        <button
                            class="btn btn-primary mr-2"
                            data-toggle="modal"
                            data-target="#unitModal"
                        >
                            Create Unit
                        </button>
                    </div>
                </div>
                <!-- DataTable Row -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card card-default">
                            <div class="card-body" style="padding: 0px">
                                <DataTable
                                    title="UNIT TABLE"
                                    fetch_url="/admin/unit/data-table"
                                    :columns="employee_table_columns"
                                    table_icon='<i class="nav-icon fas fa-archive" style="font-size: medium;"></i>'
                                    modal_title="Employee"
                                    edit_route_name="employee.edit"
                                    delete_route_name="unit.delete"
                                    edit_modal="true"
                                    @edit-item="openEditUnit"
                                    edit_modalId="#unitModal"
                                    :use_view_button=false
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
            id="unitModal"
            data-backdrop="static"
            data-keyboard="false"
            tabindex="-1"
            aria-labelledby="unitModalLabel"
            aria-hidden="true"
        >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="unitModalTitleLabel">
                            Unit {{ unit_id ? "Edit Unit" : "Create Unit" }}
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
                        @submit.prevent="handleSubmit"
                        ref="formRef"
                    >
                        <div class="modal-body">
                            <div class="form-row">
                                <div class="col-12 mb-3">
                                    <label for="unit_name">Unit Name</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="unit_name"
                                        required
                                        placeholder="Unit Name"
                                        v-model="unit_name"
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button
                                type="button"
                                class="btn btn-secondary"
                                data-dismiss="modal"
                                @click="resetInputs()"
                            >
                                Close
                            </button>
                            <button
                                type="submit"
                                class="btn btn-primary"
                                :disabled="submitDisabled"
                            >
                                {{ unit_id ? "Update Unit" : "Create Unit" }}
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
