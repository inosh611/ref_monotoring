<script setup>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import { useToast } from "vue-toastification";
import { store } from "../../../main";
import DataTable from "@/Components/Admin/DataTable.vue";

const toast = useToast();
const formRef = ref(null);

const employee_registration_number = ref("");
const target_amount = ref("");
const target_month = ref("");
const target_year = ref("");


async function handleSubmit() {
    const form = formRef.value;
    if (form.checkValidity() === false) {
        toast.error("Please fill out all required fields.");
        form.classList.add("was-validated");
        return;
    }
    const formData = new FormData();

    formData.append(
        "employee_registration_number",
        employee_registration_number.value
    );
    formData.append("target_amount", target_amount.value);
    formData.append("target_month", target_month.value);
     formData.append("target_year", target_year.value);
     
    store("target.store", formData);
}

const genarateYearOptions = () => {
    const currentYear = new Date().getFullYear();
    const years = [];
    for (let year = currentYear; year <= currentYear + 250; year++) {
        years.push(year);
    }
    return years;
};

const yearOptions = ref(genarateYearOptions());
</script>

<template>
    <AdminLayout>
        <Head title="dealer-Management" />

        <!-- Dashboard Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4 class="m-0">TARGET CREATE</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <a :href="route('dealer.index')">Dealer</a>
                            </li>
                            <li class="breadcrumb-item active">
                                Assign Target
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dashboard Content -->
        <section class="content">
            <div class="container-fluid">
                <!-- DataTable Row -->
                <form
                    class="needs-validation"
                    novalidate
                    @submit.prevent="handleSubmit"
                    ref="formRef"
                >
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card card-default">
                                <div class="card-body">
                                    <h5 class="w-75 mb-3 text-bold">
                                        Create Target
                                    </h5>
                                    <div class="form-row">
                                        <div class="col-md-4 mb-3">
                                            <label for="business_name"
                                                >Employee Registration
                                                Number</label
                                            >
                                            <input
                                                type="text"
                                                class="form-control"
                                                id="employee_registration_number"
                                                required
                                                placeholder="Employee Registration Number"
                                                v-model="
                                                    employee_registration_number
                                                "
                                            />
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="business_address"
                                                >Target Amount</label
                                            >
                                            <input
                                                type="text"
                                                class="form-control"
                                                id="target_amount"
                                                required
                                                placeholder="Target Amount"
                                                v-model="target_amount"
                                            />
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="business_address"
                                                >Select Year</label
                                            >
                                            <select
                                                class="form-control"
                                                id="year"
                                                v-model="target_year"
                                            >
                                                <option value="" disabled>
                                                    Select year
                                                </option>
                                                <option
                                                    v-for="year in yearOptions"
                                                    :key="year"
                                                    :value="year"
                                                >
                                                    {{ year }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="business_address"
                                                >Target Amount</label
                                            >
                                            <select
                                                class="form-control"
                                                id="month"
                                                v-model="target_month"
                                            >
                                                <option
                                                    value=""
                                                    selectedphp
                                                    disabled
                                                >
                                                    Select month
                                                </option>
                                                <option value="01">
                                                    January
                                                </option>
                                                <option value="02">
                                                    February
                                                </option>
                                                <option value="03">
                                                    March
                                                </option>
                                                <option value="04">
                                                    April
                                                </option>
                                                <option value="05">May</option>
                                                <option value="06">June</option>
                                                <option value="07">July</option>
                                                <option value="08">
                                                    August
                                                </option>
                                                <option value="09">
                                                    September
                                                </option>
                                                <option value="10">
                                                    October
                                                </option>
                                                <option value="11">
                                                    November
                                                </option>
                                                <option value="12">
                                                    December
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div
                                            class="col-12 d-flex justify-content-end"
                                        >
                                            <button
                                                class="btn btn-primary"
                                                type="submit"
                                            >
                                                Assign Target
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </AdminLayout>
</template>

<style scoped>
canvas {
    height: 400px !important;
}
.custom-location .col-md-6 {
    transition: all 1s ease;
}
</style>
