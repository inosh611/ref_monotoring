<script setup>
import AdminLayout from '@/Layouts/Admin/AdminLayout.vue';
import { Head } from "@inertiajs/vue3";
import { ref } from 'vue';
import { useToast } from 'vue-toastification'
import { store } from '../../../main';
import DataTable from '@/Components/Admin/DataTable.vue';

const toast = useToast();
const dealer_first_name = ref('');
const dealer_last_name = ref('');
const dealer_nic = ref('');
const dealer_contact_number = ref('');
const dealer_address = ref('');
const dealer_email = ref('');
const role_name = ref('');
const owner_position = ref('');
const business_name = ref('');
const business_address = ref('');
const business_tel = ref('');
const formRef = ref(null);

const props = defineProps({
    roles: Array,
});

// const dealer_table_columns = [
//     { field: 'id', title: 'ID', isUnique: true },
//     { field: 'first_name', title: 'First Name' },
//     { field: 'last_name', title: 'Last Name' },
//     { field: 'contact_number', title: 'Contact No' },
//     { field: 'address', title: 'Address' },
//     { field: 'nic', title: 'NIC' },
//     {
//         field: 'registration_doc',
//         title: 'Registration Doc',
//         cellRenderer: (row) =>
//             row.registration_doc
//                 ? `<a href="/storage/${row.registration_doc}" target="_blank" rel="noopener"
//            onclick="event.stopPropagation();">View PDF</a>`
//                 : 'N/A'
//     },
//     {
//         field: 'sign_application',
//         title: 'Sign Application',
//         cellRenderer: (row) =>
//             row.sign_application
//                 ? `<a href="/storage/${row.sign_application}" target="_blank" rel="noopener"
//            onclick="event.stopPropagation();">View PDF</a>`
//                 : 'N/A'
//     },
//     { field: 'actions', title: 'Actions', cellRenderer: false, width: '50px' },
// ];

async function handleSubmit() {
    const form = formRef.value;
    if (form.checkValidity() === false) {
        toast.error("Please fill out all required fields.");
        form.classList.add('was-validated');
        return;
    }
    const formData = new FormData();

    formData.append('first_name', dealer_first_name.value);
    formData.append('last_name', dealer_last_name.value);
    formData.append('nic', dealer_nic.value);
    formData.append('contact_number', dealer_contact_number.value);
    formData.append('address', dealer_address.value);
    formData.append('email', dealer_email.value);
    formData.append('owner_position', owner_position.value);
    formData.append('business_name', business_name.value);
    formData.append('business_address', business_address.value);
    formData.append('business_tel', business_tel.value);
    
    const regInput = document.getElementById('registration_doc');
    const signInput = document.getElementById('sign_application');
    const nic_copy = document.getElementById('nic_copy');
    const photo_of_the_shop = document.getElementById('photo_of_the_shop');

    if (regInput.files[0]) formData.append('registration_doc', regInput.files[0]);
    if (signInput.files[0]) formData.append('sign_application', signInput.files[0]);
    if (nic_copy.files[0]) formData.append('nic_copy', nic_copy.files[0]);
    if (photo_of_the_shop.files[0]) formData.append('photo_of_the_shop', photo_of_the_shop.files[0]);

    store('dealer.store', formData);
}

</script>

<template>
    <AdminLayout>

        <Head title="dealer-Management" />

        <!-- Dashboard Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4 class="m-0">DEALER CREATE</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a :href="route('dealer.index')">Dealer</a></li>
                            <li class="breadcrumb-item active">Dealer Create</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dashboard Content -->
        <section class="content">
            <div class="container-fluid">
                <!-- DataTable Row -->
                <form class="needs-validation" novalidate @submit.prevent="handleSubmit" ref="formRef">
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card card-default">
                            <div class="card-body">
                                <h5 class="w-75 mb-3 text-bold">Owner Details</h5>

                                <div class="form-row">
                                    <div class="col-md-6 mb-3">
                                        <label for="dealers_first_name">First Name</label>
                                        <input type="text" class="form-control" id="dealers_first_name" required
                                            placeholder="First Name" v-model="dealer_first_name">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="dealers_last_name">Last Name</label>
                                        <input type="text" class="form-control" id="dealers_last_name" required
                                            placeholder="Last Name" v-model="dealer_last_name">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="dealers_nic">NIC</label>
                                        <input type="text" class="form-control" id="dealers_nic_name" required
                                            placeholder="NIC" v-model="dealer_nic">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="dealers_contact_number">Contact number</label>
                                        <input type="text" class="form-control" id="dealers_contact_number" required
                                            placeholder="Contact Number" v-model="dealer_contact_number">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="dealers_contact_number">Address</label>
                                        <input type="text" class="form-control" id="dealers_address" required
                                            placeholder="Address" v-model="dealer_address">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="dealers_email">Email</label>
                                        <input type="email" class="form-control" id="dealers_address" required
                                            placeholder="Email address" v-model="dealer_email">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="nic_copy">NIC Photo</label>
                                        <input id="nic_copy" type="file" class="form-control" accept="image/*" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="owner_position">Position</label>
                                        <input type="text" class="form-control" id="owner_position" required
                                            placeholder="Position" v-model="owner_position">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card card-default">
                            <div class="card-body">
                                <h5 class="w-75 mb-3 text-bold">Business Details</h5>
                                <div class="form-row">
                                    <div class="col-md-6 mb-3">
                                        <label for="business_name">Business Name</label>
                                        <input type="text" class="form-control" id="business_name" required
                                            placeholder="Business Name" v-model="business_name">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="business_address">Address of the Business</label>
                                        <input type="text" class="form-control" id="business_address" required
                                            placeholder="Address of the Business" v-model="business_address">
                                    </div>
                                     <div class="col-md-6 mb-3">
                                        <label for="business_tel">Business Tell</label>
                                        <input type="text" class="form-control" id="business_tel" required
                                            placeholder="Business Tel" v-model="business_tel">
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
                                    <div class="col-md-6 mb-3">
                                        <label for="photo_of_the_shop">Photo of the shop</label>
                                        <input id="photo_of_the_shop" type="file" class="form-control"
                                            accept="image/*" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 d-flex justify-content-end">
                                        <button class="btn btn-primary" type="submit">Create</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
                <!-- DataTable Row -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card card-default">
                            <div class="card-body" style="padding: 0px;">
                                <!-- <DataTable title="DEALERS TABLE" fetch_url=""
                                    :columns="dealer_table_columns"
                                    table_icon='<i class="nav-icon fas fa-archive" style="font-size: medium;"></i>'
                                    modal_title="Dealers" edit_route_name='dealer.edit'
                                    delete_route_name='dealer.delete' view_button=false /> -->
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
