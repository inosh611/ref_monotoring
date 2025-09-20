<script setup>
import AdminLayout from '@/Layouts/Admin/AdminLayout.vue';
import DataTable from '@/Components/Admin/DataTable.vue';
import { Chart, registerables } from 'chart.js';
import { Head } from "@inertiajs/vue3";
import { onMounted, ref } from 'vue';

Chart.register(...registerables);

// Define product table columns
const product_table_columns = [
    { field: 'id', title: 'ID', isUnique: true },
    { field: 'reference_number', title: 'REFERENCE NUMBER' },
    { field: 'status', title: 'STATUS' },
    { field: 'actions', title: 'Actions', cellRenderer: false, width: '50px' },
];

// Reference for the canvas element
const pieChartRef = ref(null)
const barChartRef = ref(null)

// Mount Chart.js chart
onMounted(() => {
    // Delay chart creation by 1 second (1000ms)
  setTimeout(() => {
    // Pie Chart
    const pieCtx = pieChartRef.value.getContext('2d');
    const pieGradient = {
        red:{startColor : '#ADD100',endColor : '#7B920A'},
        blue:{startColor : '#8e2de2',endColor : '#4a00e0'},
        yellow:{startColor : '#f7971e',endColor : '#ffd200'},
    };

    const gradientPieRed = pieCtx.createLinearGradient(0, 0, 0, 400);
      gradientPieRed.addColorStop(0, pieGradient.red.startColor); // Color red
      gradientPieRed.addColorStop(1, pieGradient.red.endColor);
    const gradientPieBlue = pieCtx.createLinearGradient(0, 0, 0, 400);
      gradientPieBlue.addColorStop(0, pieGradient.blue.startColor); // Color blue
      gradientPieBlue.addColorStop(1, pieGradient.blue.endColor);
    const gradientPieYellow = pieCtx.createLinearGradient(0, 0, 0, 400);
      gradientPieYellow.addColorStop(0, pieGradient.yellow.startColor); // Color yellow
      gradientPieYellow.addColorStop(1, pieGradient.yellow.endColor);
    new Chart(pieCtx, {
      type: 'pie',
      data: {
        labels: ['Red', 'Blue', 'Yellow'],
        datasets: [{
          label: 'Pie Chart Dataset',
          data: [30, 45, 25],
          backgroundColor: [
              gradientPieRed,
              gradientPieBlue,
            gradientPieYellow,
          ],
          borderColor: [
              gradientPieRed,
              gradientPieBlue,
            gradientPieYellow,
          ],
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        animation: {
          duration: 1000,
          easing: 'easeOutBounce'
        },
        plugins: {
          legend: { position: 'top' },
          title: { display: true, text: 'Pie Chart Example' }
        }
      }
    })

    // Bar Chart
    const barCtx = barChartRef.value.getContext('2d');
    const colorGradient = {startColor : '#E8175E',endColor : '#E8175E'};
      const gradientGreen = barCtx.createLinearGradient(0, 0, 0, 400);
      gradientGreen.addColorStop(0, colorGradient.startColor); // Color at the top (0%)
      gradientGreen.addColorStop(1, colorGradient.endColor);
    new Chart(barCtx, {
      type: 'bar',
      data: {
        labels: ['January', 'February', 'March', 'April', 'May'],
        datasets: [{
          label: 'Bar Chart Dataset',
          data: [10, 20, 15, 25, 30],
          backgroundColor: "#E8175E",
        //   borderColor: 'rgba(75, 192, 192, 1)',
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        animation: {
          duration: 1000,
          easing: 'easeOutQuart'
        },
        scales: {
          y: { beginAtZero: true }
        },
        plugins: {
          legend: { display: true },
          title: { display: true, text: 'Bar Chart Example' }
        }
      }
    })
  }, 500)  // 1000 milliseconds = 1 second delay
});
</script>

<template>
    <AdminLayout>
        <Head title="Dashboard" />

        <!-- Dashboard Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">DASHBOARD</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dashboard Content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info order-color">
                            <div class="inner">
                                <h3>150</h3>
                                <p>New Orders</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>
                            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success rate-color">
                            <div class="inner">
                                <h3>100<sup style="font-size: 20px"></sup></h3>
                                <p>Completed task</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-stats-bars"></i>
                            </div>
                            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning register-color">
                            <div class="inner">
                                <h3>44</h3>
                                <p>Employees</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-person-add"></i>
                            </div>
                            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-danger visitor-color">
                            <div class="inner">
                                <h3>65</h3>
                                <p>Pending Task</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-pie-graph"></i>
                            </div>
                            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                </div>

                <!-- Chart Row -->
                <div class="row mt-4">
                    <div class="col-6">
                        <div class="card card-default">
                            <div class="card-body">
                                <canvas ref="barChartRef"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card card-default">
                            <div class="card-body d-flex justify-content-center">
                                 <canvas ref="pieChartRef"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- DataTable Row -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card card-default">
                            <div class="card-body" style="padding: 0px;">
                                <DataTable
                                    title="SAMPLES"
                                    fetch_url="/api/samples"
                                    :columns="product_table_columns"
                                    table_icon='<i class="nav-icon fas fa-archive" style="font-size: medium;"></i>'
                                    edit_route_name = 'product.edit'
                                    modal_title="Product"
                                    />

                                />
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
/* .order-color{
    background: linear-gradient(to right, #8e2de2, #4a00e0) !important;
}
.visitor-color{
    background: linear-gradient(45deg, #C04848, #480048) !important;
}
.register-color{
    background: linear-gradient(to right, #f7971e, #ffd200) !important;
}
.rate-color{
    background: linear-gradient(45deg, #00b09b, #96c93d) !important;
} */
</style>
