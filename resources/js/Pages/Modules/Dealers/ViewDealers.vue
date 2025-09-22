<script setup>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
    dealerDetails: Array,
});
</script>

<template>
    <AdminLayout>
        <Head title="dealer-Management" />

        <!-- Dashboard Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4 class="m-0">DEALER DETAILS</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <a :href="route('dealer.index')">Dealer</a>
                            </li>
                            <li class="breadcrumb-item active">
                                Dealer Details
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dashboard Content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card card-default">
                            <div class="card-body">
                                <h5 class="w-75 mb-3 text-bold">
                                    Owner Details
                                </h5>
                                <div class="row">
                                    <div class="col-lg-8 col-12">
                                        <table class="table table-striped">
                                            <tbody>
                                                <tr>
                                                    <td>First Name</td>
                                                    <td>{{
                                                            dealerDetails.owner
                                                                .first_name
                                                        }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Last Name</td>
                                                    <td>
                                                        {{
                                                            dealerDetails.owner
                                                                .last_name
                                                        }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>NIC Number</td>
                                                    <td>
                                                        {{
                                                            dealerDetails.owner
                                                                .nic
                                                        }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Contact Number</td>
                                                    <td>
                                                        {{
                                                            dealerDetails.owner
                                                                .contact_number
                                                        }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Contact Number</td>
                                                    <td>
                                                        {{
                                                            dealerDetails.owner
                                                                .address
                                                        }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Email</td>
                                                    <td>
                                                        {{
                                                            dealerDetails.owner
                                                                .email
                                                        }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Email</td>
                                                    <td>
                                                        {{
                                                            dealerDetails.owner
                                                                .email
                                                        }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Owner Position</td>
                                                    <td>
                                                        <span
                                                            class="badge badge-pill badge-primary p-2"
                                                            >{{
                                                                dealerDetails
                                                                    .owner
                                                                    .owner_position
                                                            }}</span
                                                        >
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-lg-4 col-12">
                                        <div class="card card-row card-success">
                                            <div class="card-body">
                                                <img
                                                    :src="`/storage/${dealerDetails.owner.nic_copy}`"
                                                    width="100%"
                                                    height="500px"
                                                />
                                            </div>
                                        </div>
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
                                <h5 class="w-75 mb-3 text-bold">
                                    Business Details
                                </h5>
                                <div class="row">
                                    <div class="col-lg-8 col-12">
                                        <table class="table table-striped">
                                            <tbody>
                                                <tr>
                                                    <td>Business Name</td>
                                                    <td>
                                                        {{dealerDetails.business_name}}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Business Address</td>
                                                    <td>
                                                        {{dealerDetails.business_address}}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Business Tel</td>
                                                    <td>
                                                        {{dealerDetails.business_tel}}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Registration Doc</td>
                                                    <td>
                                                        <a :href="`/storage/${dealerDetails.registration_doc}`" target="_blank">View PDF</a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Sign Application</td>
                                                    <td> <a :href="`/storage/${dealerDetails.sign_application}`" target="_blank">View PDF</a></td>
                                                    
                                                </tr>
                                                <tr>
                                                    <td>Sign Application</td>
                                                    <td> <a :href="`/storage/${dealerDetails.sign_application}`" target="_blank">View PDF</a></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                      <div class="col-lg-4 col-12">
                                        <div class="card card-row card-success">
                                            <div class="card-body">
                                                <img
                                                    :src="`/storage/${dealerDetails.photo_of_the_shop}`"
                                                    width="100%"
                                                    height="500px"
                                                />
                                            </div>
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
</style>
