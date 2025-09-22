<script setup>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue";
import DataTable from "@/Components/Admin/DataTable.vue";
import { Head } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";
import { store } from '../../../main';
const formRef = ref(null);
const time = ref("");
const date = ref("");
const dealer_id = ref("");

const props = defineProps({
    roles: Array,
    dealers: Array,
});

const resetForm = () => {
    dealer_id.value = "";
    date.value = "";
    time.value = "";
}
// Define product table columns\,
const dealer_table_columns = [
    { field: "id", title: "ID", isUnique: true },
    { field: "owner.first_name", title: "Owner Name" },
    { field: "owner.contact_number", title: "Owner Contact number" },
    { field: "business_name", title: "Business Name" },
    { field: "business_address", title: "Business Address" },
    {
        field: "nic_copy",
        title: "NIC Copy",
        cellRenderer: (row) =>
            row.owner.nic_copy
                ? `<a href="/storage/${row.owner.nic_copy}" target="_blank" rel="noopener"
           onclick="event.stopPropagation();">NIC Copy</a>`
                : "N/A",
    },
    {
        field: "registration_doc",
        title: "Registration Doc",
        cellRenderer: (row) =>
            row.registration_doc
                ? `<a href="/storage/${row.registration_doc}" target="_blank" rel="noopener"
           onclick="event.stopPropagation();">View PDF</a>`
                : "N/A",
    },
    {
        field: "sign_application",
        title: "Sign Application",
        cellRenderer: (row) =>
            row.sign_application
                ? `<a href="/storage/${row.sign_application}" target="_blank" rel="noopener"
           onclick="event.stopPropagation();">View PDF</a>`
                : "N/A",
    },
    {
        field: "photo_of_the_shop",
        title: "Photo of The Shop",
        cellRenderer: (row) =>
            row.sign_application
                ? `<a href="/storage/${row.sign_application}" target="_blank" rel="noopener"
           onclick="event.stopPropagation();">View PDF</a>`
                : "N/A",
    },
    { field: "actions", title: "Actions", cellRenderer: false, width: "50px" },
];

function getCurrentTime() {
    const now = new Date();

    // Convert to Sri Lanka offset (UTC+5:30)
    const utc = now.getTime() + now.getTimezoneOffset() * 60000;
    const slTime = new Date(utc + 5.5 * 60 * 60 * 1000);

    // Format as HH:MM
    const hours = String(slTime.getHours()).padStart(2, "0");
    const minutes = String(slTime.getMinutes()).padStart(2, "0");

    return `${hours}:${minutes}`;
}
function getCurrentDate() {
    const now = new Date();
    return now.toISOString().slice(0, 10); // "HH:MM"
}

async function handleSubmit() {
    const form = formRef.value;
    console.log("helllo", form);
    if (form.checkValidity() === false) {
        toast.error("Please fill out all required fields.");
        form.classList.add("was-validated");
        return;
    }
    const formData = new FormData();

    formData.append("dealer_id", dealer_id.value);
    formData.append("checkout_time", time.value);
    formData.append("checkout_date", date.value);
    resetForm();
    store("my.checkout.store", formData);
}
function ChangeDealer(event) {
    time.value = getCurrentTime();
    date.value = getCurrentDate();
}
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
                        <h4 class="m-0">MY VISITING</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <a :href="route('dashboard')">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active">My Visiting</li>
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
                        <a :href="route('submit.check.in')"
                            ><button class="btn btn-primary mr-2">
                                Submit Check In
                            </button></a
                        >
                        <a :href="route('dealer.create')"
                            ><button class="btn btn-primary mr-2">
                                Submit Stock
                            </button></a
                        >
                        <a :href="route('dealer.create')"
                            ><button class="btn btn-primary mr-2">
                                Submit Collection
                            </button></a
                        >
                        <button
                            class="btn btn-primary mr-2"
                            type="button"
                            data-toggle="modal"
                            data-target="#staticBackdrop"
                        >
                            Check Out
                        </button>
                    </div>
                </div>
                <!-- DataTable Row -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card card-default">
                            <div class="card-body" style="padding: 0px">
                                <DataTable
                                    title="DEALERS TABLE"
                                    fetch_url="/admin/dealer/data-table"
                                    :columns="dealer_table_columns"
                                    table_icon='<i class="nav-icon fas fa-archive" style="font-size: medium;"></i>'
                                    modal_title="Dealers"
                                    edit_route_name="dealer.edit"
                                    delete_route_name="dealer.delete"
                                    use_view_button="true"
                                    view_route_name="dealer.show"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </AdminLayout>

    <!-- Check out Modal -->
    <div
        class="modal fade"
        id="staticBackdrop"
        data-backdrop="static"
        data-keyboard="false"
        tabindex="-1"
        aria-labelledby="staticBackdropLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
                        Submit Check Out
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
                    class="needs-validation w-100 p-2"
                    novalidate
                    @submit.prevent="handleSubmit"
                    ref="formRef"
                >
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-row">
                                <div class="col-12 mb-3">
                                    <label for="photo_of_the_shop"
                                        >Select Dealer</label
                                    >
                                    <select
                                        class="custom-select"
                                        v-model="dealer_id"
                                        @change="ChangeDealer"
                                        required
                                    >
                                        <option selected value="">
                                            Select Dealer
                                        </option>
                                        <option
                                            v-for="dealer in dealers"
                                            :value="dealer.id"
                                        >
                                            {{ dealer.business_name }} -
                                            {{ dealer.business_address }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="owner_position">Time</label>
                                    <input
                                        type="time"
                                        class="form-control"
                                        id="owner_position"
                                        required
                                        placeholder="Position"
                                        v-model="time"
                                        disabled
                                    />
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="date">Date</label>
                                    <input
                                        type="date"
                                        class="form-control"
                                        id="date"
                                        required
                                        placeholder="Date"
                                        v-model="date"
                                        disabled
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-dismiss="modal"
                        >
                            Close
                        </button>
                        <button type="submit" class="btn btn-danger">
                            Check Out
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
<style scoped>
canvas {
    height: 400px !important;
}
</style>
