<script setup>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue";
import DataTable from "@/Components/Admin/DataTable.vue";
import { Head } from "@inertiajs/vue3";
import { onMounted, ref, computed, watch } from "vue";
import { useToast } from "vue-toastification";
import { store, searchData } from "../../../main";
import $ from "jquery";
import { ToWords } from "to-words";

const toast = useToast();
const formRef = ref(null);
const collection_type = ref(1);
const cheque_number = ref("");
const order_number = ref("");
const bank = ref("");
const branch = ref("");
const cheque_date = ref("");
const cheque_amount = ref(0);
const cheque_amount_text = ref("");
const cheque_type = ref("own_cheque");
const receipt_number = ref("");
const cash_amount = ref(0);
const cash_amount_text = ref("");
const cash_receipt_number = ref("");
const total_order_amount = ref(0.00);
const paid_amount = ref(0.00);
const balance_amount = ref(0.00);
const comment = ref("");
const orderDetails = [];

const toWords = new ToWords({
    localeCode: "en-GB",
    converterOptions: {
        currency: true,
        ignoreDecimal: false,
        doNotAddOnly: false,
        currencyOptions: {
            name: "Rupee",
            plural: "Rupees",
            symbol: "Rs.",
            fractionalUnit: { name: "Cent", plural: "Cents", symbol: "" },
        },
    },
});

const searchOrder = async (searchKey) => {
    try {
        const { data } = await axios.get(route('my.collection.search.order'), {
            params: { search: searchKey },
        });
        return data;
    } catch (e) {
        console.error(e);
        return [];
    }
};

watch(cheque_amount, (val) => {
    const n = Number(val);
    cheque_amount_text.value =
        !Number.isNaN(n) && val !== "" ? toWords.convert(n) : "";
        if(val > balance_amount.value){
            toast.error("Cheque amount cannot be greater than balance amount.");
            cheque_amount.value = 0;
            cheque_amount_text.value = "";
        }
});

watch(order_number, async (val) => {
  if (!val) { orderDetails.value = null; return; }

  try {
    const res = await searchOrder(val);   // ⬅️ await the Promise
    total_order_amount.value = res.data ? parseFloat(res.data.total_price).toFixed(2) : 0.00;
    paid_amount.value = res.data ? parseFloat(res.data.paid_amount).toFixed(2) : 0.00;
    balance_amount.value = res.data ? (parseFloat(res.data.total_price) - parseFloat(res.data.paid_amount)).toFixed(2) : 0.00;
    console.log('Order Details', res.data.total_price);
  } catch (e) {
    console.error(e);
    orderDetails.value = null;
  }
});
watch(cash_amount, (val) => {
    const n = Number(val);
    cash_amount_text.value =
        !Number.isNaN(n) && val !== "" ? toWords.convert(n) : "";
        if(val > balance_amount.value){
            toast.error("Cash amount cannot be greater than balance amount.");
            cash_amount.value = 0;
            cash_amount_text.value = "";
        }
});

watch(collection_type, (val) => {
    cheque_number.value = "";
    bank.value = "";
    branch.value = "";
    cheque_date.value = "";
    cheque_amount.value = 0;
    cheque_amount_text.value = "";
    cheque_type.value = "own_cheque";
    receipt_number.value = "";
    cash_amount.value = 0;
    cash_amount_text.value = "";
    cash_receipt_number.value = "";
});
async function handleSubmit() {
    const form = formRef.value;
    if (form.checkValidity() === false) {
        toast.error("Please fill out all required fields.");
        form.classList.add("was-validated");
        return;
    }
    const formData = new FormData();
    formData.append("collection_type", collection_type.value);
    formData.append("order_number", order_number.value);
    formData.append("comment", comment.value);
    if (collection_type.value == 1) {
        formData.append("cheque_number", cheque_number.value);
        formData.append("bank", bank.value);
        formData.append("branch", branch.value);
        formData.append("cheque_date", cheque_date.value);
        formData.append("cheque_amount", cheque_amount.value);
        formData.append("cheque_amount_text", cheque_amount_text.value);
        formData.append("receipt_number", receipt_number.value);
        formData.append("cheque_type", cheque_type.value);
        formData.append("receipt_number", receipt_number.value);
    }else{
         formData.append("cash_amount", cash_amount.value);
         formData.append("cash_amount_text", cash_amount_text.value);
         formData.append("cash_receipt_number", cash_receipt_number.value);
    }

    store("my.collection.store", formData);
}

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
                        <h4 class="m-0">
                            CREATE COLLECTION {{ exampleRadios }}
                        </h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <a :href="route('dashboard')">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active">
                                Create Collection
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dashboard Content -->
        <section class="content">
            <div class="container-fluid">
                <div class="col-12">
                    <div class="card card-default">
                        <div class="card-body able-card-body">
                            <div class="row mt-2 mml-2">
                                <h4>Collection Details</h4>
                            </div>
                            <form
                                class="needs-validation"
                                novalidate
                                @submit.prevent="handleSubmit"
                                ref="formRef"
                            >
                                <div class="row">
                                    <div class="col-lg-4 col-12">
                                        <div class="form-group">
                                            <label for="order-number"
                                                >Order Number</label
                                            >
                                            <input
                                                type="text"
                                                class="form-control"
                                                id="order-number"
                                                placeholder="Order Number"
                                                v-model="order_number"
                                                required
                                            />
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-12">
                                        <div class="form-group">
                                            <label for="total-amount"
                                                >Total Amount</label
                                            >
                                            <input
                                                type="number"
                                                class="form-control"
                                                id="total-amount"
                                                placeholder="Total Amount"
                                                step="0.01"
                                                min="0"
                                                disabled
                                                v-model="total_order_amount"
                                                required
                                            />
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-12">
                                        <div class="form-group">
                                            <label for="paid-amount"
                                                >Paid Amount</label
                                            >
                                            <input
                                                type="number"
                                                class="form-control"
                                                id="paid-amount"
                                                placeholder="Paid Amount"
                                                step="0.01"
                                                min="0"
                                                disabled
                                                v-model="paid_amount"
                                                required
                                            />
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-12">
                                        <div class="form-group">
                                            <label for="balance-amount"
                                                >Balance Amount</label
                                            >
                                            <input
                                                type="number"
                                                class="form-control"
                                                id="balance-amount"
                                                placeholder="Balance Amount"
                                                step="0.01"
                                                min="0"
                                                disabled
                                                v-model="balance_amount"
                                                required
                                            />
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-12">
                                        <div class="form-group">
                                            <label for="collection-type"
                                                >Collection Type</label
                                            >
                                            <select
                                                class="form-control"
                                                id="collection-type"
                                                v-model="collection_type"
                                            >
                                                <option :value="1">
                                                    Cheque
                                                </option>
                                                <option :value="2">Cash</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div
                                            class="row"
                                            v-if="collection_type == 1"
                                        >
                                            <div class="col-lg-4 col-12">
                                                <div class="form-group">
                                                    <label for="cheque-number"
                                                        >Cheque Number</label
                                                    >
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        id="cheque-number"
                                                        placeholder="Cheque Number"
                                                        v-model="cheque_number"
                                                        required
                                                    />
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-12">
                                                <div class="form-group">
                                                    <label for="collection-type"
                                                        >Bank</label
                                                    >
                                                    <select
                                                        class="form-control"
                                                        id="bank"
                                                        v-model="bank"
                                                    >
                                                        <option
                                                            value=""
                                                            selected
                                                            disabled
                                                        >
                                                            Select Bank
                                                        </option>
                                                        <option
                                                            value="bank_of_ceylon"
                                                        >
                                                            Bank of Ceylon (BOC)
                                                        </option>
                                                        <option
                                                            value="peoples_bank"
                                                        >
                                                            People’s Bank
                                                        </option>
                                                        <option
                                                            value="national_savings_bank"
                                                        >
                                                            National Savings
                                                            Bank (NSB)
                                                        </option>
                                                        <option
                                                            value="commercial_bank_of_ceylon"
                                                        >
                                                            Commercial Bank of
                                                            Ceylon PLC
                                                        </option>
                                                        <option
                                                            value="hatton_national_bank"
                                                        >
                                                            Hatton National Bank
                                                            PLC (HNB)
                                                        </option>
                                                        <option
                                                            value="sampath_bank"
                                                        >
                                                            Sampath Bank PLC
                                                        </option>
                                                        <option
                                                            value="seylan_bank"
                                                        >
                                                            Seylan Bank PLC
                                                        </option>
                                                        <option
                                                            value="nations_trust_bank"
                                                        >
                                                            Nations Trust Bank
                                                            PLC (NTB)
                                                        </option>
                                                        <option
                                                            value="dfcc_bank"
                                                        >
                                                            DFCC Bank PLC
                                                        </option>
                                                        <option
                                                            value="ndb_bank"
                                                        >
                                                            National Development
                                                            Bank PLC (NDB Bank)
                                                        </option>
                                                        <option
                                                            value="pan_asia_bank"
                                                        >
                                                            Pan Asia Banking
                                                            Corporation PLC
                                                        </option>
                                                        <option
                                                            value="union_bank"
                                                        >
                                                            Union Bank of
                                                            Colombo PLC
                                                        </option>
                                                        <option
                                                            value="cargills_bank"
                                                        >
                                                            Cargills Bank PLC
                                                        </option>
                                                        <option
                                                            value="amana_bank"
                                                        >
                                                            Amãna Bank PLC
                                                        </option>
                                                        <option
                                                            value="sdb_bank"
                                                        >
                                                            Sanasa Development
                                                            Bank PLC (SDB bank)
                                                        </option>
                                                        <option
                                                            value="hdfc_bank_sl"
                                                        >
                                                            HDFC Bank of Sri
                                                            Lanka
                                                        </option>
                                                        <option
                                                            value="regional_development_bank"
                                                        >
                                                            Regional Development
                                                            Bank (RDB)
                                                        </option>
                                                        <option
                                                            value="state_mortgage_investment_bank"
                                                        >
                                                            State Mortgage &amp;
                                                            Investment Bank
                                                            (SMIB)
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-12">
                                                <div class="form-group">
                                                    <label for="branch-name"
                                                        >Branch Name</label
                                                    >
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        id="branch-name"
                                                        placeholder="Branch Name"
                                                        v-model="branch"
                                                        required
                                                        :disabled="!bank"
                                                    />
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-12">
                                                <div class="form-group">
                                                    <label for="cheque-date"
                                                        >Date</label
                                                    >
                                                    <input
                                                        type="date"
                                                        class="form-control"
                                                        id="cheque-date"
                                                        placeholder="Cheque Date"
                                                        v-model="cheque_date"
                                                        required
                                                    />
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-12">
                                                <div class="form-group">
                                                    <label for="cheque-amount"
                                                        >Amount</label
                                                    >
                                                    <input
                                                        type="number"
                                                        step="0.01"
                                                        min="0"
                                                        class="form-control"
                                                        id="cheque-amount"
                                                        placeholder="Cheque Amount"
                                                        v-model="cheque_amount"
                                                        required
                                                    />
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-12">
                                                <div class="form-group">
                                                    <label for="cheque-amount"
                                                        >Amount Text</label
                                                    >
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        id="text-amount"
                                                        placeholder="Text Amount"
                                                        v-model="
                                                            cheque_amount_text
                                                        "
                                                        required
                                                    />
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-12">
                                                <div class="form-group">
                                                    <label for="receipt-number"
                                                        >Receipt number</label
                                                    >
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        id="receipt-number"
                                                        placeholder="Receipt number"
                                                        v-model="receipt_number"
                                                        required
                                                    />
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-12 d-flex">
                                                <div class="form-check mt-4">
                                                    <input
                                                        class="form-check-input"
                                                        type="radio"
                                                        name="own_cheque"
                                                        id="own_cheque"
                                                        value="own_cheque"
                                                        v-model="cheque_type"
                                                        checked
                                                    />
                                                    <label
                                                        class="form-check-label mr-2"
                                                        for="own_cheque"
                                                    >
                                                        Own cheque
                                                    </label>
                                                </div>
                                                <div class="form-check mt-4">
                                                    <input
                                                        class="form-check-input"
                                                        type="radio"
                                                        name="Party Cheque"
                                                        id="party_cheque"
                                                        value="Party Cheque"
                                                        v-model="cheque_type"
                                                    />
                                                    <label
                                                        class="form-check-label"
                                                        for="party_cheque"
                                                    >
                                                        Party Cheque
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row" v-else>
                                            <div class="col-lg-4 col-12">
                                                <div class="form-group">
                                                    <label for="cheque-amount"
                                                        >Amount</label
                                                    >
                                                    <input
                                                        type="number"
                                                        step="0.01"
                                                        min="0"
                                                        class="form-control"
                                                        id="cash-amount"
                                                        placeholder="Cash Amount"
                                                        v-model="cash_amount"
                                                        required
                                                    />
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-12">
                                                <div class="form-group">
                                                    <label
                                                        for="cash-amount-text"
                                                        >Amount Text</label
                                                    >
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        id="cash-text-amount"
                                                        placeholder="Cash Text Amount"
                                                        v-model="
                                                            cash_amount_text
                                                        "
                                                        required
                                                    />
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-12">
                                                <div class="form-group">
                                                    <label
                                                        for="cash-receipt-number"
                                                        >Receipt number</label
                                                    >
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        id="cash-receipt-number"
                                                        placeholder="Receipt number"
                                                        v-model="
                                                            cash_receipt_number
                                                        "
                                                        required
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12 col-12">
                                                <div class="form-group">
                                                    <label
                                                        for="comment"
                                                        >Comment</label
                                                    >
                                                    <textarea
                                                        type="text"
                                                        class="form-control"
                                                        id="comment"
                                                        placeholder="Comment"
                                                        v-model="comment"
                                                        
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div
                                                class="col-12 d-flex justify-content-end"
                                            >
                                                <button
                                                    type="submit"
                                                    class="btn btn-primary"
                                                >
                                                    CREATE
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
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
.table-trash-icon {
    cursor: pointer;
}
.table-card-body {
    overflow-x: scroll;
}
.table-card-body {
    max-height: 400px;
    overflow-y: scroll;
}
.search-result-box {
    max-height: 300px;
    overflow-y: scroll;
    border: 1px solid #ced4da;
    border-radius: 5px;
    position: absolute;
    z-index: 1;
    width: 94%;
    background: rgb(253 253 253);
}
ul {
    list-style-type: none;
    padding: 0;
}

ul li {
    padding: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
}
Ul li:hover {
    background: #ced4da;
    cursor: pointer;
}
.item-search-box {
    margin-bottom: 0;
}
@media (max-width: 900px) {
    .item-add-btn {
        width: 100%;
    }
    /* .col-12 {
        padding-left: 0 !important;
        padding-right: 0 !important;
    } */
}
</style>
