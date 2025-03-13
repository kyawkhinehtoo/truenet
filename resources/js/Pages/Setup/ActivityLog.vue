<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">
        Activity Log
      </h2>
    </template>

    <div class="py-2">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="grid grid-cols-1 md:grid-cols-5 gap-2 w-full ">
          <div class="col-span-1 sm:col-span-1">
            <label for="sh_general" class="block text-sm font-medium text-gray-700">General </label>

            <div class="mt-1 flex rounded-md">
              <span
                class="z-0 leading-snug font-normal  text-center text-blueGray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                <i class="fas fa-user"></i>
              </span>
              <input type="text" v-model="searchForm.general" name="sh_general" id="sh_general"
                class="pl-10 py-2 focus:ring-0 flex-1 block w-full rounded-md sm:text-sm border-gray-200 text-gray-600"
                placeholder="General" tabindex="1" />
            </div>

          </div>
          <div class="col-span-1 sm:col-span-1">
            <label for="sh_dn" class="block text-sm font-medium text-gray-700">Type </label>
            <div class="mt-1 z-10">
              <multiselect deselect-label="Selected already" :options="options" v-model="searchForm.option" 
                :allow-empty="true" placeholder="Please Choose Type">
              </multiselect>
            </div>

          </div>
          <div class="col-span-1 sm:col-span-2">
            <label for="dateRange" class="block text-sm font-medium text-gray-700">Date Range </label>
            <div class="mt-1 text-xs">
             
             <VueDatePicker v-model="searchForm.dateRange" :range="{ partialRange: false }" placeholder="Please choose Date Range" 
                :enable-time-picker="false" model-type="yyyy-MM-dd" id="dateRange"
                class="text-gray-400 text-sm focus:ring focus:ring-indigo-500 focus:border-indigo-500 focus:ring-opacity-10 focus:outline-none" />
            </div>

          </div>
          <div class="col-span-1 sm:col-span-1 flex self-end mb-1">
            <a @click="searchPort"
              class="cursor-pointer inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">Search
              <i class="ml-1 fa fa-search text-white" tabindex="6"></i></a>
            <a @click="resetSearch"
              class="ml-2 cursor-pointer inline-flex items-center px-4 py-2 bg-yellow-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:border-yellow-900 focus:ring focus:ring-yellow-300 disabled:opacity-25 transition">Reset
              <i class="ml-1 fa fa-eraser text-white" tabindex="7"></i>
            </a>
          </div>
        </div>

        <div class="bg-white overflow-auto shadow-xl sm:rounded-lg" v-if="activities.data">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  No.
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Activity</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Change By</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Date</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  #</th>



              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <template v-for="(row, index) in activities.data " v-bind:key="row.id">
                <tr>
                  <td class="px-6 py-2 text-sm whitespace-nowrap">{{ activities.from + index }}</td>
                  <td class="px-6 py-2 text-sm whitespace-nowrap">{{ row.description }}</td>
                  <td class="px-6 py-2 text-sm whitespace-nowrap">{{ row.causer }}</td>
                  <td class="px-6 py-2 text-sm whitespace-nowrap">{{ row.date }}</td>
                  <td class="px-6 py-2 text-sm whitespace-nowrap">
                    <a href="#" @click="toggleDetails(index)" class="ml-2">
                      <span v-if="Object.keys(row.changes).length > 0">
                        <span v-if="expandedRow === index">
                          <i class="fa fa-circle-chevron-down text-indigo-400"></i>
                        </span>
                        <span v-else> <i class="fa fa-circle-chevron-right text-gray-400"></i></span>
                      </span>


                    </a>
                  </td>
                </tr>
                <tr v-if="Object.keys(row.changes).length > 0 && (expandedRow === index)"
                  class="bg-indigo-100 text-gray-800">

                  <th class="px-6 text-sm text-left" scope="col">Field</th>
                  <th class="px-6 text-sm text-left" scope="col">From</th>
                  <th class="px-6 text-sm text-left" scope="col" colspan="3">To</th>
                </tr>
                <tr v-if="Object.keys(row.changes).length > 0 && (expandedRow === index)"
                  v-for="(change, field) in row.changes" :key="field" class="bg-indigo-50 text-gray-600">

                  <td class="px-6 text-sm capitalize">{{ field.replace("_", " ") }}</td>
                  <td class="px-6 text-sm">{{ change.from ?? 'N/A' }}</td>
                  <td class="px-6 text-sm" colspan="3">{{ change.to ?? 'N/A' }}</td>
                </tr>



              </template>
            </tbody>
          </table>

        </div>
        <span v-if="activities.links">
          <pagination class="mt-6" :links="activities.links" />
        </span>

      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout';
import Pagination from '@/Components/Pagination';
import { reactive, ref, onMounted } from 'vue';
import { router,useForm } from "@inertiajs/vue3";
import Multiselect from "@suadelabs/vue3-multiselect";
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
export default {
  name: 'ActivityLog',
  components: {
    AppLayout,
    Multiselect,
    VueDatePicker,
    Pagination
  },
  props: {
    activities: Object,
    errors: Object
  },
  setup(props) {
    let expandedRow = ref(false);
    let options = ref(["Customer Created",
      "Customer updated",
      "Bill Receipt",
      "Invoice updated",
      "Receipt Reset"]);
    const formatter = ref({
      date: "YYYY-MM-DD",
      month: "MMM",
    });
    function toggleDetails(index) {
      // Toggle the clicked row and collapse others
      expandedRow.value = expandedRow.value === index ? null : index;
    }
    const searchForm = useForm({
      general: null,
      option: null,
      dateRange: "",
    });
    function searchPort() {
      router.post('/activity-log', searchForm, { preserveState: true })
    }
    const resetSearch = () => {

      searchForm.reset();
      searchPort();
    }
    return { expandedRow, toggleDetails, searchForm, options, searchPort, resetSearch, formatter };
  }



}
</script>
<style>
.multiselect__tags {

  padding: 5px 40px 0 5px !important;

}
</style>