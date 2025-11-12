<script setup>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import { useToast } from "vue-toastification";
import { store } from "../../../main";
import DataTable from "@/Components/Admin/DataTable.vue";

const toast = useToast();
const dealer_first_name = ref("");
const dealer_last_name = ref("");
const dealer_nic = ref("");
const dealer_contact_number = ref("");
const dealer_address = ref("");
const dealer_email = ref("");
const owner_position = ref("");
const business_name = ref("");
const business_address = ref("");
const business_tel = ref("");
const lat_code = ref("");
const lng_code = ref("");
const location_type = ref("");

const lng = ref("");
const lat = ref("");
const formRef = ref(null);

const props = defineProps({
    roles: Array,
});

async function handleSubmit() {
    const form = formRef.value;
    if (form.checkValidity() === false) {
        toast.error("Please fill out all required fields.");
        form.classList.add("was-validated");
        return;
    }
    const formData = new FormData();

    formData.append("first_name", dealer_first_name.value);
    formData.append("last_name", dealer_last_name.value);
    formData.append("nic", dealer_nic.value);
    formData.append("contact_number", dealer_contact_number.value);
    formData.append("address", dealer_address.value);
    formData.append("email", dealer_email.value);
    formData.append("owner_position", owner_position.value);
    formData.append("business_name", business_name.value);
    formData.append("business_address", business_address.value);
    formData.append("business_tel", business_tel.value);
    formData.append("lng", lng.value);
    formData.append("lat", lat.value);

    const regInput = document.getElementById("registration_doc");
    const signInput = document.getElementById("sign_application");
    const nic_copy = document.getElementById("nic_copy");
    const photo_of_the_shop = document.getElementById("photo_of_the_shop");

    if (regInput.files[0])
        formData.append("registration_doc", regInput.files[0]);
    if (signInput.files[0])
        formData.append("sign_application", signInput.files[0]);
    if (nic_copy.files[0]) formData.append("nic_copy", nic_copy.files[0]);
    if (photo_of_the_shop.files[0])
        formData.append("photo_of_the_shop", photo_of_the_shop.files[0]);

    store("dealer.store", formData);
}

watch(
    () => location_type.value,
    async (newValue) => {
        if (newValue === "current") {
            if (!("geolocation" in navigator)) {
                toast.error("Geolocation is not supported by this browser.");
                return;
            }

            // (Optional) check permission state for clearer errors
            try {
                if (navigator.permissions?.query) {
                    const status = await navigator.permissions.query({
                        name: "geolocation",
                    });
                    if (status.state === "denied") {
                        toast.error(
                            "Location permission denied in browser settings."
                        );
                        return;
                    }
                }
            } catch {
                /* ignore permissions API errors */
            }

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    lat.value = position.coords.latitude;
                    lng.value = position.coords.longitude;
                    console.log("Current location selected");
                    console.log("lat:", lat.value, "lng:", lng.value);
                },
                (error) => {
                    toast.error(
                        "Error getting current location: " + error.message
                    );
                    console.error(error);
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0,
                }
            );
        } else {
            lat.value = lat_code.value;
            lng.value = lng_code.value;
            console.log("Manual location selected");
            console.log("lat:", lat.value, "lng:", lng.value);
        }
    },
    { immediate: true } // run once on component mount, optional
);
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
                            <li class="breadcrumb-item">
                                <a :href="route('dealer.index')">Dealer</a>
                            </li>
                            <li class="breadcrumb-item active">
                                Dealer Create
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
                                        Owner Details
                                    </h5>

                                    <div class="form-row">
                                        <div class="col-md-6 mb-3">
                                            <label for="dealers_first_name"
                                                >First Name</label
                                            >
                                            <input
                                                type="text"
                                                class="form-control"
                                                id="dealers_first_name"
                                                required
                                                placeholder="First Name"
                                                v-model="dealer_first_name"
                                            />
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="dealers_last_name"
                                                >Last Name</label
                                            >
                                            <input
                                                type="text"
                                                class="form-control"
                                                id="dealers_last_name"
                                                required
                                                placeholder="Last Name"
                                                v-model="dealer_last_name"
                                            />
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="dealers_nic">NIC</label>
                                            <input
                                                type="text"
                                                class="form-control"
                                                id="dealers_nic_name"
                                                required
                                                placeholder="NIC"
                                                v-model="dealer_nic"
                                            />
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="dealers_contact_number"
                                                >Contact number</label
                                            >
                                            <input
                                                type="text"
                                                class="form-control"
                                                id="dealers_contact_number"
                                                required
                                                placeholder="Contact Number"
                                                v-model="dealer_contact_number"
                                            />
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="dealers_contact_number"
                                                >Address</label
                                            >
                                            <input
                                                type="text"
                                                class="form-control"
                                                id="dealers_address"
                                                required
                                                placeholder="Address"
                                                v-model="dealer_address"
                                            />
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="dealers_email"
                                                >Email</label
                                            >
                                            <input
                                                type="email"
                                                class="form-control"
                                                id="dealers_address"
                                                required
                                                placeholder="Email address"
                                                v-model="dealer_email"
                                            />
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="nic_copy"
                                                >NIC Photo</label
                                            >
                                            <input
                                                id="nic_copy"
                                                type="file"
                                                class="form-control"
                                                accept="image/*"
                                                required
                                            />
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="owner_position"
                                                >Position</label
                                            >
                                            <input
                                                type="text"
                                                class="form-control"
                                                id="owner_position"
                                                required
                                                placeholder="Position"
                                                v-model="owner_position"
                                            />
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
                                    <div class="form-row">
                                        <div class="col-md-6 mb-3">
                                            <label for="business_name"
                                                >Business Name</label
                                            >
                                            <input
                                                type="text"
                                                class="form-control"
                                                id="business_name"
                                                required
                                                placeholder="Business Name"
                                                v-model="business_name"
                                            />
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="business_address"
                                                >Address of the Business</label
                                            >
                                            <input
                                                type="text"
                                                class="form-control"
                                                id="business_address"
                                                required
                                                placeholder="Address of the Business"
                                                v-model="business_address"
                                            />
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="business_tel"
                                                >Business Tell</label
                                            >
                                            <input
                                                type="text"
                                                class="form-control"
                                                id="business_tel"
                                                required
                                                placeholder="Business Tel"
                                                v-model="business_tel"
                                            />
                                        </div>
                                        <div class="col-md-6" :class="location_type == 'current' ?? 'mb-3'">
                                            <div class="form-group">
                                                <label
                                                    for="exampleFormControlSelect1"
                                                    >Location Type</label
                                                >
                                                <select
                                                    class="form-control"
                                                    id="exampleFormControlSelect1"
                                                    v-model="location_type"
                                                >
                                                    <option value="current">
                                                        Current Location
                                                    </option>
                                                    <option value="custom">
                                                        Custom Location
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div
                                            class="col-12 d-flex custom-location"
                                            v-if="location_type == 'custom'"
                                        >
                                         <div class="col-md-6 pl-0 mb-3">
                                                <label for="lat-code"
                                                    >Lat Code</label
                                                >
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    id="lat-code"
                                                    required
                                                    placeholder="Lat Code"
                                                    v-model="lat"
                                                />
                                            </div>
                                            <div class="col-md-6 pl-0 mb-3">
                                                <label for="lat-code"
                                                    >Lng Code</label
                                                >
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    id="lng-code"
                                                    required
                                                    placeholder="Lng Code"
                                                    v-model="lng"
                                                />
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="registration_doc"
                                                >Business Registration
                                                (PDF)</label
                                            >
                                            <input
                                                id="registration_doc"
                                                type="file"
                                                class="form-control"
                                                accept="application/pdf"
                                                required
                                            />
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="sign_application"
                                                >Signed Credit Application
                                                (PDF)</label
                                            >
                                            <input
                                                id="sign_application"
                                                type="file"
                                                class="form-control"
                                                accept="application/pdf"
                                                required
                                            />
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="photo_of_the_shop"
                                                >Photo of the shop</label
                                            >
                                            <input
                                                id="photo_of_the_shop"
                                                type="file"
                                                class="form-control"
                                                accept="image/*"
                                                required
                                            />
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
                                                Create
                                            </button>
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
                            <div class="card-body" style="padding: 0px">
                                <div class="w-full" style="height: 360px">
                                    <iframe
                                        :src="`https://www.google.com/maps?q=${lat},${lng}&z=16&output=embed`"
                                        width="100%"
                                        height="100%"
                                        style="border: 0"
                                        loading="lazy"
                                        allowfullscreen
                                        referrerpolicy="no-referrer-when-downgrade"
                                    ></iframe>
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
.custom-location .col-md-6 {
   transition: all 1s ease;
}
</style>
