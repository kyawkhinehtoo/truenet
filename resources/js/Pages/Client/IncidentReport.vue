<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">Ticket Report</h2>
    </template>

    <div class="py-2">
      <!-- Advance Search -->
      <div class="max-w-full mx-auto sm:px-6 lg:px-8 mt-4">
        <div class="bg-white shadow sm:rounded-t-lg flex justify-between space-x-2 items-end py-2 px-2 md:px-2">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-6 w-full">
            <div class="py-2 col-span-1 sm:col-span-1">
              <label for="sh_general" class="block text-sm font-medium text-gray-700">General </label>
              <div class="mt-1 flex rounded-md shadow-sm">
                <span
                  class="z-10 leading-snug font-normal text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                  <i class="fas fa-user"></i>
                </span>
                <input type="text" v-model="form.general" name="sh_general" id="sh_general"
                  class="pl-10 py-2.5 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                  placeholder="Ticket ID/Customer ID/Customer Name etc." tabindex="1" />
              </div>
            </div>
            <div class="py-2 col-span-1 sm:col-span-1">
              <label for="sh_type" class="block text-sm font-medium text-gray-700">Ticket Type </label>
              <div class="mt-1 flex rounded-md shadow-sm">
                <select id="sh_type" v-model="form.type" name="sh_type"
                  class="py-2.5 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  tabindex="2">
                  <option value="0" selected>All</option>
                  <option value="service_complaint">Service Complaint</option>
                  <option value="onsite_complaint">Onsite Complaint</option>
                  <option value="technical_complaint">Technical Complaint</option>
                  <option value="plan_change">Plan Change</option>
                  <option value="suspension">Suspension</option>
                  <option value="termination">Termination</option>
                </select>
              </div>
            </div>
            <div class="py-2 col-span-1 sm:col-span-1">
              <label for="sh_period" class="block text-sm font-medium text-gray-700">Period </label>
              <div class="mt-1 flex rounded-md shadow-sm">
             <VueDatePicker v-model="form.period" :range="{ partialRange: true }" placeholder="Please choose Ticket Range" 
                  :enable-time-picker="false" model-type="yyyy-MM-dd" id="order_date"
                  class="text-gray-400 text-sm focus:ring focus:ring-indigo-500 focus:border-indigo-500 focus:ring-opacity-10 focus:outline-none" />
              </div>
            </div>
            <div class="py-2 col-span-1 sm:col-span-1">
              <label for="sh_type" class="block text-sm font-medium text-gray-700">Ticket Status </label>
              <div class="mt-1 flex rounded-md shadow-sm">
                <select id="sh_type" v-model="form.status" name="sh_status"
                  class="py-2.5 block w-full  px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  tabindex="4">

                  <option value="0">All Status</option>
                  <option value="1">Open</option>
                  <option value="2">Escalated</option>
                  <option value="5">Resolved</option>
                  <option value="3">Closed</option>
                  <option value="4">Deleted</option>
                </select>
              </div>
            </div>
          </div>
        </div>
        <div class="mb-2 py-2 px-2 md:px-2 bg-white shadow rounded-b-lg flex justify-between">
          <div class="flex">
            <a @click="submit"
              class="cursor-pointer inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">Search
              <i class="ml-1 fa fa-search text-white" tabindex="10"></i></a>
            <a @click="clearSearch"
              class="ml-2 cursor-pointer inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring focus:ring-gray-200 disabled:opacity-25 transition">Reset
              <i class="ml-1 fa fa-undo-alt text-white" tabindex="11"></i></a>
          </div>

          <div class="flex">
            <a @click="doExcel"
              class="cursor-pointer inline-flex items-center px-4 py-2 bg-green-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition">Export
              Excel <i class="ml-1 fa fa-download text-white" tabindex="11"></i></a>
          </div>
        </div>
      </div>
      <!-- End of Advance Search -->
      <div v-if="incidents" class="max-w-full mx-auto sm:px-6 lg:px-8 mt-4">
        <div class="bg-white overflow-visible shadow-xl sm:rounded-lg" v-if="incidents.data">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col"
                  class="pl-3 px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  No.</th>
                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Ticket ID</th>
                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Customer ID</th>
                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Ticket Type</th>
                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Ticket Start Date</th>
                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Ticket Close Date</th>
                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Status</th>

              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 relative">
              <tr v-for="(row, index) in incidents.data" v-bind:key="row.id">
                <td class="pl-4 px-2 py-3 text-xs whitespace-nowrap">{{ (index += incidents.from) }}</td>
                <td class="px-2 py-3 text-xs whitespace-nowrap">{{ row.code }}</td>
                <td class="px-2 py-3 text-xs whitespace-nowrap">{{ row.ftth_id }}</td>
                <td class="px-2 py-3 text-xs whitespace-nowrap capitalize">{{ row.type.replace(/_/g, " ") }}</td>
                <td class="px-2 py-3 text-xs whitespace-nowrap">{{ row.date }} {{ row.time }}</td>
                <td class="px-2 py-3 text-xs whitespace-nowrap">{{ row.close_date }} {{ row.close_time }}</td>
                <td class="px-2 py-3 text-xs whitespace-nowrap">{{ getStatus(row.status) }}</td>

              </tr>
            </tbody>
          </table>
        </div>
        <span v-if="incidents.total" class="w-full block mt-4">
          <label class="text-xs text-gray-600">{{ incidents.data.length }} Invoices in Current Page. Total Number of
            Invoices : {{ incidents.total }}</label>
        </span>
        <span v-if="incidents.links">
          <pagination class="mt-6" :links="incidents.links" />
        </span>
        <div v-if="loading" wire:loading
          class="fixed top-0 left-0 right-0 bottom-0 w-full h-screen z-50 overflow-hidden bg-gray-700 opacity-75 flex flex-col items-center justify-center">
          <div class="loader ease-linear rounded-full border-4 border-t-4 border-gray-200 h-12 w-12 mb-4"></div>
          <h2 class="text-center text-white text-xl font-semibold">Loading...</h2>
          <p class="w-1/3 text-center text-white">This may take a few seconds, please don't close this page.</p>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import { useForm } from "@inertiajs/vue3";
import { reactive, ref, provide, onMounted } from "vue";
import { router } from '@inertiajs/vue3';
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import Multiselect from "@suadelabs/vue3-multiselect";
import Pagination from "@/Components/Pagination";
export default {
  name: "IncidentReport",
  components: {
    AppLayout,
    Pagination,
    VueDatePicker,
    Multiselect,
  },
  props: {
    incidents: Object,
  },
  setup(props) {
    const i_details = ref(null);
    const show_detail = ref(false);
    const loading = ref(false);
    const formatter = ref({
      date: "YYYY-MM-DD",
      month: "MMM",
    });
    const form = useForm({
      general: null,
      type: 0,
      status: 0,
      period: null,
    });
    const clearSearch = () => {

      form.reset();
    }
    function submit() {
      form.post(
        "/incidentReport",
        {
          onSuccess: (page) => {
            loading.value = false;
          },
          onError: (errors) => {
            loading.value = false;
            console.log(errors);
          },
          onStart: () => {
            loading.value = true;
          },
        },
        { preserveState: true }
      );
    }
    function getStatus(data) {
      let status = "Open";
      if (data == 1) {
        status = "Open";
      } else if (data == 2) {
        status = "Escalated";
      } else if (data == 3) {
        status = "Closed";
      } else if (data == 4) {
        status = "Deleted";
      } else if (data == 5) {
        status = "Resolved";
      }
      return status;
    }

    const showTime = (mins) => {
      var hours = ((mins / 60) < 1) ? false : true;
      var days = ((mins / 60) < 24) ? false : true;
      if (days || hours) {
        var temp_days = (mins / 60) / 24;
        var total_hrs = (temp_days - Math.floor(temp_days))
        var total_days = temp_days - total_hrs
        var total_mins = ((total_hrs * 24) - Math.floor(total_hrs * 24)) * 60;
        return (total_days + 'd ' + Math.round(total_hrs * 24) + 'h:' + Math.round(total_mins) + 'm')
      } else {
        return (Math.round(mins) + 'm')
      }
    }
    const doExcel = () => {

      axios.post("/exportIncidentReportExcel", form, { responseType: "blob" }).then((response) => {
        console.log(response);
        var a = document.createElement("a");
        document.body.appendChild(a);
        a.style = "display: none";
        var blob = new Blob([response.data], {
          type: response.headers["content-type"],
        });
        const link = document.createElement("a");
        link.href = window.URL.createObjectURL(blob);
        link.download = `incidents_${new Date().getTime()}.xlsx`;
        link.click();
      });
    };

    return { getStatus, loading, formatter, form, submit, doExcel, showTime, clearSearch };
  },
};
</script>

<style></style>