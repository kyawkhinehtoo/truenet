<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">Bill Adjustment List</h2>
    </template>

    <div class="py-2">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex">
        <label for="name" class="block text-sm font-bold text-gray-700 w-24 mt-3">Billing List :</label>
        <select id="id" v-model="form.bill_id" name="bill_id"
          class="ml-2 py-2.5 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          tabindex="1" required>
          <option value="0">-Choose Package-</option>
          <option v-for="row in lists" v-bind:key="row.id" :value="row.id">{{ row.name }}</option>
        </select>

        <a @click="view"
          class="ml-2 cursor-pointer inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">View
          <i class="ml-1 fa fa-search text-white" tabindex="2"></i></a>
      </div>

      <div class="mt-4 mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-4 gap-6 w-full">
        <div class="col-span-1 sm:col-span-1">
          <label for="sh_general" class="block text-sm font-medium text-gray-700">General </label>
          <div class="mt-1 flex rounded-md shadow-sm">
            <span
              class="z-10 leading-snug font-normal absolute text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
              <i class="fas fa-user"></i>
            </span>
            <input type="text" v-model="form.general" name="sh_general" id="sh_general"
              class="pl-10 py-2.5 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
              placeholder="Customer/Company Name etc." tabindex="3" />
          </div>
        </div>
        <div class="col-span-1 sm:col-span-1">
          <label for="sh_package" class="block text-sm font-medium text-gray-700">Payment Type </label>
          <div class="mt-1 flex rounded-md shadow-sm">
            <select id="sh_package" v-model="form.adjustment_type" name="sh_package"
              class="py-2.5 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              tabindex="4">
              <option value="0">-Both-</option>
              <option value="Debit Note">Debit Note</option>
              <option value="Credit Note">Credit Note</option>
            </select>
          </div>
        </div>
        <div class="col-span-1 sm:col-span-1 flex items-end">
          <a @click="filter"
            class="ml-2 cursor-pointer inline-flex items-center px-4 py-3 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">Filter
            <i class="ml-1 fa fa-search text-white" tabindex="5"></i></a>
        </div>
      </div>

      <div v-if="billings" class="max-w-full mx-auto sm:px-6 lg:px-8 mt-4">
        <div class="bg-white overflow-auto shadow-xl sm:rounded-lg" v-if="billings.data">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col"
                  class="pl-3 px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  No.</th>
                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Bill
                  Number</th>
                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Customer ID</th>
                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Package</th>
                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Speed
                </th>
                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Usage
                </th>
                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Total
                  Payable</th>
                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Type
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="(row, index) in billings.data" v-bind:key="row.id">
                <td class="pl-4 px-2 py-3 text-xs whitespace-nowrap">{{ (index += billings.from) }}</td>
                <td class="px-2 py-3 text-xs whitespace-nowrap">{{ row.bill_number }}</td>
                <td class="px-2 py-3 text-xs whitespace-nowrap">{{ row.ftth_id }}</td>
                <td class="px-2 py-3 text-xs whitespace-nowrap">{{ row.service_description }}</td>
                <td class="px-2 py-3 text-xs whitespace-nowrap">{{ row.qty }}</td>
                <td class="px-2 py-3 text-xs whitespace-nowrap">{{ row.usage_days }}</td>
                <td class="px-2 py-3 text-xs whitespace-nowrap">{{ row.total_payable }}</td>
                <td class="px-2 py-3 text-xs whitespace-nowrap">{{ row.adjustment_type }}</td>
              </tr>
            </tbody>
          </table>
        </div>
        <span v-if="billings.total" class="w-full block mt-4">
          <label class="text-xs text-gray-600">{{ billings.data.length }} Invoices in Current Page. Total Number of
            Invoices
            : {{ billings.total }}</label>
        </span>
        <span v-if="billings.links">
          <pagination class="mt-6" :links="billings.links" />
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
import { reactive, ref, provide, onMounted, onUpdated } from "vue";
import { router } from '@inertiajs/vue3';
import Multiselect from "@suadelabs/vue3-multiselect";
import Pagination from "@/Components/Pagination";
import BillingSearch from "@/Components/BillingSearch";
import { Switch } from "@headlessui/vue";
export default {
  name: "BillList",
  components: {
    AppLayout,
    Pagination,
    BillingSearch,
    Multiselect,
    Switch,
  },
  props: {
    lists: Object,
    billings: Object,
    packages: Object,
    projects: Object,
    townships: Object,
    status: Object,
    errors: Object,
    user: Object,
    users: Object,
    roles: Object,
    max_receipt: Object,
    receivable: Object,
    paid: Object,
    current_bill: Object,
    smsgateway: Object,
    prepaid_customers: Object,
  },
  setup(props) {
    const formatter = ref({
      date: "YYYY-MM-DD",
      month: "MMM",
    });
    provide("packages", props.packages);
    provide("projects", props.projects);
    provide("townships", props.townships);
    provide("status", props.status);
    let show_search = ref(false);
    let loading = ref(false);
    let parameter = ref("");

    const form = useForm({
      id: null,
      bill_id: props.current_bill ? props.current_bill["id"] : null,
      general: null,
      adjustment_type: 0,
    });
    const filter = (parm) => {
      let url = "/showAdjustment";
      if (form.bill_id != null) {
        parm.bill_id = form.bill_id;
      }

      router.get(url, form, { preserveState: true });
    };
    const toggleAdv = () => {
      show_search.value = !show_search.value;
      console.log(props.route);
    };

    function view() {
      //form._method = "POST";
      let parm = Object.create({});
      parm.bill_id = form.bill_id;
      parameter.value = parm;
      form._method = "GET";
      router.get("/showAdjustment", form, {
        preserveState: true,
        resetOnSuccess: true,
        onSuccess: (page) => {
          cal_percent();
        },
        onError: (errors) => {
          console.log("error ..".errors);
        },
      });
    }

    function doExcel() {
      axios.post("/exportBillingExcel", parameter.value).then((response) => {
        console.log(response);
        var a = document.createElement("a");
        document.body.appendChild(a);
        a.style = "display: none";
        let blob = new Blob([response.data], { type: "text/csv" }),
          url = window.URL.createObjectURL(blob);
        a.href = url;
        a.download = "billings.csv";
        a.click();
        window.URL.revokeObjectURL(url);
      });
    }

    onMounted(() => {
      let bill_id = props.current_bill ? props.current_bill["id"] : null;
      if (bill_id) {
        let parm = Object.create({});
        parm.bill_id = bill_id;
        parameter.value = parm;
      }
      //paid_percent.value = temp;
    });
    onUpdated(() => {
      let bill_id = props.current_bill ? props.current_bill["id"] : null;
      if (bill_id) {
        let parm = Object.create({});
        parm.bill_id = bill_id;
        parameter.value = parm;
      }
    });
    return { view, show_search, form, toggleAdv, filter, doExcel };
  },
};
</script>
<style scoped>
.loader {
  border-top-color: #3498db;
  -webkit-animation: spinner 1.5s linear infinite;
  animation: spinner 1.5s linear infinite;
}

@-webkit-keyframes spinner {
  0% {
    -webkit-transform: rotate(0deg);
  }

  100% {
    -webkit-transform: rotate(360deg);
  }
}

@keyframes spinner {
  0% {
    transform: rotate(0deg);
  }

  100% {
    transform: rotate(360deg);
  }
}

.bg-marga {
  background: #255978;
  color: #ffffff;
}

.border-marga {
  border-color: #255978;
}
</style>