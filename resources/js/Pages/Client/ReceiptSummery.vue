<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">Receipt Summery</h2>
    </template>

    <div class="py-2">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- search -->
        <div class="bg-white shadow sm:rounded-t-lg py-2 px-2 md:px-2">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full">
            <div class="col-span-1 sm:col-span-1">
              <div class="py-2">
                <label for="sh_general" class="block text-sm font-medium text-gray-700">General </label>
                <div class="mt-1 flex rounded-md shadow-sm">
                  <span
                    class="z-10 leading-snug font-normal absolute text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                    <i class="fas fa-user"></i>
                  </span>
                  <input type="text" v-model="search_form.sh_general" name="sh_general" id="sh_general"
                    class="pl-10 py-2.5 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                    placeholder="Customer/Company Name etc." tabindex="1" />
                </div>
              </div>
            </div>
            <div class="col-span-1 sm:col-span-1">
              <div class="py-2">
                <label for="sh_year" class="block text-sm font-medium text-gray-700">Bill Year</label>
                <div class="mt-1 flex rounded-md shadow-sm">
                  <input type="text" v-model="search_form.sh_year" name="sh_year" id="sh_year"
                    class="py-2.5 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                    tabindex="2" />
                </div>
              </div>
            </div>
          </div>

        </div>
        <div class="mb-2 py-2 px-2 md:px-2 bg-white shadow rounded-b-lg flex justify-between">
          <div class="flex">
            <a @click="doSearch"
              class="cursor-pointer inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">Search
              <i class="ml-1 fa fa-search text-white" tabindex="9"></i></a>
            <a @click="resetSearchForm"
              class="ml-2 cursor-pointer inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring focus:ring-gray-200 disabled:opacity-25 transition">Reset
              <i class="ml-1 fa fa-undo-alt text-white" tabindex="10"></i></a>
          </div>
          <div class="flex">
            <a @click="isOpen = true"
              class="cursor-pointer inline-flex items-center px-4 py-2 bg-green-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition">Export
              Excel <i class="ml-1 fa fa-download text-white" tabindex="11"></i></a>
          </div>
        </div>
        <!-- search -->
        <!-- <label class="text-sm">No Invoice <i class="fa fas text-gray-400 fa-stop-circle"></i></label> |
       <label class="text-sm">Outstanding <i class="fa fas text-gray-400 fa-minus-square"></i></label> |
       <label class="text-sm">Full Paid <i class="fa fas fa-check-square text-green-500"></i></label> |
       <label class="text-sm">Under Paid <i class="fa fas fa-check-square text-yellow-500"></i></label> |
       <label class="text-sm">Over Paid <i class="fa fas fa-check-square text-blue-500"></i></label> -->
        <div class="bg-white overflow-auto shadow-xl sm:rounded-lg" v-if="receipt_summeries.data">
          <!-- <button @click="openModal" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded my-3">Create New Township</button>
                 <input class="w-half rounded py-2 my-3 float-right" type="text" placeholder="Search Township" v-model="search" v-on:keyup.enter="searchTsp">
                    -->

          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  No.
                </th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Customer ID</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Jan
                </th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Feb
                </th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Mar
                </th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Apr
                </th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  May
                </th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Jun
                </th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Jul
                </th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Aug
                </th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Sep
                </th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Oct
                </th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Nov
                </th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Dec
                </th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Year
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="(row, index) in receipt_summeries.data" v-bind:key="row.id">

                <td class="px-4 py-2 whitespace-nowrap">{{ (index += receipt_summeries.from) }}</td>
                <td class="px-4 py-2 whitespace-nowrap">{{ row.ftth_id }}</td>
                <td class="px-4 py-2 whitespace-nowrap text-left"><span v-if="row['0']"><a
                      :href="`/pdfpreview2/${row['0']}`" target="_blank"> <i
                        class="fa fas fa-check-square text-green-600">
                      </i></a></span><span v-else><i class="fa fas fa-minus-square text-gray-400"></i></span></td>
                <td class="px-4 py-2 whitespace-nowrap text-left"><span v-if="row['1']"><a
                      :href="`/pdfpreview2/${row['1']}`" target="_blank"> <i
                        class="fa fas fa-check-square text-green-600">
                      </i></a></span><span v-else><i class="fa fas fa-minus-square text-gray-400"></i></span></td>
                <td class="px-4 py-2 whitespace-nowrap text-left"><span v-if="row['2']"><a
                      :href="`/pdfpreview2/${row['2']}`" target="_blank"> <i
                        class="fa fas fa-check-square text-green-600">
                      </i></a></span><span v-else><i class="fa fas fa-minus-square text-gray-400"></i></span></td>
                <td class="px-4 py-2 whitespace-nowrap text-left"><span v-if="row['3']"><a
                      :href="`/pdfpreview2/${row['3']}`" target="_blank"> <i
                        class="fa fas fa-check-square text-green-600">
                      </i></a></span><span v-else><i class="fa fas fa-minus-square text-gray-400"></i></span></td>
                <td class="px-4 py-2 whitespace-nowrap text-left"><span v-if="row['4']"><a
                      :href="`/pdfpreview2/${row['4']}`" target="_blank"> <i
                        class="fa fas fa-check-square text-green-600">
                      </i></a></span><span v-else><i class="fa fas fa-minus-square text-gray-400"></i></span></td>
                <td class="px-4 py-2 whitespace-nowrap text-left"><span v-if="row['5']"><a
                      :href="`/pdfpreview2/${row['5']}`" target="_blank"> <i
                        class="fa fas fa-check-square text-green-600">
                      </i></a></span><span v-else><i class="fa fas fa-minus-square text-gray-400"></i></span></td>
                <td class="px-4 py-2 whitespace-nowrap text-left"><span v-if="row['6']"><a
                      :href="`/pdfpreview2/${row['6']}`" target="_blank"> <i
                        class="fa fas fa-check-square text-green-600">
                      </i></a></span><span v-else><i class="fa fas fa-minus-square text-gray-400"></i></span></td>
                <td class="px-4 py-2 whitespace-nowrap text-left"><span v-if="row['7']"><a
                      :href="`/pdfpreview2/${row['7']}`" target="_blank"> <i
                        class="fa fas fa-check-square text-green-600">
                      </i></a></span><span v-else><i class="fa fas fa-minus-square text-gray-400"></i></span></td>
                <td class="px-4 py-2 whitespace-nowrap text-left"><span v-if="row['8']"><a
                      :href="`/pdfpreview2/${row['8']}`" target="_blank"> <i
                        class="fa fas fa-check-square text-green-600">
                      </i></a></span><span v-else><i class="fa fas fa-minus-square text-gray-400"></i></span></td>
                <td class="px-4 py-2 whitespace-nowrap text-left"><span v-if="row['9']"><a
                      :href="`/pdfpreview2/${row['9']}`" target="_blank"> <i
                        class="fa fas fa-check-square text-green-600">
                      </i></a></span><span v-else><i class="fa fas fa-minus-square text-gray-400"></i></span></td>
                <td class="px-4 py-2 whitespace-nowrap text-left"><span v-if="row['10']"><a
                      :href="`/pdfpreview2/${row['10']}`" target="_blank"><i
                        class="fa fas fa-check-square text-green-600">
                      </i></a></span><span v-else><i class="fa fas fa-minus-square text-gray-400"></i></span> </td>
                <td class="px-4 py-2 whitespace-nowrap text-left"><span v-if="row['11']"><a
                      :href="`/pdfpreview2/${row['11']}`" target="_blank"><i
                        class="fa fas fa-check-square text-green-600">
                      </i></a></span><span v-else><i class="fa fas fa-minus-square text-gray-400"></i></span> </td>
                <td class="px-4 py-2 whitespace-nowrap">{{ row.year }}</td>
              </tr>
            </tbody>
          </table>

          <div ref="isOpen" class="fixed z-10 inset-0 overflow-y-auto ease-out duration-400" v-if="isOpen">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
              <div class="fixed inset-0 transition-opacity">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
              </div>
              <!-- This element is to trick the browser into centering the modal contents. -->
              <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>​
              <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
                role="dialog" aria-modal="true" aria-labelledby="modal-headline">

                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                  <div class="">
                    <div class="mb-4">
                      <label for="period" class="block text-gray-700 text-sm font-bold mb-2">Select Billing :</label>

                      <select id="period" v-model="period" name="period"
                        class="py-2.5 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        tabindex="2">
                        <option value="0"> -Choose Package-</option>
                        <option v-for="row in bills" v-bind:key="row.id" :value="row.id">{{ row.name }}</option>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                  <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                    <button @click="exportExcel" type="button"
                      class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">Export</button>
                  </span>

                  <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
                    <button @click="closeModal" type="button"
                      class="inline-flex justify-center w-full rounded-md border border-gray-300 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5">Cancel</button>
                  </span>
                </div>

              </div>
            </div>
          </div>
        </div>
        <span v-if="receipt_summeries.total" class="w-full block mt-4">
          <label class="text-xs text-gray-600">{{ receipt_summeries.data.length }} Paid Customers in Current Page. Total
            Number of Paid Customers : {{ receipt_summeries.total }}</label>
        </span>
        <span v-if="receipt_summeries.links">
          <pagination class="mt-6" :links="receipt_summeries.links" />
        </span>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import Pagination from "@/Components/Pagination";
import { useForm } from "@inertiajs/vue3";
import { reactive, ref, onMounted } from "vue";
import { router } from '@inertiajs/vue3';
export default {
  name: "ReceiptSummery",
  components: {
    AppLayout,
    Pagination,
  },
  props: {
    receipt_summeries: Object,
    records: Object,
    bills: Object,
    errors: Object,
  },
  setup(props) {


    const search_form = useForm({
      id: null,
      name: null,
      sh_general: null,
      sh_year: new Date().getFullYear(),
    });
    const search = ref("");
    const isOpen = ref(false);
    const period = ref(null);
    function resetSearchForm() {
      search_form.reset();
      search_form.post("/receipt/search");
    }
    function doSearch() {
      search_form.post("/receipt/search");
    }
    function closeModal() {
      isOpen.value = false;
      period.value = null;
    }
    function exportExcel() {
      if (period.value) {
        let parm = Object.create({});
        parm.id = period.value;

        axios.post("/exportRevenue", parm, { responseType: "blob" }).then((response) => {
          console.log(response);
          var a = document.createElement("a");
          document.body.appendChild(a);
          a.style = "display: none";
          var blob = new Blob([response.data], {
            type: response.headers["content-type"],
          });
          const link = document.createElement("a");
          link.href = window.URL.createObjectURL(blob);
          link.download = `revenue${new Date().getTime()}.xlsx`;
          link.click();
        });
      }
    }
    // onMounted(() => {
    //   props.receipt_summeries.data.map(function (x) {
    //    for (let index = 0; index < 11; index++) {
    //       if(x[index])
    //       x[index] =  props.records.filter((d) => d.id == x[index])[0];
    //       }
    //   });
    // });
    return { search_form, search, doSearch, resetSearchForm, isOpen, period, closeModal, exportExcel };
  },
};
</script>
