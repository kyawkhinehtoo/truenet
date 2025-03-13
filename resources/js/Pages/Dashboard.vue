<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-white leading-tight">Customer Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!--All Card-->
                <div class="grid gap-6 mb-8 md:grid-cols-2 xl:grid-cols-6">
                    <!-- Card -->
                    <div class="flex items-center p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800">
                        <div
                            class="px-3 py-2 text-orange-500 bg-orange-100 rounded-full dark:text-orange-100 dark:bg-orange-500">
                            <i class="fa fa-globe "></i>
                        </div>
                        <div class="ml-2">
                            <p class="mb-2 text-sm font-bold text-gray-600 dark:text-gray-400">Total Clients</p>
                            <p class="text-lg font-semibold text-gray-700 dark:text-gray-200">{{ all_customers }}</p>
                        </div>
                    </div>
                    <!-- End Card -->
                    <!-- Card -->
                    <div class="flex items-center p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800">
                        <div
                            class="p-3 mr-4 text-green-500 bg-green-100 rounded-full dark:text-green-100 dark:bg-green-500">

                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <p class="mb-2 text-sm font-bold text-gray-600 dark:text-gray-400">Net Active</p>
                            <p class="text-lg font-semibold text-gray-700 dark:text-gray-200">{{ total }}</p>
                        </div>
                    </div>
                    <!-- End Card -->
                    <div class="flex items-center p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800">
                        <div
                            class="px-3 py-2 mr-4 text-green-500 bg-green-100 rounded-full dark:text-green-100 dark:bg-green-500">
                            <i class="fa fa-chart-line "></i>
                        </div>
                        <div>
                            <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">New Order</p>
                            <p class="text-lg font-semibold text-gray-700 dark:text-gray-200">{{ to_install }}</p>
                        </div>
                    </div>

                    <!-- Card -->
                    <div class="flex items-center p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800">
                        <div
                            class="p-3 mr-4 text-yellow-500 bg-yellow-100 rounded-full dark:text-yellow-100 dark:bg-yellow-500">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zM7 8H5v2h2V8zm2 0h2v2H9V8zm6 0h-2v2h2V8z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">Suspended</p>
                            <p class="text-lg font-semibold text-gray-700 dark:text-gray-200">{{ suspense }}</p>
                        </div>
                    </div>
                    <!-- Card -->
                    <div class="flex items-center p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800">
                        <div
                            class="px-3 py-2 mr-4 text-red-500 bg-red-100 rounded-full dark:text-blue-100 dark:bg-blue-500">
                            <i class="fa fa-stop "></i>
                        </div>
                        <div>
                            <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">Terminated</p>
                            <p class="text-lg font-semibold text-gray-700 dark:text-gray-200">{{ terminate }}</p>
                        </div>
                    </div>
                    <!-- Card -->
                    <div class="flex items-center p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800">
                        <div
                            class="px-3 py-2 mr-4 text-blue-500 bg-blue-100 rounded-full dark:text-blue-100 dark:bg-blue-500">
                            <i class="fa fa-check-double "></i>
                        </div>
                        <div>
                            <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">Installed this Week</p>
                            <p class="text-lg font-semibold text-gray-700 dark:text-gray-200">{{ install_week }}</p>
                        </div>
                    </div>
                </div>

                <!--End All Card-->
                <div class="w-full bg-white rounded-lg shadow dark:bg-gray-800">
                    <h4 class="mb-4 p-4 font-semibold text-gray-800 dark:text-gray-300">FTTH Customers</h4>
                    <vue3-chart-js v-bind="{ ...barChartFTTH }" />
                </div>
                <div class="grid gap-6 mb-8 md:grid-cols-2 mt-2">
                    <div class="min-w-0 p-4 bg-white rounded-lg shadow dark:bg-gray-800">
                        <h4 class="mb-4 font-semibold text-gray-800 dark:text-gray-300">Service Ratio</h4>
                        <div style="display: flex;flex-direction:column;">
                            <vue3-chart-js :id="doughnutChart.id" ref="chartRef" :type="doughnutChart.type"
                                :data="doughnutChart.data"></vue3-chart-js>
                            <div class="flex justify-center mt-4 space-x-3 text-sm text-gray-600 dark:text-gray-400">
                                <!-- Chart legend -->
                                <div class="flex items-center">
                                    <span class="inline-block w-3 h-3 mr-1 rounded-full"
                                        style="background:#00D8FF"></span>
                                    <span>{{ ftth }}</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="inline-block w-3 h-3 mr-1 rounded-full"
                                        style="background:#41B883"></span>
                                    <span>{{ b2b }}</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="inline-block w-3 h-3 mr-1 rounded-full"
                                        style="background:#E46651"></span>
                                    <span>{{ dia }}</span>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="min-w-0 p-4 bg-white rounded-lg shadow dark:bg-gray-800">
                        <div class="chartjs-size-monitor">
                            <div class="chartjs-size-monitor-expand">
                                <div class=""></div>
                            </div>
                            <div class="chartjs-size-monitor-shrink">
                                <div class=""></div>
                            </div>
                        </div>
                        <h4 class="mb-4 font-semibold text-gray-800 dark:text-gray-300">B2B and DIA Customers</h4>

                        <vue3-chart-js v-bind="{ ...barChartb2b }" />
                        <vue3-chart-js v-bind="{ ...barChartDIA }" />

                    </div>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import Vue3ChartJs from "@j-t-mcc/vue3-chartjs";
import { ref } from "vue";
export default {
    components: {
        AppLayout,
        Vue3ChartJs,
    },
    name: "Dashboard",
    props: {
        ftth: Object,
        b2b: Object,
        dia: Object,
        ftth_total: Object,
        b2b_total: Object,
        dia_total: Object,
        total: Object,
        all_customers: Object,
        to_install: Object,
        suspense: Object,
        terminate: Object,
        install_week: Object,
    },
    setup(props) {
        const chartRef = ref(null);
        let proj_name = ref(['FTTH', 'B2B', 'DIA']);
        let ftth_name = ref([]);
        let b2b_name = ref([]);
        let dia_name = ref([]);

        let ftth_number = ref([]);
        props.ftth_total.map(function (x) {
            ftth_number.value.push(x.customers)
        });

        let b2b_number = ref([]);
        props.b2b_total.map(function (x) {
            b2b_number.value.push(x.customers)
        });

        let dia_number = ref([]);
        props.dia_total.map(function (x) {
            dia_number.value.push(x.customers)
        });

        props.ftth_total.map(function (x) {
            ftth_name.value.push(x.name)
        });
        props.b2b_total.map(function (x) {
            b2b_name.value.push(x.name)
        });
        props.dia_total.map(function (x) {
            dia_name.value.push(x.name)
        });
        const doughnutChart = {
            id: "doughnut",
            type: "doughnut",
            data: {
                labels: proj_name.value,
                datasets: [
                    {
                        backgroundColor: ["#34495e", "#41B883", "#E46651"],
                        data: [props.ftth, props.b2b, props.dia],
                    },
                ],
            },
        };
        const barChartFTTH = {
            type: "bar",
            options: {
                min: 0,
                max: 100,
                responsive: true,
                plugins: {
                    legend: {
                        position: "top",
                    },
                },
                scales: {
                    y: {
                        min: 0,
                        max: Math.max(ftth_number) + 20,
                        ticks: {
                            callback: function (value) {
                                return `${value}`;
                            },
                        },
                    },
                },
            },
            data: {
                labels: ftth_name.value,
                datasets: [
                    {
                        label: "FTTH User Chart",
                        backgroundColor: "#34495e",
                        data: ftth_number.value,
                    },

                ],
            },
        };
        const barChartb2b = {
            type: "bar",
            options: {
                min: 0,
                max: 100,
                responsive: true,
                plugins: {
                    legend: {
                        position: "top",
                    },
                },
                scales: {
                    y: {
                        min: 0,
                        max: Math.max(b2b_number) + 20,
                        ticks: {
                            callback: function (value) {
                                return `${value}`;
                            },
                        },
                    },
                },
            },
            data: {
                labels: b2b_name.value,
                datasets: [
                    {
                        label: "B2B User Chart",
                        backgroundColor: "#1abc9c",
                        data: b2b_number.value,
                    },

                ],
            },
        };
        const barChartDIA = {
            type: "bar",
            options: {
                min: 0,
                max: 20,
                responsive: true,
                plugins: {
                    legend: {
                        position: "top",
                    },
                },
                scales: {
                    y: {
                        min: 0,
                        max: Math.max(dia_number) + 20,
                        ticks: {
                            callback: function (value) {
                                return `${value}`;
                            },
                        },
                    },
                },
            },
            data: {
                labels: dia_name.value,
                datasets: [
                    {
                        label: "DIA User Chart",
                        backgroundColor: ["#E46651"],
                        data: dia_number.value,
                    },

                ],
            },
        };
        const updateChart = () => {
            doughnutChart.data.labels = ["Cats", "Dogs", "Hamsters", "Dragons"];
            doughnutChart.data.datasets = [
                {
                    backgroundColor: ["#333333", "#E46651", "#00D8FF", "#DD1B16"],
                    data: [100, 20, 800, 20],
                },
            ];

            chartRef.value.update();
        };
        const getMax = (data) => {

        }
        return {
            doughnutChart,
            chartRef,
            proj_name,
            barChartFTTH,
            barChartb2b,
            barChartDIA
        };
    },
};
</script>