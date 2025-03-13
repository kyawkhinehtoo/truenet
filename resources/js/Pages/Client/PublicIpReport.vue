<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">Public IP Usage Report</h2>
    </template>

    <div class="py-2">
      <!-- Advance Search -->
      <div class="max-w-full mx-auto sm:px-6 lg:px-8 mt-4">
        <div class="bg-white shadow sm:rounded-t-lg flex justify-between space-x-2 items-end py-2 px-2 md:px-2">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full">
            <div class="py-2 col-span-1 sm:col-span-1">
              <label for="sh_general" class="block text-sm font-medium text-gray-700">General </label>
              <div class="mt-1 flex rounded-md shadow-sm">
                <span
                  class="z-10 leading-snug font-normal  text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                  <i class="fas fa-user"></i>
                </span>
                <input type="text" v-model="form.general" name="sh_general" id="sh_general"
                  class="pl-10 py-2 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                  placeholder="IPv4 /Customer ID/Customer Name etc." tabindex="1" />
              </div>
            </div>

            <div class="py-2 col-span-1 sm:col-span-1">
              <label for="sh_period" class="block text-sm font-medium text-gray-700">End Date (Range) </label>
              <div class="mt-1 flex rounded-md shadow-sm">
                  <VueDatePicker v-model="form.end_date" :range="{ partialRange: true }" placeholder="Please End Date" 
                  :enable-time-picker="false" model-type="yyyy-MM-dd" id="end_date"
                  class="text-gray-400 text-sm focus:ring focus:ring-indigo-500 focus:border-indigo-500 focus:ring-opacity-10 focus:outline-none" />
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
      <div v-if="public_ips" class="max-w-full mx-auto sm:px-6 lg:px-8 mt-4">
        <div class="bg-white overflow-visible shadow-xl sm:rounded-lg" v-if="public_ips.data">
          <table class="min-w-full divide-y divide-gray-200 table-auto">
            <thead class="bg-gray-50">
              <tr class="text-left">
                <th scope="col" class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">No.</th>
                <th scope="col" class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">Ticket ID</th>
                <th scope="col" class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">Customer ID</th>
                <th scope="col" class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">Package</th>
                <th scope="col" class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">Description</th>
                <th scope="col" class="px-4 py-3 text-xs font-medium text-gray-500 uppercase text-right">Annual Fees
                </th>
                <th scope="col" class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">Start Date</th>
                <th scope="col" class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">End Date</th>
                <th scope="col" class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">Status</th>

              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 text-sm max-h-64">
              <tr v-for="(row, index) in public_ips.data" v-bind:key="row.id">
                <td class="px-4 py-3 text-xs font-medium">{{ (index += public_ips.from) }}</td>
                <td class="px-4 py-3 text-xs font-medium">{{ row.ip_address }}</td>
                <td class="px-4 py-3 text-xs font-medium text-indigo-600"><a
                    :href="'/customer/' + row.customer_id + '/edit'">{{
                    row.ftth_id }}</a></td>
                <td class="px-4 py-3 text-xs font-medium">{{ row.package_name }}</td>
                <td class="px-4 py-3 text-xs font-medium">
                  <p v-if="row.description.length > 30">{{ row.description.substring(0, 30) + '...' }}</p>
                  <p v-else>{{ row.description }}</p>
                </td>
                <td class="px-4 py-3 text-xs font-medium text-right">{{ row.annual_charge }} {{
                  row.currency.toUpperCase()
                  }}</td>
                <td class="px-4 py-3 text-xs font-medium whitespace-nowrap">{{ row.start_date }}</td>
                <td class="px-4 py-3 text-xs font-medium whitespace-nowrap">{{ row.end_date }}</td>
                <td class="px-4 py-3 text-xs font-medium">{{ row.status_name }}</td>

              </tr>
            </tbody>
          </table>
        </div>
        <span v-if="public_ips.total" class="w-full block mt-4">
          <label class="text-xs text-gray-600">{{ public_ips.data.length }} IPv4 Address in Current Page. Total Number
            of IP
            : {{ public_ips.total }}</label>
        </span>
        <span v-if="public_ips.links">
          <pagination class="mt-6" :links="public_ips.links" />
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
  name: "PublicIpReport",
  components: {
    AppLayout,
    Pagination,
    VueDatePicker,
    Multiselect,
  },
  props: {
    public_ips: Object,
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
      end_date: null,
    });
    const clearSearch = () => {
      form.general = null;
      form.end_date = '';
    }
    function submit() {
      form.post(
        "/publicIpReport",
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
    const doExcel = () => {
      axios.post("/exportPublicIpReportExcel", form, { responseType: "blob" }).then((response) => {
        var a = document.createElement("a");
        document.body.appendChild(a);
        a.style = "display: none";
        var blob = new Blob([response.data], {
          type: response.headers["content-type"],
        });
        const link = document.createElement("a");
        link.href = window.URL.createObjectURL(blob);
        link.download = `public_ip_report_${new Date().getTime()}.xlsx`;
        link.click();
      });
    };


    return { loading, formatter, form, submit, doExcel, clearSearch };
  },
};
</script>

<style></style>