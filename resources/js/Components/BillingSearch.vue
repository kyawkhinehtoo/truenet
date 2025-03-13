<template>
  <!-- Advance Search -->
  <div class="bg-white shadow sm:rounded-t-lg flex justify-between space-x-2 items-end py-2 px-2 md:px-2">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 w-full">
      <div class="col-span-1 sm:col-span-1">
        <div class="py-2">
          <label for="sh_general" class="block text-sm font-medium text-gray-700">General </label>
          <div class="mt-1 flex rounded-md shadow-sm">
            <span
              class="z-10 leading-snug font-normal absolute text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
              <i class="fas fa-user"></i>
            </span>
            <input type="text" v-model="sh_general" name="sh_general" id="sh_general"
              class="pl-10 py-2 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
              placeholder="Customer/Company Name etc." tabindex="1" />

          </div>

        </div>
        <div class="py-2">
          <label for="sh_payment" class="block text-sm font-medium text-gray-700">Paid/Unpaid</label>
          <div class="mt-1 flex rounded-md shadow-sm">
            <select id="sh_payment" v-model="sh_payment" name="sh_payment"
              class="py-2 block w-full px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              tabindex="2">
              <option value="0"> -Both-</option>
              <option value="paid"> Paid </option>
              <option value="unpaid"> Unpaid</option>
            </select>
          </div>
        </div>
      </div>
      <div class="col-span-1 sm:col-span-1">
        <div class="py-2">
          <label for="sh_package" class="block text-sm font-medium text-gray-700">Package </label>
          <div class="mt-1 flex rounded-md shadow-sm">
            <span
              class="z-10 leading-snug font-normal  text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
              <i class="fas fa-user"></i>
            </span>
            <select id="sh_package" v-model="sh_package" name="sh_package"
              class="pl-10 py-2 block w-full px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              tabindex="2">
              <option value="0"> -Choose Package-</option>
              <option v-for="row in packages" v-bind:key="row.id" :value="row.id">{{ row.item_data }}</option>
            </select>

          </div>

        </div>
        <div class="py-2">
          <label for="sh_installation" class="block text-sm font-medium text-gray-700">Installation Date </label>
          <div class="mt-1 flex rounded-md shadow-sm">
              <VueDatePicker v-model="sh_installation" :range="{ partialRange: true }" placeholder="Please choose Installation Date" 
              :enable-time-picker="false" model-type="yyyy-MM-dd" id="sh_installation"
              class="text-gray-400 text-sm focus:ring focus:ring-indigo-500 focus:border-indigo-500 focus:ring-opacity-10 focus:outline-none" />
          </div>
        </div>

      </div>
      <div class="col-span-1 sm:col-span-1">
        <div class="py-2">
          <label for="sh_township" class="block text-sm font-medium text-gray-700">Township </label>
          <div class="mt-1 flex rounded-md shadow-sm">
            <span
              class="z-10 leading-snug font-normal  text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
              <i class="fas fa-user"></i>
            </span>
            <select id="sh_township" v-model="sh_township" name="sh_township"
              class="pl-10 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              tabindex="3">
              <option value="0"> -Choose Township -</option>
              <option v-for="row in townships" v-bind:key="row.id" :value="row.id">{{ row.name }}</option>
            </select>

          </div>

        </div>
        <div class="py-2">
          <label for="sh_status" class="block text-sm font-medium text-gray-700">Customer Status </label>
          <div class="mt-1 flex rounded-md shadow-sm">
            <span
              class="z-10 leading-snug font-normal text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
              <i class="fas fa-user"></i>
            </span>
            <select id="sh_status" v-model="sh_status" name="sh_status"
              class="pl-10 py-2 block w-full px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              tabindex="7">
              <option value="0"> -Choose Status -</option>
              <option v-for="row in status" v-bind:key="row.id" :value="row.id">{{ row.name }}</option>
            </select>

          </div>

        </div>
      </div>
      <div class="col-span-1 sm:col-span-1">
      
        <div class="py-2">
          <label for="sh_package_type" class="block text-sm font-medium text-gray-700">Package Type </label>
          <div class="mt-1 flex rounded-md shadow-sm">
            <span
              class="z-10 leading-snug font-normal text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
              <i class="fas fa-user"></i>
            </span>
            <select id="sh_package_type" v-model="sh_package_type" name="sh_package_type"
              class="pl-10 py-2 block w-full px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              tabindex="7">
              <option value="0"> -Choose Type -</option>
              <option v-for="row in package_type" v-bind:key="row.type" :value="row.type">{{ row.type.toUpperCase() }}
              </option>
            </select>

          </div>

        </div>
      </div>

    </div>

  </div>
  <div class="mb-2 py-2 px-2 md:px-2 bg-white shadow rounded-b-lg flex">
    <div class="flex">
      <a @click="doSearch"
        class="cursor-pointer inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">Search
        <i class="ml-1 fa fa-search text-white" tabindex="10"></i></a>
      <a @click="clearSearch"
        class="ml-2 cursor-pointer inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring focus:ring-gray-200 disabled:opacity-25 transition">Reset
        <i class="ml-1 fa fa-undo-alt text-white" tabindex="11"></i></a>
    </div>

  </div>

  <!-- End of Advance Search -->
</template>

<script>
import { reactive, inject, ref } from 'vue'
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import { router } from '@inertiajs/vue3';
export default {
  name: "BillingSearch",
  emits: ['search_parameter'],
  components: {
    VueDatePicker,
  },
  setup(props, context) {
    const packages = inject("packages");
    
    const package_type = inject("package_type");
    const townships = inject("townships");
    const status = inject("status");
    const formatter = ref({
      date: 'YYYY-MM-DD',
      month: 'MMM'
    });
    let sh_general = ref("");
    let sh_installation = ref("");
    let sh_payment = ref(0);
    let sh_package = ref(0);
    let sh_township = ref(0);
    let sh_status = ref(0);

    let sh_package_type = ref(0);


    const clearSearch = () => {
      let myurl = Object.create({});
      sh_general.value = "";
      sh_package.value = 0;
      sh_township.value = 0;

      sh_payment.value = 0;
      sh_status.value = 0;

      sh_package_type.value = 0;
      sh_installation.value ="";

      context.emit('search_parameter', myurl);
    }

    const doSearch = () => {

      let myurl = Object.create({});

      if (sh_general.value != "") {
        myurl.general = sh_general.value;
      }
      if (sh_package.value != 0) {
        myurl.package = sh_package.value;
      }
      if (sh_township.value != 0) {
        myurl.township = sh_township.value;
      }
      if (sh_status.value != 0) {
        myurl.status = sh_status.value;

      }
      if (sh_installation.value.from != "" && sh_installation.value.to != "") {
        myurl.installation = sh_installation.value;
      }
      if (sh_payment.value != 0) {
        myurl.payment_type = sh_payment.value;
      }
 
      if (sh_package_type.value != 0) {
        myurl.package_type = sh_package_type.value;
      }


      context.emit('search_parameter', myurl);
    };
    function onClickButton(event) {
      console.log("Emit..")
      context.emit('hello', 'Hello World');
    }

    return {
      formatter, sh_general, sh_general, sh_installation, sh_payment, sh_package, sh_township, sh_status, sh_package_type, packages, package_type, townships, status, doSearch, clearSearch
    }
  }
}
</script>

<style scoped></style>