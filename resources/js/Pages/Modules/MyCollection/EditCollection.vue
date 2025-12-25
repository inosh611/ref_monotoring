<script setup>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue";
import { Head } from "@inertiajs/vue3";
import { onMounted, ref, watch } from "vue";
import { useToast } from "vue-toastification";
import { update } from "../../../main"; // ✅ your common axios update()
import { ToWords } from "to-words";

const toast = useToast();
const formRef = ref(null);

// ✅ props from controller edit()
const props = defineProps({
  payment: Object,
});

// ✅ payment id (sent in FormData)
const id = ref(null);

// form refs
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

const total_order_amount = ref("0.00");
const paid_amount = ref("0.00");
const balance_amount = ref("0.00");
const comment = ref("");

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
    const { data } = await axios.get(route("my.collection.search.order"), {
      params: { search: searchKey },
    });
    return data;
  } catch (e) {
    console.error(e);
    return null;
  }
};

// ✅ update totals when order number changes
watch(order_number, async (val) => {
  if (!val) return;

  const res = await searchOrder(val);
  if (res?.data) {
    total_order_amount.value = parseFloat(res.data.total_price).toFixed(2);
    paid_amount.value = parseFloat(res.data.paid_amount).toFixed(2);
    balance_amount.value = (
      parseFloat(res.data.total_price) - parseFloat(res.data.paid_amount)
    ).toFixed(2);
  } else {
    total_order_amount.value = "0.00";
    paid_amount.value = "0.00";
    balance_amount.value = "0.00";
  }
});

// ✅ convert amounts to words + simple balance validation
watch(cheque_amount, (val) => {
  const n = Number(val);
  cheque_amount_text.value = !Number.isNaN(n) && val !== "" ? toWords.convert(n) : "";
  if (Number(val) > Number(balance_amount.value)) {
    toast.error("Cheque amount cannot be greater than balance amount.");
    cheque_amount.value = 0;
    cheque_amount_text.value = "";
  }
});

watch(cash_amount, (val) => {
  const n = Number(val);
  cash_amount_text.value = !Number.isNaN(n) && val !== "" ? toWords.convert(n) : "";
  if (Number(val) > Number(balance_amount.value)) {
    toast.error("Cash amount cannot be greater than balance amount.");
    cash_amount.value = 0;
    cash_amount_text.value = "";
  }
});

// ✅ reset type-specific fields when switching
watch(collection_type, () => {
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

onMounted(async () => {
  const p = props.payment;
  console.log("Edit Details:", p);

  // ✅ set payment id
  id.value = p.id;

  // ✅ fill base
  collection_type.value = p.collection_type == "Cheque" ? 1 : 2;
  order_number.value = p.order?.order_number ?? "";
  comment.value = p.comment ?? "";

  // ✅ fill totals from order search API (optional)
  if (order_number.value) {
    const res = await searchOrder(order_number.value);
    if (res?.data) {
      total_order_amount.value = parseFloat(res.data.total_price).toFixed(2);
      paid_amount.value = parseFloat(res.data.paid_amount).toFixed(2);
      balance_amount.value = (
        parseFloat(res.data.total_price) - parseFloat(res.data.paid_amount)
      ).toFixed(2);
    }
  }

  // ✅ fill cheque/cash existing data
  if (collection_type.value === 1 && p.cheque) {
    cheque_number.value = p.cheque.cheque_number ?? "";
    bank.value = p.cheque.bank ?? "";
    branch.value = p.cheque.branch ?? "";
    cheque_date.value = p.cheque.cheque_date ?? "";
    cheque_amount.value = p.cheque.cheque_amount ?? 0;
    cheque_amount_text.value = p.cheque.cheque_amount_text ?? "";
    cheque_type.value = p.cheque.cheque_type ?? "own_cheque";
    receipt_number.value = p.cheque.receipt_number ?? "";
  }

  if (collection_type.value === 2 && p.cash) {
    cash_amount.value = p.cash.cash_amount ?? 0;
    cash_amount_text.value = p.cash.cash_amount_text ?? "";
    cash_receipt_number.value = p.cash.cash_receipt_number ?? "";
  }

});

async function handleSubmit() {
  const form = formRef.value;

  if (form.checkValidity() === false) {
    toast.error("Please fill out all required fields.");
    form.classList.add("was-validated");
    return;
  }

  const formData = new FormData();

  // ✅ IMPORTANT: id inside form data (your requirement)
  formData.append("id", id.value);

  formData.append("collection_type", collection_type.value);
  formData.append("order_number", order_number.value);
  formData.append("comment", comment.value ?? "");

  if (collection_type.value == 1) {
    formData.append("cheque_number", cheque_number.value);
    formData.append("bank", bank.value);
    formData.append("branch", branch.value);
    formData.append("cheque_date", cheque_date.value);
    formData.append("cheque_amount", cheque_amount.value);
    formData.append("cheque_amount_text", cheque_amount_text.value);
    formData.append("receipt_number", receipt_number.value);
    formData.append("cheque_type", cheque_type.value);
  } else {
    formData.append("cash_amount", cash_amount.value);
    formData.append("cash_amount_text", cash_amount_text.value);
    formData.append("cash_receipt_number", cash_receipt_number.value);
  }

  // ✅ EDIT must call update route (not store)
  update("my.collection.update", formData, "Collection");
}
</script>

<template>
  <AdminLayout>
    <Head title="Edit Collection" />

    <!-- Header -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h4 class="m-0">EDIT COLLECTION</h4>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item">
                <a :href="route('dashboard')">Dashboard</a>
              </li>
              <li class="breadcrumb-item active">Edit Collection</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <!-- Content -->
    <section class="content">
      <div class="container-fluid">
        <div class="col-12">
          <div class="card card-default">
            <div class="card-body able-card-body">
              <div class="row mt-2 mml-2">
                <h4>Collection Details</h4>
                <pre>
                    {{ p }}
                </pre>
              </div>

              <form class="needs-validation" novalidate @submit.prevent="handleSubmit" ref="formRef">
                <div class="row">

                  <!-- Order Number -->
                  <div class="col-lg-4 col-12">
                    <div class="form-group">
                      <label for="order-number">Order Number</label>
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

                  <!-- Total -->
                  <div class="col-lg-4 col-12">
                    <div class="form-group">
                      <label for="total-amount">Total Amount</label>
                      <input
                        type="number"
                        class="form-control"
                        id="total-amount"
                        step="0.01"
                        disabled
                        v-model="total_order_amount"
                      />
                    </div>
                  </div>

                  <!-- Paid -->
                  <div class="col-lg-4 col-12">
                    <div class="form-group">
                      <label for="paid-amount">Paid Amount</label>
                      <input
                        type="number"
                        class="form-control"
                        id="paid-amount"
                        step="0.01"
                        disabled
                        v-model="paid_amount"
                      />
                    </div>
                  </div>

                  <!-- Balance -->
                  <div class="col-lg-6 col-12">
                    <div class="form-group">
                      <label for="balance-amount">Balance Amount</label>
                      <input
                        type="number"
                        class="form-control"
                        id="balance-amount"
                        step="0.01"
                        disabled
                        v-model="balance_amount"
                      />
                    </div>
                  </div>

                  <!-- Type -->
                  <div class="col-lg-6 col-12">
                    <div class="form-group">
                      <label for="collection-type">Collection Type</label>
                      <select class="form-control" id="collection-type" v-model="collection_type">
                        <option :value="1">Cheque</option>
                        <option :value="2">Cash</option>
                      </select>
                    </div>
                  </div>

                  <div class="col-12">
                    <!-- Cheque -->
                    <div class="row" v-if="collection_type == 1">
                      <div class="col-lg-4 col-12">
                        <div class="form-group">
                          <label for="cheque-number">Cheque Number</label>
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
                          <label for="bank">Bank</label>
                          <select class="form-control" id="bank" v-model="bank" required>
                            <option value="" disabled>Select Bank</option>
                            <option value="bank_of_ceylon">Bank of Ceylon (BOC)</option>
                            <option value="peoples_bank">People’s Bank</option>
                            <option value="national_savings_bank">National Savings Bank (NSB)</option>
                            <option value="commercial_bank_of_ceylon">Commercial Bank of Ceylon PLC</option>
                            <option value="hatton_national_bank">Hatton National Bank PLC (HNB)</option>
                            <option value="sampath_bank">Sampath Bank PLC</option>
                            <option value="seylan_bank">Seylan Bank PLC</option>
                            <option value="nations_trust_bank">Nations Trust Bank PLC (NTB)</option>
                            <option value="dfcc_bank">DFCC Bank PLC</option>
                            <option value="ndb_bank">National Development Bank PLC (NDB Bank)</option>
                            <option value="pan_asia_bank">Pan Asia Banking Corporation PLC</option>
                            <option value="union_bank">Union Bank of Colombo PLC</option>
                            <option value="cargills_bank">Cargills Bank PLC</option>
                            <option value="amana_bank">Amãna Bank PLC</option>
                            <option value="sdb_bank">Sanasa Development Bank PLC (SDB bank)</option>
                            <option value="hdfc_bank_sl">HDFC Bank of Sri Lanka</option>
                            <option value="regional_development_bank">Regional Development Bank (RDB)</option>
                            <option value="state_mortgage_investment_bank">
                              State Mortgage &amp; Investment Bank (SMIB)
                            </option>
                          </select>
                        </div>
                      </div>

                      <div class="col-lg-4 col-12">
                        <div class="form-group">
                          <label for="branch-name">Branch Name</label>
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
                          <label for="cheque-date">Date</label>
                          <input
                            type="date"
                            class="form-control"
                            id="cheque-date"
                            v-model="cheque_date"
                            required
                          />
                        </div>
                      </div>

                      <div class="col-lg-4 col-12">
                        <div class="form-group">
                          <label for="cheque-amount">Amount</label>
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
                          <label for="text-amount">Amount Text</label>
                          <input
                            type="text"
                            class="form-control"
                            id="text-amount"
                            placeholder="Text Amount"
                            v-model="cheque_amount_text"
                            required
                          />
                        </div>
                      </div>

                      <div class="col-lg-4 col-12">
                        <div class="form-group">
                          <label for="receipt-number">Receipt number</label>
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
                            id="own_cheque"
                            value="own_cheque"
                            v-model="cheque_type"
                          />
                          <label class="form-check-label mr-2" for="own_cheque">
                            Own cheque
                          </label>
                        </div>

                        <div class="form-check mt-4">
                          <input
                            class="form-check-input"
                            type="radio"
                            id="party_cheque"
                            value="Party Cheque"
                            v-model="cheque_type"
                          />
                          <label class="form-check-label" for="party_cheque">
                            Party Cheque
                          </label>
                        </div>
                      </div>
                    </div>

                    <!-- Cash -->
                    <div class="row" v-else>
                      <div class="col-lg-4 col-12">
                        <div class="form-group">
                          <label for="cash-amount">Amount</label>
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
                          <label for="cash-text-amount">Amount Text</label>
                          <input
                            type="text"
                            class="form-control"
                            id="cash-text-amount"
                            placeholder="Cash Text Amount"
                            v-model="cash_amount_text"
                            required
                          />
                        </div>
                      </div>

                      <div class="col-lg-4 col-12">
                        <div class="form-group">
                          <label for="cash-receipt-number">Receipt number</label>
                          <input
                            type="text"
                            class="form-control"
                            id="cash-receipt-number"
                            placeholder="Receipt number"
                            v-model="cash_receipt_number"
                            required
                          />
                        </div>
                      </div>
                    </div>

                    <!-- Comment -->
                    <div class="row">
                      <div class="col-lg-12 col-12">
                        <div class="form-group">
                          <label for="comment">Comment</label>
                          <textarea
                            class="form-control"
                            id="comment"
                            placeholder="Comment"
                            v-model="comment"
                          />
                        </div>
                      </div>
                    </div>

                    <!-- Submit -->
                    <div class="row">
                      <div class="col-12 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                          UPDATE
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
ul li:hover {
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
}
</style>
