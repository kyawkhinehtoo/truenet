<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">Customer Service Request</h2>
    </template>

    <div class="py-2">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between space-x-2 items-end mb-2 px-1 md:px-0">
          <div class="relative flex flex-wrap">
            <span
              class="z-10 h-full leading-snug font-normal absolute text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-3"><i
                class="fas fa-search"></i></span>
            <input type="text" placeholder="Search here..."
              class="border-0 px-3 py-3 placeholder-gray-300 text-gray-600 relative bg-white bg-white rounded text-sm shadow outline-none focus:outline-none focus:ring w-full pl-10"
              id="search_txt" v-model="search_txt" v-on:keyup.enter="search" />
          </div>
          <div class="flex">
            <button :class="{ 'bg-gray-600': status == 'active' }"
              class="inline-flex btn py-3 px-8 bg-gray-400 rounded rounded-r-none focus:ring-0 focus:border-transparent focus:outline-none focus:shadow-inner  text-gray-50 text-xs font-semibold uppercase"
              @click="goActive">Active</button>
            <button :class="{ 'bg-gray-600': status == 'close' }"
              class="inline-flex  btn py-3 px-8 bg-gray-400 rounded rounded-l-none focus:ring-0 focus:border-transparent focus:outline-none focus:shadow-inner text-gray-50 text-xs font-semibold uppercase"
              @click="goClose">Closed</button>
          </div>
        </div>
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg" v-if="incidents.data">
          <!-- <button @click="openModal" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded my-3">Create New Township</button>
                 <input class="w-half rounded py-2 my-3 float-right" type="text" placeholder="Search Township" v-model="search" v-on:keyup.enter="searchTsp">
                    -->

          <table class="min-w-full divide-y divide-gray-200 text-xs">
            <thead class="bg-gray-100">
              <tr>
                <th scope="col"
                  class="px-6 py-3 cursor-pointer text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  @click="sortBy('id')">No.<span v-if="sort == 'id'"><i class="fa fas fa-sort-up"
                      v-if="order == 'asc'"></i><i class="fa fas fa-sort-down" v-else></i> </span> </th>
                <th scope="col"
                  class="px-6 py-3 cursor-pointer text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  @click="sortBy('date')">Date <span v-if="sort == 'date'"><i class="fa fas fa-sort-up"
                      v-if="order == 'asc'"></i><i class="fa fas fa-sort-down" v-else></i> </span></th>
                <th scope="col"
                  class="px-6 py-3 cursor-pointer text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  @click="sortBy('cid')">Customer ID <span v-if="sort == 'cid'"><i class="fa fas fa-sort-up"
                      v-if="order == 'asc'"></i><i class="fa fas fa-sort-down" v-else></i> </span></th>
                <th scope="col"
                  class="px-6 py-3 cursor-pointer text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  @click="sortBy('id')">Incident ID <span v-if="sort == 'id'"><i class="fa fas fa-sort-up"
                      v-if="order == 'asc'"></i><i class="fa fas fa-sort-down" v-else></i> </span></th>
                <th scope="col"
                  class="px-6 py-3 cursor-pointer text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  @click="sortBy('type')">Request Type <span v-if="sort == 'type'"><i class="fa fas fa-sort-up"
                      v-if="order == 'asc'"></i><i class="fa fas fa-sort-down" v-else></i> </span></th>
                <th scope="col"
                  class="px-6 py-3 cursor-pointer text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  @click="sortBy('start')">Effective From <span v-if="sort == 'start'"><i class="fa fas fa-sort-up"
                      v-if="order == 'asc'"></i><i class="fa fas fa-sort-down" v-else></i> </span></th>
                <th scope="col"
                  class="px-6 py-3 cursor-pointer text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  @click="sortBy('request')">Requested By <span v-if="sort == 'request'"><i class="fa fas fa-sort-up"
                      v-if="order == 'asc'"></i><i class="fa fas fa-sort-down" v-else></i> </span></th>
                <th scope="col"
                  class="px-6 py-3 cursor-pointer text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  @click="sortBy('status')">Status <span v-if="sort == 'status'"><i class="fa fas fa-sort-up"
                      v-if="order == 'asc'"></i><i class="fa fas fa-sort-down" v-else></i> </span></th>
                <th scope="col" class="relative px-6 py-3"><span class="sr-only">Action</span></th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="(row, index) in incidents.data" v-bind:key="row.id">
                <td class="px-6 py-3 whitespace-nowrap">{{ (index += incidents.from) }}</td>
                <td class="px-6 py-3 whitespace-nowrap">{{ row.date }}</td>
                <td class="px-6 py-3 whitespace-nowrap">{{ row.ftth_id }}</td>
                <td class="px-6 py-3 whitespace-nowrap">{{ row.code }}</td>
                <td class="px-6 py-3 whitespace-nowrap capitalize">{{ row.type.replace(/_/g, " ") }}</td>
                <td class="px-6 py-3 whitespace-nowrap">{{ row.start_date }}</td>
                <td class="px-6 py-3 whitespace-nowrap">{{ row.incharge }}</td>
                <td class="px-6 py-3 whitespace-nowrap">{{ getStatus(row.status) }}</td>
                <td class="px-6 py-3 whitespace-nowrap text-right text-sm font-medium">
                  <a href="#" @click="edit(row)" class="text-indigo-600 hover:text-indigo-900">Detail</a>
                </td>
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
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full"
                role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                <form @submit.prevent="submit">
                  <div class="py-2 py-2 border-b border-gray-100">
                    <div class="mt-1 px-3 flex w-full justify-between">
                      <div class="flex">
                        <h2 class="font-semibold text-md text-gray-800 leading-tight capitalize">Request for :
                          {{ form.type }}
                        </h2>
                      </div>
                      <div class="flex"><label class="font-semibold text-sm text-gray-800 leading-tight"> Date :
                          {{ form.date }} </label> </div>
                    </div>
                  </div>
                  <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="grid grid-cols-5 gap-2">
                      <!-- form.id =  data.id;
            form.code =  data.code;
            form.ftth_id =  data.ftth_id;
            form.date =  data.date;
            form.start_date = data.start_date;
            form.end_date = data.end_date;
            form.type = data.type;
            form.request_by = data.request_by;
            form.status = data.status;
            form.current_package = data.current_package;
            form.current_address = data.current_address;
            form.new_address = data.new_address;
            form.current_location = data.current_location;
            form.new_location = data.new_location;
            form.current_township = data.current_township;
            form.new_township = data.new_township;
            form.description = data.description; -->
                      <!-- general -->
                      <div class="mb-4 col-span-1 sm:col-span-1">
                        <label for="code" class="block text-gray-700 text-sm font-bold mt-3 mr-2">Ticket ID:</label>
                      </div>
                      <div class="mb-4 col-span-4 sm:col-span-4">
                        <input type="text"
                          class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                          id="code" v-model="form.code" :readonly="true" />
                      </div>
                      <div class="mb-4 col-span-1 sm:col-span-1">
                        <label for="request_by" class="block text-gray-700 text-sm font-bold mt-3 mr-2">Requested
                          By:</label>
                      </div>
                      <div class="mb-4 col-span-4 sm:col-span-4">

                        <input type="text"
                          class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                          id="request_by" :value='`${form.request_by.name}`' :readonly="true" />
                      </div>
                      <div class="mb-4 col-span-1 sm:col-span-1">
                        <label for="ftth_id" class="block text-gray-700 text-sm font-bold mt-3 mr-2">Customer
                          ID:</label>
                      </div>
                      <div class="mb-4 col-span-4 sm:col-span-4">
                        <input type="text"
                          class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                          id="ftth_id" v-model="form.ftth_id" :readonly="true" />
                      </div>
                      <div class="mb-4 col-span-1 sm:col-span-1">
                        <label for="status" class="block text-gray-700 text-sm font-bold mt-3 mr-2">Customer
                          Status:</label>
                      </div>
                      <div class="mb-4 col-span-4 sm:col-span-4">
                        <input type="text"
                          class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                          id="status" :value='`${form.status}`' :readonly="true" />
                      </div>
                      <!-- end of general -->

                      <div class="mb-4 col-span-1 sm:col-span-1" v-if="form.current_package">
                        <label for="current_package" class="block text-gray-700 text-sm font-bold mt-3 mr-2">Current
                          Plan:</label>
                      </div>
                      <div class="mb-4 col-span-4 sm:col-span-4" v-if="form.current_package">

                        <input type="text" :class="{ 'ring-1 ring-blue-200': form.new_package }"
                          class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                          id="current_package" :value='`${form.current_package.item_data}`' :readonly="true" />
                      </div>



                      <div class="mb-4 col-span-1 sm:col-span-1" v-if="form.new_package">
                        <label for="new_package" class="block text-gray-700 text-sm font-bold mt-3 mr-2">New
                          Plan:</label>
                      </div>
                      <div class="mb-4 col-span-4 sm:col-span-4" v-if="form.new_package">

                        <input type="text"
                          class="mt-1 ring-1 ring-green-200  focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                          id="new_package" :value='`${form.new_package.item_data}`' :readonly="true" />
                      </div>

                      <div class="mb-4 col-span-1 sm:col-span-1" v-if="form.new_address">
                        <label for="current_address" class="block text-gray-700 text-sm font-bold mt-3 mr-2">Current
                          Address:</label>
                      </div>
                      <div class="mb-4 col-span-4 sm:col-span-4" v-if="form.new_address">

                        <textarea
                          class="mt-1 ring-1 ring-blue-200  focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                          id="current_address" :value='`${form.current_address}`' :readonly="true" />
                      </div>
                      <div class="mb-4 col-span-1 sm:col-span-1" v-if="form.new_address">
                        <label for="current_township" class="block text-gray-700 text-sm font-bold mt-3 mr-2">Current
                          Township:</label>
                      </div>
                      <div class="mb-4 col-span-4 sm:col-span-4" v-if="form.new_address">

                        <input type="text"
                          class="mt-1 ring-1 ring-blue-200  focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                          id="current_township" :value='`${form.current_township.name}`' :readonly="true" />
                      </div>

                      <div class="mb-4 col-span-1 sm:col-span-1" v-if="form.new_address">
                        <label for="new_address" class="block text-gray-700 text-sm font-bold mt-3 mr-2">New
                          Address:</label>
                      </div>
                      <div class="mb-4 col-span-4 sm:col-span-4" v-if="form.new_address">

                        <textarea
                          class="mt-1 ring-1 ring-green-200  focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                          id="new_address" :value='`${form.new_address}`' :readonly="true" />
                      </div>
                      <div class="mb-4 col-span-1 sm:col-span-1" v-if="form.new_address">
                        <label for="new_location" class="block text-gray-700 text-sm font-bold mt-3 mr-2">New Lat
                          Long:</label>
                      </div>
                      <div class="mb-4 col-span-4 sm:col-span-4" v-if="form.new_address">
                        <input type="text"
                          class="mt-1 ring-1 ring-green-200  focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                          id="new_location" :value='`${form.new_location}`' :readonly="true" />
                      </div>
                      <div class="mb-4 col-span-1 sm:col-span-1" v-if="form.new_address">
                        <label for="new_township" class="block text-gray-700 text-sm font-bold mt-3 mr-2">New
                          Township:</label>
                      </div>
                      <div class="mb-4 col-span-4 sm:col-span-4" v-if="form.new_address">

                        <input type="text"
                          class="mt-1 ring-1 ring-green-200  focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                          id="new_township" :value='`${form.new_township.name}`' :readonly="true" />
                      </div>
                      <!-- Effective Date From / To -->
                      <div class="mb-4 col-span-1 sm:col-span-1" v-if="form.start_date">
                        <label for="start_date" class="block text-gray-700 text-sm font-bold mt-3 mr-2">Effective From
                          :</label>
                      </div>
                      <div class="mb-4 col-span-4 sm:col-span-4" v-if="form.start_date">
                        <input type="text"
                          class="mt-1 ring-1 ring-green-200 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                          id="start_date" v-model="form.start_date" :readonly="true" />
                      </div>
                      <div class="mb-4 col-span-1 sm:col-span-1" v-if="form.end_date">
                        <label for="end_date" class="block text-gray-700 text-sm font-bold mt-3 mr-2">Effective To
                          :</label>
                      </div>
                      <div class="mb-4 col-span-4 sm:col-span-4" v-if="form.end_date">
                        <input type="text"
                          class="mt-1 ring-1 ring-green-200  focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                          id="end_date" v-model="form.end_date" :readonly="true" />
                      </div>

                      <!-- End of Effective Date From / To -->
                      <div class="mb-4 col-span-1 sm:col-span-1" v-if="form.description">
                        <label for="description"
                          class="block text-gray-700 text-sm font-bold mt-3 mr-2">Description:</label>
                      </div>
                      <div class="mb-4 col-span-4 sm:col-span-4" v-if="form.description">

                        <textarea
                          class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                          id="description" :value='`${form.description}`' :readonly="true" />
                      </div>
                    </div>
                  </div>
                  <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto" v-if="status == 'active'">
                      <button wire:click.prevent="submit" type="submit"
                        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">Process</button>
                    </span>

                    <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
                      <button @click="closeModal" type="button"
                        class="inline-flex justify-center w-full rounded-md border border-gray-300 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5">Close</button>
                    </span>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
        <span v-if="incidents.links">
          <pagination class="mt-6" :links="incidents.links" />
        </span>
      </div>
    </div>
    <div v-if="loading" wire:loading
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
import { onMounted, reactive, ref, onUpdated } from "vue";
import { router } from '@inertiajs/vue3';
export default {
  name: "ServiceRequest",
  components: {
    AppLayout,
    Pagination
  },
  props: {
    incidents: Object,
    townships: Object,
    packages: Object,
    users: Object,
    errors: Object,
  },
  setup(props) {
    const form = reactive({
      id: null,
      code: null,
      customer_id: null,
      ftth_id: null,
      date: null,
      start_date: null,
      end_date: null,
      type: null,
      request_by: null,
      status: null,
      status_id: null,
      current_package: null,
      new_package: null,
      current_address: null,
      new_address: null,
      current_location: null,
      new_location: null,
      current_township: null,
      new_township: null,
      description: null,
    });
    const search_txt = ref("");
    const sort = ref("");
    const order = ref("desc");
    const status = ref("active");
    let editMode = ref(false);
    let isOpen = ref(false);
    let loading = ref(false);
    function resetForm() {
      form.id = null;
      form.code = null;
      form.customer_id = null;
      form.ftth_id = null;
      form.date = null;
      form.start_date = null;
      form.end_date = null;
      form.type = null;
      form.request_by = null;
      form.status = null;
      form.status_id = null;
      form.current_package = null;
      form.new_package = null;
      form.current_address = null;
      form.new_address = null;
      form.current_location = null;
      form.new_location = null;
      form.current_township = null;
      form.new_township = null;
      form.description = null;
    }
    function getStatus(data) {
      let status = "WIP";
      if (data == 1) {
        status = "WIP";
      } else if (data == 2) {
        status = "Escalated";
      } else if (data == 3) {
        status = "Closed";
      } else if (data == 4) {
        status = "Deleted";
      }
      return status;
    }

    function submit() {

      form._method = "PUT";
      router.post("/servicerequest/" + form.id, form, {
        onSuccess: (page) => {
          closeModal();
          resetForm();
          Toast.fire({
            icon: "success",
            title: page.props.flash.message,
          });
        },

        onError: (errors) => {
          closeModal();
          console.log("error ..".errors);
        },
      });

    }
    function edit(data) {
      form.id = data.id;
      form.code = data.code;
      form.customer_id = data.customer_id;
      form.ftth_id = data.ftth_id;
      form.date = data.date;
      form.start_date = data.start_date;
      form.end_date = data.end_date;
      form.type = data.type.replace(/_/g, " ");
      form.request_by = props.users.filter((d) => d.id == data.incharge_id)[0];
      form.status = data.status_name;
      form.status_id = data.status_id;
      form.current_package = props.packages.filter((d) => d.id == data.current_package)[0];
      form.new_package = props.packages.filter((d) => d.id == data.new_package)[0];
      form.current_address = data.current_address;
      form.new_address = data.new_address;
      form.current_location = data.current_location;
      form.new_location = data.location;
      form.current_township = props.townships.filter((d) => d.id == data.current_township)[0];
      form.new_township = props.townships.filter((d) => d.id == data.new_township)[0];
      form.description = data.description;
      editMode.value = true;
      openModal();
    }

    function deleteRow(data) {
      if (!confirm("Are you sure want to remove?")) return;
      data._method = "DELETE";
      router.post("/equiptment/" + data.id, data);
      closeModal();
      resetForm();
    }
    function openModal() {
      isOpen.value = true;
    }
    const closeModal = () => {
      isOpen.value = false;
      resetForm();
      editMode.value = false;
    };
    const goActive = () => {
      status.value = 'active';
      search();
    }
    const goClose = () => {
      status.value = 'close';
      search();
    }
    const search = () => {

      router.get("/servicerequest/",
        { status: status.value, sort: sort.value, order: order.value, general: search_txt.value },
        {
          preserveState: true,
          onSuccess: (page) => {
            loading.value = false;
          },
          onError: (errors) => {
            loading.value = false;
          },
          onStart: (pending) => {
            loading.value = true;
          }
        });
    };
    const sortBy = (d) => {
      sort.value = d;

      if (order.value == 'asc') {
        order.value = 'desc';
      } else {
        order.value = 'asc';
      }
      console.log("search value is" + sort.value);
      router.get('/servicerequest/',
        { status: status.value, sort: sort.value, order: order.value, general: search_txt.value },
        {
          preserveState: true,
          onSuccess: (page) => {
            loading.value = false;
          },
          onError: (errors) => {
            loading.value = false;
          },
          onStart: (pending) => {
            loading.value = true;
          }
        });
    };
    onMounted(() => {
      props.packages.map(function (x) {
        return (x.item_data = `${x.speed} Mbps - ${x.name} - ${x.contract_period} Months Contract`);
      });

    });
    onUpdated(() => {
      props.packages.map(function (x) {
        return (x.item_data = `${x.speed} Mbps - ${x.name} - ${x.contract_period} Months Contract`);
      });

    });
    return { form, submit, editMode, isOpen, goActive, goClose, openModal, closeModal, edit, deleteRow, search, search_txt, getStatus, sortBy, sort, order, status, loading };
  },
};
</script>
