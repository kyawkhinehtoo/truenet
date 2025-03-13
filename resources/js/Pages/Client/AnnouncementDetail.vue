<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">Announcement</h2>
    </template>

    <div class="py-2">
      
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex gap-2 content-center justify-center">
        <label for="name" class="block text-sm font-bold text-gray-700 mt-4">Announcement Title </label>
        <div class="mt-1 flex rounded-md shadow-sm w-4/5">
          <input type="text" v-model="createForm.name" name="name" id="name"
            class="py-2.5 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
            placeholder="Please Enter Announcement Name" />
        </div>
      </div>
      <div v-if="$page.props.errors.name" class="max-w-7xl mx-auto sm:px-6 lg:px-8 text-red-500">{{
        $page.props.errors.name }}</div>
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <label for="name" class="block text-sm font-bold text-indigo-700 mt-4">Announcement Preference</label>

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
                  <input type="text" v-model="createForm.general" name="sh_general" id="sh_general"
                    class="pl-10 py-2.5 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                    placeholder="Customer/Company Name etc." tabindex="1" />
                  <div v-if="$page.props.errors.general" class="text-red-500">{{ $page.props.errors.general }}</div>
                </div>
              </div>
              <div class="py-2">
                <label for="sh_template" class="block text-sm font-medium text-gray-700">Template </label>
                <div class="mt-1 flex rounded-md shadow-sm">
                  <select id="sh_template" v-model="createForm.template" name="sh_template"
                    class="py-2.5 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    tabindex="5" @change="template_change">
                    <option value="0">-Choose Template -</option>
                    <option v-for="row in templates" v-bind:key="row.id" :value="row.id">{{ row.name }}</option>
                  </select>
                  <div v-if="$page.props.errors.template" class="text-red-500">{{ $page.props.errors.template }}</div>
                </div>
              </div>
            </div>
            <div class="col-span-1 sm:col-span-1">
              <div class="py-2">
                <div class="flex justify-between">
                  <label for="sh_package" class="inline-flex text-sm font-medium text-gray-700">Package </label>
                  <label class="inline-flex text-sm font-medium text-gray-700">
                    <input
                      class="text-indigo-500 w-5 h-5 mr-2 focus:ring-indigo-400 focus:ring-opacity-25 border border-gray-300 rounded"
                      type="checkbox" v-model="createForm.package_except" />
                    Except
                  </label>
                </div>
                <div class="mt-1 flex rounded-md shadow-sm" v-if="package_speed.length !== 0">
                  <multiselect deselect-label="Selected already" :options="package_speed" track-by="speed"
                    label="item_data" v-model="createForm.package" :allow-empty="true" :multiple="true" tabindex="2">
                  </multiselect>
                  <div v-if="$page.props.errors.package" class="text-red-500">{{ $page.props.errors.package }}</div>
                </div>
              </div>
              <div class="py-2">
                <label for="sh_type" class="block text-sm font-medium text-gray-700">Type </label>
                <div class="mt-1 flex rounded-md shadow-sm">
                  <input type="text" v-model="createForm.type" name="sh_type" id="sh_type"
                    class="py-2.5 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300 uppercase"
                    tabindex="6" disabled />
                </div>
                <div v-if="$page.props.errors.type" class="text-red-500">{{ $page.props.errors.type }}</div>
              </div>
            </div>
            <div class="col-span-1 sm:col-span-1">
              <div class="py-2">
                <label for="sh_township" class="block text-sm font-medium text-gray-700">Township </label>
                <div class="mt-1 flex rounded-md shadow-sm">
                  <span
                    class="z-10 leading-snug font-normal absolute text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                    <i class="fas fa-user"></i>
                  </span>
                  <select id="sh_township" v-model="createForm.township" name="sh_township"
                    class="pl-10 py-2.5 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    tabindex="3">
                    <option value="0">-Choose Township -</option>
                    <option v-for="row in townships" v-bind:key="row.id" :value="row.id">{{ row.name }}</option>
                  </select>
                  <div v-if="$page.props.errors.township" class="text-red-500">{{ $page.props.errors.township }}</div>
                </div>
              </div>
              <div class="py-2">
                <div class="flex justify-between">
                  <label for="sh_status" class="block text-sm font-medium text-gray-700">Customer Status </label>
                  <label class="inline-flex text-sm font-medium text-gray-700">
                    <input
                      class="text-indigo-500 w-5 h-5 mr-2 focus:ring-indigo-400 focus:ring-opacity-25 border border-gray-300 rounded"
                      type="checkbox" v-model="createForm.status_except" />
                    Except
                  </label>
                </div>
                <div class="mt-1 flex rounded-md shadow-sm" v-if="status.length !== 0">
                  <multiselect deselect-label="Selected already" :options="status" track-by="id" label="name"
                    v-model="createForm.status" :allow-empty="true" :multiple="true"> </multiselect>
                  <div v-if="$page.props.errors.status" class="text-red-500">{{ $page.props.errors.status }}</div>
                </div>
              </div>
            </div>
            <div class="col-span-1 sm:col-span-1">
              <div class="py-2">
                <label for="sh_partner" class="block text-sm font-medium text-gray-700">Project </label>
                <div class="mt-1 flex rounded-md shadow-sm">
                  <span
                    class="z-10 leading-snug font-normal absolute text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                    <i class="fas fa-user"></i>
                  </span>
                  <select id="sh_partner" v-model="createForm.partner" name="sh_partner"
                    class="pl-10 py-2.5 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    tabindex="4">
                    <option value="0">-Choose Project/Partner -</option>
                    <option v-for="row in projects" v-bind:key="row.id" :value="row.id">{{ row.name }}</option>
                  </select>
                  <div v-if="$page.props.errors.partner" class="text-red-500">{{ $page.props.errors.partner }}</div>
                </div>
              </div>
              
            </div>
          </div>
        </div>

        <div class="mb-2 py-2 px-2 md:px-2 bg-white shadow rounded-b-lg flex justify-between">
          <div class="flex">
            <a @click="updateForm"
              class="cursor-pointer inline-flex items-center px-4 py-2 bg-indigo-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring focus:ring-indigo-300 disabled:opacity-25 transition">1
              Update <i class="ml-1 fa fa-save text-white" tabindex="10"></i></a>
            <a @click="doSearch"
              class="ml-2 cursor-pointer inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">2
              Preview <i class="ml-1 fa fa-search text-white" tabindex="11"></i></a>
          </div>

          <div class="flex">
            <a @click="sendAnnouncement"
              class="cursor-pointer inline-flex items-center px-4 py-2 bg-red-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring focus:ring-red-300 disabled:opacity-25 transition">3
              Send Announcement<i class="ml-1 -mt-1 fa fa-bullhorn text-white" tabindex="12"></i></a>
          </div>
        </div>

        <!-- End of Advance Search -->

        <!-- <div v-if="search"> -->
        <div>
          <div class="bg-white overflow-auto shadow-xl sm:rounded-lg" v-if="customers.data">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Customer ID</th>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Order Date</th>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Name</th>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Package</th>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Township</th>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Status</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200 text-sm">
                <tr v-for="row in customers.data" v-bind:key="row.id" :class="' text-' + row.color">
                  <td class="px-6 py-3 text-xs font-medium whitespace-nowrap">{{ row.ftth_id }}</td>
                  <td class="px-6 py-3 text-xs font-medium whitespace-nowrap">{{ row.order_date }}</td>
                  <td class="px-6 py-3 text-xs font-medium whitespace-nowrap">{{ row.name }}</td>
                  <td class="px-6 py-3 text-xs font-medium whitespace-nowrap">{{ row.package }}</td>
                  <td class="px-6 py-3 text-xs font-medium whitespace-nowrap">{{ row.township }}</td>
                  <td class="px-6 py-3 text-xs font-medium whitespace-nowrap">{{ row.status }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <span v-if="customers.total" class="w-full block mt-4">
            <label class="text-xs text-gray-600">{{ customers.data.length }} Customers in Current Page. Total Number of
              Customers : {{ customers.total }}</label>
          </span>

          <span v-if="customers.links">
            <pagination class="mt-4" :links="customers.links" />
          </span>
        </div>
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
import { onMounted, onUpdated, provide, ref } from "vue";
import { router } from '@inertiajs/vue3';
import Multiselect from "@suadelabs/vue3-multiselect";

import SmsGenerationProgress from "@/Components/SmsGenerationProgress";
export default {
  name: "Announcement",
  components: {
    AppLayout,
    Multiselect,
    Pagination,
    SmsGenerationProgress,
  },
  props: {
    announcement: Object,
    templates: Object,
    customers: Object,
    packages: Object,
    projects: Object,
    townships: Object,
    package_speed: Object,
    status: Object,
    errors: Object,
  },
  setup(props) {

    const search = ref(false);
    const sort = ref("");
    const loading = ref(false);
    const announcementsms = ref(0);
    const showSmsProgress = ref(false);
    const createForm = useForm({
      id: null,
      name: null,
      general: null,
      template: 0,
      package_except: null,
      package: null,
      type: null,
      township: 0,
      status_except: null,
      status: null,
      partner: 0,
      payment: 0,
      deposit_status: 0,
    });
    function resetCreateForm() {
      createForm.id = null;
      createForm.name = null;
      createForm.general = null;
      createForm.template = 0;
      createForm.package_except = null;
      createForm.package = null;
      createForm.type = null;
      createForm.township = 0;
      createForm.status_except = null;
      createForm.status = null;
      createForm.partner = 0;
      createForm.payment = 0;
      createForm.deposit_status = 0;
    }
    function formFill(data) {
      createForm.id = data.id;
      createForm.name = data.name;
      createForm.general = data.general;
      createForm.template = data.template_id;
      createForm.package_except = data.packages_invert ? true : false;

      if (data.packages) {
        createForm.package = JSON.parse(data.packages);

        // if (data.packages.indexOf(",") !== -1) {
        //   let package_id_array = data.packages.split(",");
        //   createForm.package = props.packages.filter((d) => package_id_array.includes("" + d.id));
        // } else {
        //   createForm.package = props.packages.filter((e) => "" + e.id == data.packages)[0];
        // }
      }

      if (data.customer_status) {
        if (data.customer_status.indexOf(",") !== -1) {
          let status_id_array = data.customer_status.split(",");
          createForm.status = props.status.filter((d) => status_id_array.includes("" + d.id));
        } else {
          createForm.status = props.status.filter((e) => "" + e.id == data.customer_status);
        }
      }
      createForm.township = data.townships;
      createForm.status_except = data.customer_status_invert ? true : false;

      createForm.partner = data.projects;
      createForm.payment = data.payment_type;
      createForm.deposit_status = data.deposit_status;
      if (createForm.template != 0) {
        let template_type = props.templates.filter((d) => d.id == parseInt(createForm.template))[0];
        createForm.type = template_type.type;
      }
    }
    function template_change() {
      if (createForm.template != 0) {
        let template_type = props.templates.filter((d) => d.id == createForm.template)[0];
        createForm.type = template_type.type;
      }
    }

    const updateForm = () => {
      createForm.post("/announcement/detail/" + createForm.id + "/update", {
        onSuccess: (page) => {
          search.value = false;
          Toast.fire({
            icon: "success",
            title: page.props.flash.message,
          });
        },
        onError: (errors) => {
          console.log(errors);
        },
      });
    };
    const doSearch = () => {
      createForm.post("/announcement/detail/" + createForm.id, {
        onSuccess: (page) => {
          search.value = true;
        },
        onError: (errors) => {
          search.value = false;
          console.log(errors);
        },
      });
    };
    const sendAnnouncement = () => {
      createForm.post("/announcement/detail/" + createForm.id + "/send", {
        onSuccess: (page) => {
          search.value = false;
          loading.value = false;
          Toast.fire({
            icon: "success",
            title: page.props.flash.message,
          });
        },
        onError: (errors) => {
          loading.value = false;
          console.log(errors);
        },
        onStart: (pending) => {

          loading.value = true;
        },
      });
    };
    const deleteRow = (data) => {
      if (!confirm("Are you sure want to Delete?")) return;
      data._method = "DELETE";
      router.post("/announcement/" + data.id, data);
    };
    const dismiss = (e) => {
      if (e) {
        showSmsProgress.value = !e;
      };
    }
    onMounted(() => {
     
      props.packages.map(function (x) {
        return (x.item_data = `${x.speed}Mbps - ${x.name} - ${x.contract_period} Months`);
      });
      props.package_speed.map(function (x) {
        return (x.item_data = `${x.speed} Mbps - ${x.type.toUpperCase()}`);
      });
      if (props.announcement) {
        formFill(props.announcement);
      }
    });
    onUpdated(() => {
      props.packages.map(function (x) {
        return (x.item_data = `${x.speed}Mbps - ${x.name} - ${x.contract_period} Months`);
      });
      props.package_speed.map(function (x) {
        return (x.item_data = `${x.speed} Mbps - ${x.type.toUpperCase()}`);
      });
      if (props.announcement) {
        formFill(props.announcement);
      }
    });
    return { deleteRow, doSearch, template_change, sort, search, createForm, loading, updateForm, resetCreateForm, formFill, sendAnnouncement, dismiss, announcementsms, showSmsProgress };
  },
};
</script>
