<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">Radius Report</h2>
    </template>

    <div class="py-2">
      <!-- Advance Search -->
      <div class="max-w-full mx-auto sm:px-6 lg:px-8 mt-4">
        <div class="bg-white shadow sm:rounded-t-lg items-end py-2 px-2 md:px-2">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-6 w-full">
            <div class="py-2 col-span-1 sm:col-span-1">
              <label class="text-sm font-medium text-gray-700">General</label>
              <div class="mt-1 flex rounded-md shadow-sm">
                <span
                  class="z-10 leading-snug font-normal  text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                  <i class="fas fa-user"></i>
                </span>
                <input type="text" v-model="form.general" name="sh_general" id="sh_general"
                  class="pl-10 py-2.5 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                  placeholder="Ticket ID/Customer ID/Customer Name etc." tabindex="1" />
              </div>


            </div>
            <div class="py-2 col-span-3 sm:col-span-3">
              <fieldset class="mt-1 text-sm border border-solid border-gray-300 rounded-md p-3 flex justify-between">
                <legend class="text-sm font-medium text-gray-700">Radius Status</legend>
                <label class="flex items-center"> <input type="radio" class="form-radio h-5 w-5 text-blue-900"
                    name="type" v-model="form.radius_status" value="any" /><span class="ml-2 text-gray-700">Any</span>
                </label>
                <label class="flex items-center"> <input type="radio" class="form-radio h-5 w-5 text-green-600"
                    name="type" v-model="form.radius_status" value="online" /><span
                    class="ml-2 text-gray-700">Online</span> </label>
                <label class="flex items-center"> <input type="radio" class="form-radio h-5 w-5 text-blue-600"
                    name="type" v-model="form.radius_status" value="offline" /><span
                    class="ml-2 text-gray-700">Offline</span> </label>
                <label class="flex items-center"> <input type="radio" class="form-radio h-5 w-5 text-red-500"
                    name="type" v-model="form.radius_status" value="disabled" /><span
                    class="ml-2 text-gray-700">Disabled</span>
                </label>
                <label class="flex items-center"> <input type="radio" class="form-radio h-5 w-5 text-indigo-600"
                    name="type" v-model="form.radius_status" value="range_expired" /><span
                    class="ml-2 text-gray-700">Range
                    Expired</span> </label>
                <label class="flex items-center"> <input type="radio" class="form-radio h-5 w-5 text-indigo-600"
                    name="type" v-model="form.radius_status" value="expired" /><span
                    class="ml-2 text-gray-700">Expired</span> </label>
                <label class="flex items-center"> <input type="radio" class="form-radio h-5 w-5 text-yellow-600"
                    name="type" v-model="form.radius_status" value="not found" /><span class="ml-2 text-gray-700">Not
                    Found</span>
                </label>
                <label class="flex items-center"> <input type="radio" class="form-radio h-5 w-5 text-gray-600"
                    name="type" v-model="form.radius_status" value="no account" /><span class="ml-2 text-gray-700">No
                    Account</span>
                </label>

              </fieldset>


            </div>


          </div>

          <!-- new row  -->
          <div class="grid grid-cols-1 md:grid-cols-4 gap-6 w-full"
            v-if="form.radius_status != 'no account' && form.radius_status != 'not found' && form.radius_status != 'any'">
            <div class="py-2 col-span-2 sm:col-span-2">
              <label class="text-sm font-medium text-gray-700">Expiry Date</label>
              <div class="mt-1 flex rounded-md shadow-sm">
           
                <VueDatePicker v-model="form.expiration" :range="{ partialRange: true }" placeholder="Please choose Expiry Date" 
                  :enable-time-picker="false" model-type="yyyy-MM-dd" id="order_date"
                  class="text-gray-400 text-sm focus:ring focus:ring-indigo-500 focus:border-indigo-500 focus:ring-opacity-10 focus:outline-none" />
              </div>


            </div>


          </div>
        </div>
        <div class="mb-2 py-2 px-2 md:px-2 bg-white shadow rounded-b-lg flex justify-between">
          <div class="inline-flex ">
            <label class="text-sm font-medium text-gray-700 w-20 mt-2" for="vlan">VLAN : </label>
            <multiselect :class="border - 0" deselect-label="Selected already" :options="vlans" track-by="vlan"
              label="vlan" v-model="form.vlan" :allow-empty="true"></multiselect>
          </div>

          <div class="flex">
            <a @click="submit"
              class="cursor-pointer inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">Search
              <i class="ml-1 fa fa-search text-white" tabindex="10"></i></a>
            <a @click="resetFrom"
              class="ml-2 cursor-pointer inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring focus:ring-gray-200 disabled:opacity-25 transition">Reset
              <i class="ml-1 fa fa-undo-alt text-white" tabindex="11"></i></a>
            <a @click="doExcel"
              class="ml-2 cursor-pointer inline-flex items-center px-4 py-2 bg-green-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition">Export
              Excel <i class="ml-1 fa fa-download text-white" tabindex="11"></i></a>
          </div>
        </div>
      </div>
      <!-- End of Advance Search -->
      <div class="max-w-full mx-auto sm:px-6 lg:px-8 mt-4">



        <div class="bg-white overflow-auto shadow-xl sm:rounded-lg" v-if="customers.data">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  v-if="radius">Radius</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  ID
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Name
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Package</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Township</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Status</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Expiration</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 text-sm">
              <tr v-for="row in customers.data" v-bind:key="row.id" :class='" text-" + row.color'>
                <td class="px-3 py-3 text-xs font-medium whitespace-nowrap" v-if="radius">
                  <div
                    class="text-xs inline-flex items-center font-medium leading-sm capitalize  px-3 py-1 rounded-full"
                    :class="{
                      'bg-green-200 text-green-700 ': row.radius_status == 'online',
                      'bg-blue-200 text-blue-700': row.radius_status == 'offline',
                      'bg-red-200 text-red-700': row.radius_status == 'disabled',
                      'bg-indigo-400 text-white border': row.radius_status == 'expired',
                      'bg-orange-200 text-orange-700': row.radius_status == 'not found',
                      'bg-white text-gray-700 border': row.radius_status == 'no account'

                    }">{{ row.radius_status }}</div>
                </td>
                <td class="px-6 py-3 text-xs font-medium  whitespace-nowrap">{{ row.ftth_id }}</td>
                <td class="px-6 py-3 text-xs font-medium  whitespace-nowrap">{{ row.name }}</td>
                <td class="px-6 py-3 text-xs font-medium  whitespace-nowrap">{{ row.package }}</td>
                <td class="px-6 py-3 text-xs font-medium  whitespace-nowrap">{{ row.township }}</td>
                <td class="px-6 py-3 text-xs font-medium  whitespace-nowrap">{{ row.status }}</td>
                <td class="px-6 py-3 text-xs font-medium  whitespace-nowrap">{{ row.expiration }}</td>

              </tr>
            </tbody>
          </table>


        </div>
        <span v-if="customers.total" class="w-full block mt-4">
          <label class="text-xs text-gray-600">{{ customers.data.length }} Customers in Current Page. Total Number of
            Customers : {{ customers.total }}</label>
        </span>
        <span v-if="customers.links">
          <pagination class="mt-6" :links="customers.links" />
        </span>
      </div>
    </div>
    <div v-if="loading"
      class="fixed top-0 left-0 right-0 bottom-0 w-full h-screen z-50 overflow-hidden bg-gray-700 opacity-75 flex flex-col items-center justify-center">
      <div class="loader ease-linear rounded-full border-4 border-t-4 border-gray-200 h-12 w-12 mb-4"></div>
      <h2 class="text-center text-white text-xl font-semibold">Loading...</h2>
      <p class="w-1/3 text-center text-white">This may take a few seconds, please don't close this page.</p>
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
  name: "Radius",
  components: {
    AppLayout,
    VueDatePicker,
    Pagination,
    Multiselect
  },
  props: {
    customers: Object,
    errors: Object,
    vlans: Object,
    radius: Object
  },
  setup(props) {
    const loading = ref(false);
    const formatter = ref({
      date: "YYYY-MM-DD",
      month: "MMM",
    });
    const form = useForm({
      general: null,
      vlan: null,
      radius_status: 'any',
      expiration: "",
    });
    const resetFrom = () => {
      form.reset();
    };


    function submit() {
      form.post(
        "/showRadius",
        {
          onSuccess: (page) => {

          },
          onError: (errors) => {

            console.log(errors);
          },
          onStart: () => {

          },
        },
        { preserveState: true }
      );
    }

    const doExcel = () => {
      loading.value = true;
      axios.post("/RadiusExport",
        form)
        .then(response => {
          console.log(response)
          loading.value = false;
          var a = document.createElement("a");
          document.body.appendChild(a);
          a.style = "display: none";
          let blob = new Blob([response.data], { type: 'text/csv' }),
            url = window.URL.createObjectURL(blob)
          a.href = url;
          a.download = 'radius_users.csv';
          a.click();
          window.URL.revokeObjectURL(url);
        })
    };
    // const sortBy = (d) => {
    //   sort.value = d;
    //   sort.order = 'asc';
    //   if(sort.order == 'asc'){
    //     sort.order = 'desc';
    //   }
    //   console.log("search value is" + sort.value);
    //   router.post('/customer/all/',{sort: sort.value, order:sort.order},{ preserveState: true });
    // };


    return { submit, form, doExcel, formatter, resetFrom, loading };
  },
};
</script>
