<script setup>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref, watch, computed, onBeforeUnmount } from "vue";
import { useToast } from "vue-toastification";
import { update } from "../../../main";
import DataTable from "@/Components/Admin/DataTable.vue";
import SriLankaMapPickerPro from "@/Components/SriLankaMapPicker.vue";

const picked = ref(null); // becomes { lat, lng } after user selects

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
const location_code = ref("");

const nic_view = ref(""); // your existing server path string (e.g. set from props)
const dealer_photo_view = ref(""); // your existing server path string (e.g. set from props)
const nicFile = ref(null); // the newly picked file
const shopFile = ref(null); // the newly picked file
const nicObjectUrl = ref(null);
const shopObjectUrl = ref(null);
const lng = ref("");
const lat = ref("");
const formRef = ref(null);
const change_nic_copy = ref(null);

const props = defineProps({
    roles: Array,
    dealerDetails: Array,
});

business_name.value = props.dealerDetails.business_name;
business_address.value = props.dealerDetails.business_address;
business_tel.value = props.dealerDetails.business_tel;
dealer_first_name.value = props.dealerDetails.owner.first_name;
dealer_last_name.value = props.dealerDetails.owner.last_name;
dealer_nic.value = props.dealerDetails.owner.nic;
dealer_contact_number.value = props.dealerDetails.owner.contact_number;
dealer_address.value = props.dealerDetails.owner.address;
dealer_email.value = props.dealerDetails.owner.email;
owner_position.value = props.dealerDetails.owner.owner_position;
nic_view.value = props.dealerDetails.owner.nic_copy;
dealer_photo_view.value = props.dealerDetails.photo_of_the_shop;
lat.value = props.dealerDetails.lat;
lng.value = props.dealerDetails.lng;

const onNicChange = (e) => {
    const file = e.target.files?.[0] || null;
    nicFile.value = file;

    // cleanup any previous blob URL
    if (nicObjectUrl.value) URL.revokeObjectURL(nicObjectUrl.value);
    nicObjectUrl.value = file ? URL.createObjectURL(file) : null;
};

const onShopChange = (e) => {
    const file = e.target.files?.[0] || null;
    shopFile.value = file;

    if (shopObjectUrl.value) URL.revokeObjectURL(shopObjectUrl.value);
    shopObjectUrl.value = file ? URL.createObjectURL(file) : null;
};
// What the <img> should show: new preview if present, else existing server image
const nicImgSrc = computed(
    () =>
        nicObjectUrl.value ||
        (nic_view.value ? `/storage/${nic_view.value}` : "")
);
const shopImgSrc = computed(
    () =>
        shopObjectUrl.value ||
        (dealer_photo_view.value ? `/storage/${dealer_photo_view.value}` : "")
);

onBeforeUnmount(() => {
    if (nicObjectUrl.value) URL.revokeObjectURL(nicObjectUrl.value);
    if (shopObjectUrl.value) URL.revokeObjectURL(shopObjectUrl.value);
});

async function handleSubmit() {
    const form = formRef.value;
    if (form.checkValidity() === false) {
        toast.error("Please fill out all required fields.");
        form.classList.add("was-validated");
        return;
    }
    const formData = new FormData();

    formData.append("dealer_id", props.dealerDetails.id);
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
    // send existing paths so backend can keep them if no new file is uploaded
    formData.append("current_nic_copy", nic_view.value || "");
    formData.append("current_photo_of_the_shop", dealer_photo_view.value || "");
    formData.append(
        "current_registration_doc",
        props.dealerDetails.registration_doc || ""
    );
    formData.append(
        "current_sign_application",
        props.dealerDetails.sign_application || ""
    );

    const regInput = document.getElementById("registration_doc");
    const signInput = document.getElementById("sign_application");
    const nic_copy = document.getElementById("nic_copy");
    const photo_of_the_shop = document.getElementById("photo_of_the_shop");

    const appendFileOrEmpty = (el, key) => {
        const file = el?.files?.[0];
        if (file) formData.append(key, file); // sends the new file
        else formData.append(key, ""); // sends empty string
    };

    appendFileOrEmpty(regInput, "registration_doc");
    appendFileOrEmpty(signInput, "sign_application");
    appendFileOrEmpty(nic_copy, "nic_copy");
    appendFileOrEmpty(photo_of_the_shop, "photo_of_the_shop");

    update("dealer.update", formData);
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
                                        <div class="col-md-6 mb-3">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <label
                                                        >Current NIC
                                                        Photo</label
                                                    ><br />
                                                    <img
                                                        :src="nicImgSrc"
                                                        alt=""
                                                        width="100%"
                                                        height="200px"
                                                        style="
                                                            border: 1px solid;
                                                        "
                                                    />
                                                </div>
                                                <div class="col-lg-4">
                                                    <label
                                                        for="nic_copy"
                                                        class="btn btn-primary btn-change-nic"
                                                        >Change NIC Photo</label
                                                    >
                                                    <input
                                                        id="nic_copy"
                                                        type="file"
                                                        class="form-control d-none"
                                                        accept="image/*"
                                                        @change="onNicChange"
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
                                        <div class="col-md-6 mb-3">
                                            <div class="row pl-4">
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
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="row pl-4">
                                                <label for=""
                                                    >Business Registration
                                                    (PDF)</label
                                                >
                                            </div>
                                            <div class="row pl-4">
                                                <div class="col-lg-6">
                                                    <div>
                                                        <a
                                                            :href="`/storage/${props.dealerDetails.registration_doc}`"
                                                            target="_blank"
                                                            class="text-bold text-danger"
                                                            >View Current
                                                            File</a
                                                        >
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <input
                                                        id="registration_doc"
                                                        type="file"
                                                        class="file-input"
                                                        accept="application/pdf"
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="row pl-4">
                                                <label for="sign_application"
                                                    >Signed Credit Application
                                                    (PDF)</label
                                                >
                                            </div>
                                            <div class="row pl-4">
                                                <div class="col-lg-6">
                                                    <div>
                                                        <a
                                                            :href="`/storage/${props.dealerDetails.registration_doc}`"
                                                            target="_blank"
                                                            class="text-bold text-danger"
                                                            >View Current
                                                            File</a
                                                        >
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <input
                                                        id="sign_application"
                                                        type="file"
                                                        class="file-input"
                                                        accept="application/pdf"
                                                    />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <label
                                                        >Photo of the
                                                        shop</label
                                                    ><br />
                                                    <img
                                                        :src="shopImgSrc"
                                                        alt=""
                                                        width="100%"
                                                        height="200px"
                                                        style="
                                                            border: 1px solid;
                                                        "
                                                    />
                                                </div>
                                                <div class="col-lg-4">
                                                    <label
                                                        for="photo_of_the_shop"
                                                        class="btn btn-primary btn-change-nic"
                                                        >Change Shop
                                                        Photo</label
                                                    >
                                                    <input
                                                        id="photo_of_the_shop"
                                                        type="file"
                                                        class="form-control d-none"
                                                        accept="image/*"
                                                        @change="onShopChange"
                                                    />
                                                </div>
                                            </div>
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
                                                update
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
.btn-change-nic {
    position: absolute;
    top: 80%;
}

:deep(.file-input)::file-selector-button {
    background: #2563eb;
    color: #fff;
    border: 0;
    padding: 6px 12px;
    border-radius: 6px;
    cursor: pointer;
}

:deep(.file-input):hover::file-selector-button {
    background: #1d4ed8;
}
:deep(.file-input):focus-visible::file-selector-button {
    outline: 2px solid #93c5fd;
}

:deep(.file-input)::-webkit-file-upload-button {
    background: #2563eb;
    color: #fff;
    border: 0;
    padding: 6px 12px;
    border-radius: 4px;
    cursor: pointer;
}
</style>
