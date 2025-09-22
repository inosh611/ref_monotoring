<script setup>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref } from "vue";
import { useToast } from "vue-toastification";
import { store } from "../../../main";
import DataTable from "@/Components/Admin/DataTable.vue";

const toast = useToast();
const dealer_first_name = ref("");
const shop_photo = ref("");
const formRef = ref(null);
const time = ref("");
const date = ref("");
const dealer_id = ref("");
const location = ref({ lat: null, lng: null });

const props = defineProps({
    roles: Array,
    dealers: Array,
});
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

function getCurrentLocation() {
    return new Promise((resolve, reject) => {
        if (!navigator.geolocation) {
            reject("Geolocation is not supported by this browser.");
        }

        navigator.geolocation.getCurrentPosition(
            (position) => {
                resolve({
                    lat: position.coords.latitude,
                    lng: position.coords.longitude,
                });
            },
            (error) => {
                reject(error.message);
            }
        );
    });
}
async function fetchLocation() {
    try {
        location.value = await getCurrentLocation();
        console.log("Current location:", location.value);
    } catch (err) {
        console.error("Error getting location:", err);
    }
}
async function handleSubmit() {
    const form = formRef.value;
    if (form.checkValidity() === false) {
        toast.error("Please fill out all required fields.");
        form.classList.add("was-validated");
        return;
    }
    const formData = new FormData();

    formData.append("dealer_id", dealer_id.value);
    formData.append("time", time.value);
    formData.append("location", JSON.stringify(location.value));
    formData.append("date", date.value);

    const photo_of_the_shop = document.getElementById("photo_of_the_shop");
    if (photo_of_the_shop.files[0])
        formData.append("photo_of_the_shop", photo_of_the_shop.files[0]);

    store("my.visiting.store", formData);
}
function uploadPhoto(event) {
    shop_photo.value = event.target.files[0];
    if (!shop_photo.value) return;
    time.value = getCurrentTime();
    date.value = getCurrentDate();
    fetchLocation();
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
                                            <label for="photo_of_the_shop"
                                                >Select Dealer</label
                                            >
                                            <select
                                                class="custom-select"
                                                v-model="dealer_id"
                                                required
                                            >
                                                <option selected value="">
                                                    Open this select menu
                                                </option>
                                                <option
                                                    v-for="dealer in dealers"
                                                    :value="dealer.id"
                                                >
                                                    {{ dealer.business_name }} -
                                                    {{
                                                        dealer.business_address
                                                    }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="photo_of_the_shop"
                                                >Shop Photo</label
                                            >
                                            <input
                                                id="photo_of_the_shop"
                                                type="file"
                                                class="form-control"
                                                accept="image/*"
                                                capture="environment"
                                                @change="uploadPhoto"
                                                :disabled="dealer_id === ''"
                                                required
                                            />
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="owner_position"
                                                >Time</label
                                            >
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
                                        <div class="col-md-6 mb-3">
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
                                        <div class="col-md-6 mb-3">
                                            <label for="location"
                                                >Location</label
                                            >
                                            <input
                                                type="text"
                                                class="form-control"
                                                id="date"
                                                required
                                                placeholder="Location"
                                                :value="`Lat: ${location.lat}, Lng: ${location.lng}`"
                                                readonly
                                                disabled
                                            />
                                        </div>
                                        <div
                                            class="col-md-6 mb-3 d-flex align-items-center mt-4"
                                        >
                                            <button
                                                type="submit"
                                                class="btn btn-primary"
                                            >
                                                Submit
                                            </button>
                                        </div>
                                        <div>
                                            <p v-if="location.lat">
                                                📍 Lat: {{ location.lat }}, Lng:
                                                {{ location.lng }}
                                            </p>
                                        </div>
                                        <iframe
                                            :src="`https://www.google.com/maps?q=${location.lat},${location.lng}&hl=es;z=14&output=embed`"
                                            width="100%"
                                            height="300"
                                            style="border: 0"
                                            allowfullscreen=""
                                            loading="lazy"
                                        ></iframe>
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
</style>
