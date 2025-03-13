<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">Bill Receipt Report</h2>
    </template>

    <div class="py-2">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <label for="name" class="block text-sm font-bold text-indigo-700 mt-4">Report Preference</label>

        <!-- Advance Search -->
        <div class="bg-white shadow sm:rounded-t-lg flex justify-between space-x-2 items-end py-2 px-2 md:px-2">
          <div class="grid grid-cols-1 md:grid-cols-6 gap-2 w-full">


            <div class="col-span-1 sm:col-span-1">
              <div class="py-2">
                <label for="general" class="block text-sm font-medium text-gray-700">General </label>
                <div class="mt-1 flex rounded-md shadow-sm">
                  <span
                    class="z-10 leading-snug font-normal text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                    <i class="fas fa-user"></i>
                  </span>
                  <input type="text" v-model="form.general" name="general" id="general"
                    class="pl-10 py-2.5 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded sm:text-sm border-gray-200"
                    placeholder="Customer/Company Name etc." tabindex="1" />

                </div>
              </div>

            </div>


            <div class="col-span-1 sm:col-span-1">
              <div class="py-2">
                <label for="date" class="block text-sm font-medium text-gray-700">Receipt Date </label>
                <div class="mt-1 flex rounded-md shadow-sm">
                   <VueDatePicker v-model="form.date" :range="{ partialRange: true }" placeholder="Please choose Order Date" 
              :enable-time-picker="false" model-type="yyyy-MM-dd" id="order_date"
              class="text-gray-400 text-sm focus:ring focus:ring-indigo-500 focus:border-indigo-500 placeholder:text-xs focus:ring-opacity-10 focus:outline-none" />
                </div>
              </div>
            </div>

            <div class="col-span-1 sm:col-span-1">
              <div class="py-2">
                <label for="bill_id" class="block text-sm font-medium text-gray-700">Bill Name </label>
                <div class="mt-1 flex rounded-md shadow-sm" v-if="bill_list.length !== 0">
                  <multiselect deselect-label="Selected already" :options="bill_list" track-by="id" label="name"
                    v-model="form.bill_id" :allow-empty="true" :multiple="true"> </multiselect>

                </div>
              </div>
            </div>
            <div class="col-span-1 sm:col-span-1">
              <div class="py-2">
                <label class="block text-sm font-medium text-gray-700">Yesterday Collection </label>
                <div class="mt-4 flex ">{{ yesterday_collection.toLocaleString('en-US') }}</div>
              </div>
            </div>
            <div class="col-span-1 sm:col-span-1">
              <div class="py-2">
                <label class="block text-sm font-medium text-gray-700">Today Collection </label>
                <div class="mt-4 flex ">{{ today_collection.toLocaleString('en-US') }}

                </div>
              </div>
            </div>
            <div class="col-span-1 sm:col-span-1">
              <div class="py-2">
                <label class="block text-sm font-medium text-gray-700">Total Search Result </label>
                <div class="mt-4 flex ">{{ select_total.toLocaleString('en-US') }}</div>
              </div>
            </div>
          </div>
        </div>

        <div class="mb-2 py-2 px-2 md:px-2 bg-white shadow rounded-b-lg flex justify-between">
          <div class="flex">
            <a @click="doSearch"
              class="cursor-pointer inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">Search
              <i class="ml-1 fa fa-search text-white"></i></a>
            <a @click="resetForm"
              class="ml-2 cursor-pointer inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring focus:ring-gray-200 disabled:opacity-25 transition">Reset
              <i class="ml-1 fa fa-undo-alt text-white"></i></a>
          </div>
          <div class="flex">
            <button @click="exportExcel" type="button"
              class="inline-flex items-center px-4 py-2 bg-green-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition">Export</button>

          </div>
        </div>

        <!-- End of Advance Search -->

        <div class="bg-white overflow-auto shadow-xl sm:rounded-lg" v-if="receipt_records.data">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Bill
                  Name</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Bill
                  For</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Invoice Number</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Customer ID</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Issue
                  Amount</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Receipt Amount</th>

                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Receipt Date</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Customer Paid Date</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 text-sm">
              <tr v-for="row in receipt_records.data" v-bind:key="row.id">
                <td class="px-6 py-3 text-xs font-medium whitespace-nowrap">{{ row.bill_name }}</td>
                <td class="px-6 py-3 text-xs font-medium whitespace-nowrap">{{ row.year }}-{{ row.month }}</td>
                <td class="px-6 py-3 text-xs font-medium whitespace-nowrap">{{ row.bill_number }}</td>
                <td class="px-6 py-3 text-xs font-medium whitespace-nowrap">{{ row.ftth_id }}</td>
                <td class="px-6 py-3 text-xs font-medium whitespace-nowrap">{{ row.issue_amount }}</td>
                <td class="px-6 py-3 text-xs font-medium whitespace-nowrap">{{ row.collected_amount }}</td>

                <td class="px-6 py-3 text-xs font-medium whitespace-nowrap">{{ row.created_at }}</td>
                <td class="px-6 py-3 text-xs font-medium whitespace-nowrap">{{ row.receipt_date }}</td>
              </tr>
            </tbody>
          </table>
        </div>
        <span v-if="receipt_records.total" class="w-full block mt-4">
          <label class="text-xs text-gray-600">{{ receipt_records.data.length }} Receipt_records in Current Page. Total
            Number of Receipt_records : {{ receipt_records.total }}</label>
        </span>

        <span v-if="receipt_records.links">
          <pagination class="mt-4" :links="receipt_records.links" />
        </span>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import Pagination from "@/Components/Pagination";
import { useForm } from "@inertiajs/vue3";
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import { onMounted, onUpdated, provide, ref } from "vue";
import { router } from '@inertiajs/vue3';
import Multiselect from "@suadelabs/vue3-multiselect";
export default {
  name: "DailyReceipt",
  components: {
    AppLayout,
    VueDatePicker,
    Multiselect,
    Pagination,
  },
  props: {
    receipt_records: Object,
    bill_list: Object,
    yesterday_collection: Object,
    today_collection: Object,
    select_total: Object,
    errors: Object,
  },
  setup(props) {
    const formatter = ref({
      date: "YYYY-MM-DD",
      month: "MMM",
    });
    const form = useForm({
      general: null,
      date: null,
      bill_id: null,
    });
    function resetForm() {
      form.general = null;
      form.date = null;
      form.bill_id = null;
    }
    const doSearch = () => {
      form.post("/dailyreceipt/show", {
        onSuccess: (page) => { },
        onError: (errors) => {
          console.log(errors);
        },
      });
    };
    function exportExcel() {
      axios.post("/exportReceipt", form, { responseType: "blob" }).then((response) => {
        var a = document.createElement("a");
        document.body.appendChild(a);
        a.style = "display: none";
        var blob = new Blob([response.data], {
          type: response.headers["content-type"],
        });
        const link = document.createElement("a");
        link.href = window.URL.createObjectURL(blob);
        link.download = `receipts${new Date().getTime()}.xlsx`;
        link.click();
      });
    }
    return { doSearch, resetForm, exportExcel, form, formatter };
  },
};
</script>
<style>

</style>