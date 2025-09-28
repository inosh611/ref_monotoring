<script setup>
import AdminLayout from '@/Layouts/Admin/AdminLayout.vue';
import { Head } from "@inertiajs/vue3";
import { ref } from 'vue';
import { useToast } from 'vue-toastification'
import { store } from '../../../main';
import DataTable from '@/Components/Admin/DataTable.vue';

const toast = useToast();
const customer_first_name = ref('');
const customer_last_name = ref('');
const customer_nic = ref('');
const customer_contact_number = ref('');
const customer_address = ref('');
const customer_email = ref('');
const role_name = ref('');
const customer_position = ref('');
const formRef = ref(null);

const props = defineProps({
    roles: Array,
});

const customer_table_columns = [
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

async function handleSubmit() {
    const form = formRef.value;
    if (form.checkValidity() === false) {
        toast.error("Please fill out all required fields.");
        form.classList.add('was-validated');
        return;
    }
    const formData = new FormData();

    formData.append('first_name', customer_first_name.value);
    formData.append('last_name', customer_last_name.value);
    formData.append('nic', customer_nic.value);
    formData.append('contact_number', customer_contact_number.value);
    formData.append('address', customer_address.value);
    formData.append('email', customer_email.value);

    const regInput = document.getElementById('registration_doc');
    const signInput = document.getElementById('sign_application');

    if (regInput.files[0]) formData.append('registration_doc', regInput.files[0]);
    if (signInput.files[0]) formData.append('sign_application', signInput.files[0]);

    store('customer.store', formData);
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
                        <h4 class="m-0">UNIT CREATE</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a :href="route('customer.index')">CUSTOMER</a></li>
                            <li class="breadcrumb-item active">CUSTOMER Create</li>
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
                                <h5 class="w-75 mb-3 text-bold">CUSTOMER Details</h5>
                                <form class="needs-validation" novalidate @submit.prevent="handleSubmit" ref="formRef">
                                    <div class="form-row">
                                        <div class="col-md-6 mb-3">
                                            <label for="customer_first_name">First Name</label>
                                            <input type="text" class="form-control" id="customer_first_name" required
                                                placeholder="First Name" v-model="customer_first_name">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="customer_last_name">Last Name</label>
                                            <input type="text" class="form-control" id="customer_last_name" required
                                                placeholder="Last Name" v-model="customer_last_name">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="customer_nic">NIC</label>
                                            <input type="text" class="form-control" id="customer_nic_name" required
                                                placeholder="NIC" v-model="customer_nic">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="customer_contact_number">Contact number</label>
                                            <input type="number" class="form-control" id="customer_contact_number"
                                                required placeholder="Contact Number" v-model="customer_contact_number">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="customer_contact_number">Address</label>
                                            <input type="text" class="form-control" id="customer_address" required
                                                placeholder="Address" v-model="customer_address">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="customer_email">Email</label>
                                            <input type="email" class="form-control" id="customer_address" required
                                                placeholder="Email address" v-model="customer_email">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="registration_doc">Business Registration (PDF)</label>
                                            <input id="registration_doc" type="file" class="form-control"
                                                accept="application/pdf" required>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="sign_application">Signed Application (PDF)</label>
                                            <input id="sign_application" type="file" class="form-control"
                                                accept="application/pdf" required>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 d-flex justify-content-end">
                                            <button class="btn btn-primary" type="submit">Create</button>
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
                            <div class="card-body" style="padding: 0px;">
                                <DataTable title="CUSTOMER TABLE" fetch_url="/admin/customer/data-table"
                                    :columns="customer_table_columns"
                                    table_icon='<i class="nav-icon fas fa-archive" style="font-size: medium;"></i>'
                                    modal_title="Customer" edit_route_name='customer.edit'
                                    delete_route_name='customer.delete' view_button=false />
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
