<script setup>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { Chart, registerables } from "chart.js";
import { ref, onMounted, onBeforeUnmount } from "vue";

Chart.register(...registerables);

const props = defineProps({
    todayVisiting: Array,
    employeeTarget: Object,
    todayCollections: Number,
    weeklyVisits: Array,
    weeklyCollections: Array,
    confirmedOrders: Array,
    todayExpectedCollections: Array,
});
// ---- STATIC KPI DATA (FOR ONE EMPLOYEE / SALES REP) ----
const employeeName = "Inosh Perera";

const todayVisits = 7;
const completedVisits = 5;
const monthlyTarget = props.employeeTarget.target_value; // LKR
const monthlyAchieved = props.employeeTarget.achieved_value; // LKR
const targetProgress = Math.round((monthlyAchieved / monthlyTarget) * 100);

// ---- REAL-TIME CLOCK + SESSION TIMER ----
const now = ref(new Date());
const sessionSeconds = ref(0);
let timerId = null;

const sending = ref(false);

const sendReport = async () => {
    sending.value = true;
    try {
        await axios.post("/employee/report/send-daily"); // route below
        alert("Report request sent. Admin will receive the PDF soon.");
    } catch (e) {
        alert("Failed to send report.");
    } finally {
        sending.value = false;
    }
};

function getDistanceInMeters(lat1, lon1, lat2, lon2) {
    const R = 6371000;
    const toRad = (x) => (x * Math.PI) / 180;

    const dLat = toRad(lat2 - lat1);
    const dLon = toRad(lon2 - lon1);

    const a =
        Math.sin(dLat / 2) ** 2 +
        Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) * Math.sin(dLon / 2) ** 2;

    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

    return R * c;
}

function isLocationMatch(row) {
    if (!row.location || !row.dealer) return false;

    const checkin = JSON.parse(row.location); // check-in lat/lng
    const dealerLat = parseFloat(row.dealer.lat);
    const dealerLng = parseFloat(row.dealer.lng);

    const distance = this.getDistanceInMeters(
        dealerLat,
        dealerLng,
        checkin.lat,
        checkin.lng
    );

    return distance <= 20; // within 20 meters
}

onMounted(() => {
    timerId = setInterval(() => {
        now.value = new Date();
        sessionSeconds.value += 1;
    }, 1000); // update every second
});

onBeforeUnmount(() => {
    if (timerId) clearInterval(timerId);
});

const formattedDate = () =>
    now.value.toLocaleDateString("en-LK", {
        weekday: "long",
        year: "numeric",
        month: "short",
        day: "numeric",
    });

const formattedTime = () =>
    now.value.toLocaleTimeString("en-LK", {
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
    });

const formattedSessionTime = () => {
    const total = sessionSeconds.value;
    const h = String(Math.floor(total / 3600)).padStart(2, "0");
    const m = String(Math.floor((total % 3600) / 60)).padStart(2, "0");
    const s = String(total % 60).padStart(2, "0");
    return `${h}:${m}:${s}`;
};

// ---- NUMBER TO WORDS CONVERTER ----
const numberInput = ref("");
const numberWords = ref("");

function convertNumberToWords(num) {
    // supports 0 – 999,999
    const ones = [
        "Zero",
        "One",
        "Two",
        "Three",
        "Four",
        "Five",
        "Six",
        "Seven",
        "Eight",
        "Nine",
        "Ten",
        "Eleven",
        "Twelve",
        "Thirteen",
        "Fourteen",
        "Fifteen",
        "Sixteen",
        "Seventeen",
        "Eighteen",
        "Nineteen",
    ];
    const tens = [
        "",
        "",
        "Twenty",
        "Thirty",
        "Forty",
        "Fifty",
        "Sixty",
        "Seventy",
        "Eighty",
        "Ninety",
    ];

    if (num === 0) return "Zero";
    if (num < 0 || num > 999999) return "Out of range (0 – 999,999)";

    function toWords(n) {
        let result = "";
        if (n >= 100) {
            result += ones[Math.floor(n / 100)] + " hundred";
            n = n % 100;
            if (n > 0) result += " and ";
        }
        if (n >= 20) {
            result += tens[Math.floor(n / 10)];
            if (n % 10 > 0) result += " " + ones[n % 10];
        } else if (n > 0) {
            result += ones[n];
        }
        return result;
    }

    let words = "";
    if (num >= 1000) {
        const thousands = Math.floor(num / 1000);
        words += toWords(thousands) + " thousand";
        const rest = num % 1000;
        if (rest > 0) {
            words += rest < 100 ? " and " : ", ";
            words += toWords(rest);
        }
    } else {
        words = toWords(num);
    }

    return words;
}

function handleNumberInput() {
    const raw = numberInput.value.trim();
    if (raw === "") {
        numberWords.value = "";
        return;
    }
    const parsed = parseInt(raw, 10);
    if (isNaN(parsed)) {
        numberWords.value = "Please enter a valid number.";
    } else {
        numberWords.value = convertNumberToWords(parsed);
    }
}

// ---- STATIC MESSAGES FROM ADMIN ----
const adminMessages = [
    {
        id: 1,
        title: "Route priority – Kurunegala town",
        snippet: "Please cover Ranjan Stores and Sahan Stores before 11.30 AM.",
        time: "Today • 08:15 AM",
        unread: true,
    },
    {
        id: 2,
        title: "Cheque handling reminder",
        snippet: "Double-check cheque details before submitting collection.",
        time: "Yesterday • 05:40 PM",
        unread: false,
    },
];

// ---- CHART REFS ----
const weeklyChartRef = ref(null);
const targetChartRef = ref(null);

// ---- CHARTS ----
onMounted(() => {
    setTimeout(() => {
        // WEEKLY VISITS & COLLECTIONS (BAR + LINE)
        const weeklyCtx = weeklyChartRef.value.getContext("2d");
        const gradientBar = weeklyCtx.createLinearGradient(0, 0, 0, 300);
        gradientBar.addColorStop(0, "#4f46e5");
        gradientBar.addColorStop(1, "#6366f1");

        const gradientLine = weeklyCtx.createLinearGradient(0, 0, 0, 300);
        gradientLine.addColorStop(0, "#22c55e");
        gradientLine.addColorStop(1, "#16a34a");

        new Chart(weeklyCtx, {
            type: "bar",
            data: {
                labels: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
                datasets: [
                    {
                        type: "bar",
                        label: "Visits",
                        data: props.weeklyVisits,
                        backgroundColor: gradientBar,
                        borderWidth: 0,
                        yAxisID: "y",
                    },
                    {
                        type: "line",
                        label: "Collection (LKR '000)",
                        data: props.weeklyCollections,
                        borderColor: gradientLine,
                        backgroundColor: "transparent",
                        tension: 0.35,
                        borderWidth: 2.5,
                        yAxisID: "y1",
                    },
                ],
            },
            options: {
                responsive: true,
                animation: {
                    duration: 900,
                    easing: "easeOutQuad",
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { display: false },
                        ticks: { color: "#6b7280" },
                    },
                    y1: {
                        position: "right",
                        beginAtZero: true,
                        grid: { display: false },
                        ticks: { color: "#6b7280" },
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: "#6b7280" },
                    },
                },
                plugins: {
                    legend: {
                        labels: {
                            color: "#4b5563",
                            boxWidth: 14,
                            font: { size: 11 },
                        },
                    },
                    title: {
                        display: false,
                    },
                },
            },
        });

        // MONTHLY TARGET VS ACHIEVEMENT (DOUGHNUT)
        const targetCtx = targetChartRef.value.getContext("2d");
        const remaining = Math.max(monthlyTarget - monthlyAchieved, 0);

        new Chart(targetCtx, {
            type: "doughnut",
            data: {
                labels: ["Achieved", "Remaining"],
                datasets: [
                    {
                        data: [monthlyAchieved, remaining],
                        backgroundColor: ["#22c55e", "#e5e7eb"],
                        borderWidth: 0,
                        cutout: "70%",
                    },
                ],
            },
            options: {
                responsive: true,
                animation: {
                    duration: 900,
                    easing: "easeOutQuad",
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function (ctx) {
                                const value = ctx.raw;
                                return `${
                                    ctx.label
                                }: LKR ${value.toLocaleString("en-LK")}`;
                            },
                        },
                    },
                },
            },
        });
    }, 400);
});
</script>

<template>
    <AdminLayout>
        <Head title="Employee Dashboard" />

        <div class="content-header employee-header">
            <div class="container-fluid">
                <div class="row align-items-center mb-2">
                    <div class="col-md-7">
                        <h1 class="m-0 font-weight-bold">
                            Welcome back, {{ employeeName }}
                        </h1>
                        <p class="text-muted mb-0 small">
                            Track today’s visits, collections, and monthly
                            target in one place.
                        </p>
                    </div>
                    <div class="col-md-5 mt-3 mt-md-0">
                        <div
                            class="d-flex justify-content-md-end justify-content-start"
                        >
                            <!-- Live clock -->
                            <div
                                class="time-card d-inline-flex align-items-center px-3 py-2 mr-2 mb-2 mb-md-0"
                            >
                                <div class="time-icon mr-2">
                                    <i class="far fa-clock"></i>
                                </div>
                                <div class="text-left">
                                    <div class="time-label">Current Time</div>
                                    <div class="time-date">
                                        {{ formattedDate() }}
                                    </div>
                                    <div class="time-time">
                                        {{ formattedTime() }}
                                    </div>
                                </div>
                            </div>
                            <!-- Session timer -->
                            <div
                                class="time-card d-inline-flex align-items-center px-3 py-2"
                            >
                                <div class="time-icon mr-2">
                                    <i class="fas fa-hourglass-half"></i>
                                </div>
                                <div class="text-left">
                                    <div class="time-label">Session Timer</div>
                                    <div class="time-date">
                                        Since page opened
                                    </div>
                                    <div class="time-time">
                                        {{ formattedSessionTime() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Number to Words Row -->
                <div class="row mt-2">
                    <div class="col-lg-6">
                        <div class="card card-default number-converter-card">
                            <div
                                class="card-header border-0 d-flex justify-content-between align-items-center"
                            >
                                <h3 class="card-title mb-0">
                                    <i class="fas fa-font mr-1"></i> Number to
                                    Text
                                </h3>
                                <span class="text-muted small"
                                    >Type a number, see it in words</span
                                >
                            </div>
                            <div class="card-body">
                                <div class="form-group mb-2">
                                    <label class="small text-muted mb-1"
                                        >Enter a number (0 – 999,999)</label
                                    >
                                    <input
                                        type="text"
                                        class="form-control form-control-sm"
                                        placeholder="Example: 1250"
                                        v-model="numberInput"
                                        @input="handleNumberInput"
                                    />
                                </div>
                                <div
                                    v-if="numberWords"
                                    class="converted-text mt-2"
                                >
                                    <div class="small text-muted mb-1">
                                        In words:
                                    </div>
                                    <div class="converted-pill">
                                        {{ numberWords }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <!-- KPI Row -->
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="kpi-card kpi-visits">
                            <div class="kpi-body">
                                <div class="kpi-title">Today’s Visits</div>
                                <div class="kpi-value">
                                    {{ todayVisiting.length }}
                                </div>
                                <div class="kpi-sub">
                                    Total shops in today route
                                </div>
                            </div>
                            <div class="kpi-icon">
                                <i class="fas fa-route"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="kpi-card kpi-completed">
                            <div class="kpi-body">
                                <div class="kpi-title">Completed</div>
                                <div class="kpi-value">
                                    {{ completedVisits }}
                                </div>
                                <div class="kpi-sub">
                                    {{ completedVisits }}/{{ todayVisits }}
                                    completed
                                </div>
                            </div>
                            <div class="kpi-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6 mt-3 mt-lg-0">
                        <div class="kpi-card kpi-collection">
                            <div class="kpi-body">
                                <div class="kpi-title">Today’s Collection</div>
                                <div class="kpi-value">
                                    LKR
                                    {{
                                        todayCollections.toLocaleString("en-LK")
                                    }}
                                </div>
                                <div class="kpi-sub">
                                    Cash + Cheque received
                                </div>
                            </div>
                            <div class="kpi-icon">
                                <i class="fas fa-coins"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6 mt-3 mt-lg-0">
                        <div class="kpi-card kpi-target">
                            <div class="kpi-body">
                                <div class="kpi-title">Monthly Target</div>
                                <div class="kpi-value">
                                    {{
                                        (
                                            (employeeTarget.achieved_value /
                                                employeeTarget.target_value) *
                                            100
                                        ).toFixed(2)
                                    }}%
                                </div>
                                <div class="kpi-sub">
                                    LKR
                                    {{
                                        employeeTarget.achieved_value.toLocaleString(
                                            "en-LK"
                                        )
                                    }}
                                    /
                                    {{
                                        employeeTarget.target_value.toLocaleString(
                                            "en-LK"
                                        )
                                    }}
                                </div>
                            </div>
                            <div class="kpi-icon">
                                <i class="fas fa-bullseye"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions + Messages -->
                <div class="row mt-3">
                    <div class="col-lg-8">
                        <div class="card card-default quick-actions-card">
                            <div
                                class="card-header border-0 d-flex justify-content-between align-items-center"
                            >
                                <h3 class="card-title mb-0">
                                    <i class="fas fa-bolt mr-1"></i> Quick
                                    Actions
                                </h3>
                                <span class="text-muted small"
                                    >Most used actions for your day</span
                                >
                            </div>
                            <div class="card-body pt-2 pb-3">
                                <div class="row">
                                    <div
                                        class="col-6 col-md-4 mb-2"
                                        v-for="action in [
                                            {
                                                icon: 'fas fa-play-circle',
                                                label: 'Start Visit',
                                                link: 'submit.index',
                                            },
                                            {
                                                icon: 'fas fa-shopping-basket',
                                                label: 'Create Order',
                                                link: 'order.index',
                                            },
                                            {
                                                icon: 'fas fa-money-bill-wave',
                                                label: 'Cash Collection',
                                                link: 'my.collection.index',
                                            },
                                            {
                                                icon: 'fas fa-boxes',
                                                label: 'Update Stock',
                                                link: 'stock.odit.create',
                                            },
                                        ]"
                                        :key="action.label"
                                    >
                                        <a
                                            :href="route(action.link)"
                                            style="text-decoration: none"
                                        >
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-quick-action btn-block"
                                            >
                                                <i
                                                    :class="[
                                                        'mr-2',
                                                        action.icon,
                                                    ]"
                                                ></i>
                                                {{ action.label }}
                                            </button>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Messages from Admin -->
                    <div class="col-lg-4">
                        <div class="card card-default messages-card">
                            <div
                                class="card-header border-0 d-flex justify-content-between align-items-center"
                            >
                                <h3 class="card-title mb-0">
                                    <i class="far fa-bell mr-1"></i> Messages
                                    from Admin
                                </h3>
                                <span
                                    class="badge badge-pill badge-primary small"
                                >
                                    {{
                                        adminMessages.filter((m) => m.unread)
                                            .length
                                    }}
                                    new
                                </span>
                            </div>
                            <div class="card-body p-0">
                                <ul class="list-group list-group-flush">
                                    <li
                                        v-for="msg in adminMessages"
                                        :key="msg.id"
                                        class="list-group-item message-item"
                                        :class="{
                                            'message-unread': msg.unread,
                                        }"
                                    >
                                        <div
                                            class="d-flex justify-content-between align-items-center"
                                        >
                                            <div class="message-title">
                                                {{ msg.title }}
                                            </div>
                                            <span
                                                v-if="msg.unread"
                                                class="badge badge-success badge-dot"
                                            >
                                                New
                                            </span>
                                        </div>
                                        <div class="message-snippet">
                                            {{ msg.snippet }}
                                        </div>
                                        <div
                                            class="message-time text-muted small"
                                        >
                                            {{ msg.time }}
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="row mt-3">
                    <div class="col-lg-8">
                        <div class="card card-default chart-card">
                            <div
                                class="card-header border-0 d-flex justify-content-between align-items-center"
                            >
                                <h3 class="card-title mb-0">
                                    <i class="fas fa-chart-line mr-1"></i>
                                    Weekly Visits & Collections
                                </h3>
                                <span class="text-muted small"
                                    >Static demo data</span
                                >
                            </div>
                            <div class="card-body">
                                <canvas
                                    ref="weeklyChartRef"
                                    class="chart-canvas"
                                ></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card card-default chart-card target-card">
                            <div
                                class="card-header border-0 d-flex justify-content-between align-items-center"
                            >
                                <h3 class="card-title mb-0">
                                    <i class="fas fa-bullseye mr-1"></i> Monthly
                                    Target
                                </h3>
                            </div>
                            <div
                                class="card-body d-flex flex-column align-items-center justify-content-center"
                            >
                                <div class="position-relative mb-3">
                                    <canvas
                                        ref="targetChartRef"
                                        class="target-chart-canvas"
                                    ></canvas>
                                    <div class="target-center">
                                        <div class="target-percent">
                                            {{ targetProgress }}%
                                        </div>
                                        <div class="target-label">Achieved</div>
                                    </div>
                                </div>
                                <div class="target-legend text-center small">
                                    <div>
                                        LKR
                                        {{
                                            monthlyAchieved.toLocaleString(
                                                "en-LK"
                                            )
                                        }}
                                        collected
                                    </div>
                                    <div class="text-muted">
                                        Balance
                                        {{
                                            (
                                                monthlyTarget - monthlyAchieved
                                            ).toLocaleString("en-LK")
                                        }}
                                        to reach target
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Today Route Summary -->
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card card-default">
                            <div
                                class="card-header border-0 d-flex justify-content-between align-items-center"
                            >
                                <h3 class="card-title mb-0">
                                    <i class="fas fa-clipboard-list mr-1"></i>
                                    Today Route Summary
                                </h3>
                                <span class="text-muted small">
                                    Your shop visits and collections for today.
                                </span>
                                <button
                                    class="btn btn-primary"
                                    @click="sendReport"
                                    :disabled="sending"
                                >
                                    <i class="fas fa-paper-plane mr-1"></i>
                                    {{
                                        sending
                                            ? "Sending..."
                                            : "Send Daily Report to Admin"
                                    }}
                                </button>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th style="width: 60px">#</th>
                                                <th>Dealer</th>
                                                <th>In Time</th>
                                                <th>In Date</th>
                                                <th>Out Time</th>
                                                <th>Out Date</th>
                                                <th>Location Status</th>
                                                <th>Shop Img</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr
                                                v-for="(
                                                    row, index
                                                ) in todayVisiting"
                                                :key="row.id"
                                            >
                                                <td>{{ index + 1 }}</td>
                                                <td>
                                                    {{
                                                        row.dealer.business_name
                                                    }}
                                                    -
                                                    {{
                                                        row.dealer
                                                            .business_address
                                                    }}
                                                </td>
                                                <td>{{ row.time }}</td>
                                                <td>{{ row.date }}</td>
                                                <td>
                                                    <span
                                                        v-if="row.checkout_time"
                                                    >
                                                        {{ row.checkout_time }}
                                                    </span>

                                                    <span
                                                        v-else
                                                        class="badge badge-danger"
                                                    >
                                                        Not Yet
                                                    </span>
                                                </td>
                                                <td>
                                                    <span
                                                        v-if="row.checkout_date"
                                                    >
                                                        {{ row.checkout_date }}
                                                    </span>

                                                    <span
                                                        v-else
                                                        class="badge badge-danger"
                                                    >
                                                        Not Yet
                                                    </span>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge"
                                                        :class="
                                                            isLocationMatch(row)
                                                                ? 'badge-success'
                                                                : 'badge-danger'
                                                        "
                                                    >
                                                        {{
                                                            isLocationMatch(row)
                                                                ? "Location Match"
                                                                : "Location Mismatch"
                                                        }}
                                                    </span>
                                                </td>

                                                <!-- Your Status Column -->
                                                <td>
                                                    <a
                                                        :href="
                                                            '/storage/' +
                                                            row.dealer
                                                                .photo_of_the_shop
                                                        "
                                                        target="_blank"
                                                        rel="noopener"
                                                        @click.stop
                                                    >
                                                        View Image
                                                    </a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Expected Orders & Collections -->
                <div class="row mt-3">
                    <div class="col-lg-6">
                        <div class="card card-default">
                            <div
                                class="card-header border-0 d-flex justify-content-between align-items-center"
                            >
                                <h3 class="card-title mb-0">
                                    <i class="fas fa-box-open mr-1"></i> Today’s
                                    Expected Orders
                                </h3>
                                <span class="text-muted small"
                                    >From your dealers</span
                                >
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table mb-0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Dealer</th>
                                                <th>Telephone</th>
                                                <th>Expected</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr
                                                v-for="(
                                                    ord, index
                                                ) in confirmedOrders"
                                                :key="ord.id"
                                            >
                                                <td>{{ index + 1 }}</td>
                                                <td>
                                                    {{ ord.shop.business_name }}
                                                    -
                                                    {{
                                                        ord.shop
                                                            .business_address
                                                    }}
                                                </td>
                                                <td>{{ ord.order_number }}</td>
                                                <td>
                                                    {{
                                                        ord.expected_order_date
                                                    }}
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge badge-pill"
                                                        :class="{
                                                            'badge-success p-2':
                                                                ord.order_status ===
                                                                'Confirmed',
                                                            'badge-info':
                                                                ord.status ===
                                                                'Follow-up',
                                                        }"
                                                    >
                                                        {{ ord.order_status }}
                                                    </span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 mt-3 mt-lg-0">
                        <div class="card card-default">
                            <div
                                class="card-header border-0 d-flex justify-content-between align-items-center"
                            >
                                <h3 class="card-title mb-0">
                                    <i class="fas fa-hand-holding-usd mr-1"></i>
                                    Today’s Expected Collections
                                </h3>
                                <span class="text-muted small"
                                    >Planned follow-ups</span
                                >
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table mb-0 table-hover">
                                        <thead class="thead-light">
                                            <tr>
                                                <th style="width: 50px">#</th>
                                                <th>Dealer</th>
                                                <th>Invoice</th>
                                                <th>Expected</th>
                                                <th class="text-right">
                                                    Total (LKR)
                                                </th>
                                                <th class="text-right">
                                                    Paid (LKR)
                                                </th>
                                                <th class="text-right">
                                                    Balance (LKR)
                                                </th>
                                                <th style="width: 110px">
                                                    Status
                                                </th>
                                            </tr>
                                        </thead>

                                        <tbody
                                            v-if="
                                                props.todayExpectedCollections &&
                                                props.todayExpectedCollections
                                                    .length
                                            "
                                        >
                                            <tr
                                                v-for="(
                                                    col, index
                                                ) in props.todayExpectedCollections"
                                                :key="col.id"
                                            >
                                                <td>{{ index + 1 }}</td>

                                                <td>
                                                    <div
                                                        class="font-weight-semibold"
                                                    >
                                                        {{ col.dealer_name }}
                                                    </div>
                                                    <div
                                                        class="text-muted small"
                                                    >
                                                        {{ col.telephone }}
                                                    </div>
                                                </td>

                                                <td>
                                                    {{
                                                        col.order_number ?? "-"
                                                    }}
                                                </td>

                                                <td>
                                                    {{
                                                        col.expected_date ?? "-"
                                                    }}
                                                </td>

                                                <td class="text-right">
                                                    {{
                                                        Number(
                                                            col.total_price ?? 0
                                                        ).toLocaleString(
                                                            "en-LK"
                                                        )
                                                    }}
                                                </td>

                                                <td class="text-right">
                                                    {{
                                                        Number(
                                                            col.paid_amount ?? 0
                                                        ).toLocaleString(
                                                            "en-LK"
                                                        )
                                                    }}
                                                </td>

                                                <td
                                                    class="text-right font-weight-bold"
                                                >
                                                    {{
                                                        Number(
                                                            col.balance ?? 0
                                                        ).toLocaleString(
                                                            "en-LK"
                                                        )
                                                    }}
                                                </td>

                                                <td>
                                                    <span
                                                        class="badge badge-pill p-2 badge-warning"
                                                    >
                                                        {{
                                                            col.status ??
                                                            "Pending"
                                                        }}
                                                    </span>
                                                </td>
                                            </tr>
                                        </tbody>

                                        <tbody v-else>
                                            <tr>
                                                <td
                                                    colspan="8"
                                                    class="text-center py-4 text-muted"
                                                >
                                                    No pending collections for
                                                    today.
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
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
.employee-header {
    background: linear-gradient(120deg, #0f172a, #1e293b);
    color: #fff;
    border-radius: 0 0 1.6rem 1.6rem;
    padding-bottom: 1.5rem;
    margin-bottom: 1rem;
}

.employee-header h1 {
    font-size: 1.6rem;
}

/* Time card */
.time-card {
    border-radius: 999px;
    background: rgba(15, 23, 42, 0.9);
    color: #e5e7eb;
    box-shadow: 0 10px 25px rgba(15, 23, 42, 0.45);
}

.time-icon {
    width: 32px;
    height: 32px;
    border-radius: 999px;
    background: rgba(30, 64, 175, 0.9);
    display: flex;
    align-items: center;
    justify-content: center;
}

.time-label {
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #9ca3af;
}

.time-date {
    font-size: 0.85rem;
    font-weight: 600;
}

.time-time {
    font-size: 0.85rem;
}

/* Number converter */
.number-converter-card {
    border-radius: 1rem;
    box-shadow: 0 10px 20px rgba(15, 23, 42, 0.08);
    margin-top: 0.8rem;
}

.converted-text .converted-pill {
    display: inline-block;
    border-radius: 999px;
    background: #eef2ff;
    color: #1e293b;
    padding: 0.35rem 0.9rem;
    font-size: 0.85rem;
}

/* KPI cards */
.kpi-card {
    border-radius: 1.1rem;
    padding: 0.85rem 0.9rem;
    position: relative;
    overflow: hidden;
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: #fff;
    box-shadow: 0 10px 22px rgba(15, 23, 42, 0.25);
}

.kpi-card::before {
    content: "";
    position: absolute;
    width: 110px;
    height: 110px;
    border-radius: 100%;
    background: rgba(255, 255, 255, 0.16);
    right: -30px;
    top: -40px;
}

.kpi-body {
    position: relative;
    z-index: 1;
}

.kpi-title {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    opacity: 0.9;
}

.kpi-value {
    font-size: 1.25rem;
    font-weight: 800;
}

.kpi-sub {
    font-size: 0.75rem;
    opacity: 0.9;
}

.kpi-icon {
    position: relative;
    z-index: 1;
    font-size: 1.7rem;
    opacity: 0.9;
}

/* KPI gradients */
.kpi-visits {
    background: linear-gradient(135deg, #22c55e, #16a34a);
}
.kpi-completed {
    background: linear-gradient(135deg, #0ea5e9, #2563eb);
}
.kpi-collection {
    background: linear-gradient(135deg, #f97316, #ea580c);
}
.kpi-target {
    background: linear-gradient(135deg, #6366f1, #7c3aed);
}

/* Quick actions */
.quick-actions-card {
    border-radius: 1rem;
    box-shadow: 0 10px 20px rgba(15, 23, 42, 0.08);
}

.btn-quick-action {
    border-radius: 999px;
    border: 0;
    background: #f3f4f6;
    color: #111827;
    font-size: 0.8rem;
    text-align: left;
    padding: 0.45rem 0.7rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    transition: all 0.15s ease-in-out;
}

.btn-quick-action i {
    width: 16px;
    text-align: center;
}

.btn-quick-action:hover {
    background: linear-gradient(135deg, #6366f1, #3b82f6);
    color: #f9fafb;
    transform: translateY(-1px);
    box-shadow: 0 8px 18px rgba(37, 99, 235, 0.35);
}

/* Messages card */
.messages-card {
    border-radius: 1rem;
    box-shadow: 0 10px 20px rgba(15, 23, 42, 0.08);
}

.message-item {
    font-size: 0.8rem;
}

.message-title {
    font-weight: 600;
}

.message-snippet {
    color: #4b5563;
    margin-top: 0.1rem;
}

.message-time {
    margin-top: 0.2rem;
}

.message-unread {
    background-color: #eff6ff;
}

/* Charts */
.chart-card {
    border-radius: 1rem;
    box-shadow: 0 10px 20px rgba(15, 23, 42, 0.08);
}

.chart-canvas {
    height: 320px !important;
}

/* Target chart */
.target-card {
    text-align: center;
}

.target-chart-canvas {
    width: 180px !important;
    height: 180px !important;
}

.target-center {
    position: absolute;
    inset: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    pointer-events: none;
}

.target-percent {
    font-size: 1.4rem;
    font-weight: 800;
}

.target-label {
    font-size: 0.8rem;
    color: #6b7280;
}

/* Tables */
.card-default {
    border-radius: 1rem;
}

.table-hover tbody tr:hover {
    background-color: #f9fafb;
}

/* Badges */
.badge-success {
    background-color: #22c55e;
}
.badge-warning {
    background-color: #f59e0b;
}
.badge-secondary {
    background-color: #9ca3af;
}
.badge-danger {
    background-color: #ef4444;
}
.badge-info {
    background-color: #0ea5e9;
}

/* Responsive */
@media (max-width: 767.98px) {
    .employee-header {
        border-radius: 0 0 1rem 1rem;
    }
}
</style>
