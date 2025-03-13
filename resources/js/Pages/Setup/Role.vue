<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">Role Setup</h2>
    </template>

    <div class="py-2">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between space-x-2 items-end mb-2 px-1 md:px-0">
          <div class="relative flex flex-wrap z-0">
            <span
              class="z-10 h-full leading-snug font-normal  text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-3"><i
                class="fas fa-search"></i></span>
            <input type="text" placeholder="Search here..."
              class="border-0 px-3 py-3 placeholder-gray-300 text-gray-600 relative bg-white rounded text-sm shadow outline-none focus:outline-none focus:ring w-full pl-10"
              id="search" v-model="search" v-on:keyup.enter="searchTsp" />
          </div>
          <button @click="openModal"
            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">Create</button>
        </div>
        <div class="bg-white  shadow-xl sm:rounded-lg" v-if="roles.data">
          <!-- <button @click="openModal" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded my-3">Create New Township</button>
                 <input class="w-half rounded py-2 my-3 float-right" type="text" placeholder="Search Township" v-model="search" v-on:keyup.enter="searchTsp">
                    -->

          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  No.
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Name</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Permission</th>
                <th scope="col" class="relative px-6 py-3"><span class="sr-only">Action</span></th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="row in roles.data" v-bind:key="row.id">
                <td class="px-6 py-3 whitespace-nowrap">{{ row.id }}</td>
                <td class="px-6 py-3 whitespace-nowrap">{{ row.name }}</td>
                <td class="px-6 py-3 whitespace-nowrap">
                  <div v-html="getPerm(row.permission)"></div>
                </td>
                <td class="px-6 py-3 whitespace-nowrap text-right text-sm font-medium">
                  <a href="#" @click="edit(row)" class="text-indigo-600 hover:text-indigo-900">Edit</a> |
                  <a href="#" @click="deleteRow(row)" class="text-red-600 hover:text-red-900">Delete</a>
                </td>
              </tr>
            </tbody>
          </table>

          <div ref="isOpen" class="fixed z-10 inset-0 overflow-y-auto ease-out duration-400" v-if="isOpen">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
              <div class="fixed inset-0 transition-opacity">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
              </div>
              ​
              <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-5xl sm:w-full"
                role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                <form @submit.prevent="submit">
                  <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 min-h-screen">
                    <div class="">
                      <div class="mb-4">
                        <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Status :</label>
                        <input type="text"
                          class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                          id="name" placeholder="Enter Role Name" v-model="form.name" />
                        <div v-if="$page.props.errors.name" class="text-red-500">{{ $page.props.errors.name[0] }}</div>
                      </div>

                      <div class="mb-4">
                        <fieldset class="mt-4 border border-solid border-gray-300 p-3 rounded-md">
                          <legend class="text-gray-700 text-sm font-bold">Customer Data </legend>
                          <label class="inline-flex ml-2 text-sm">
                            <input
                              class="text-gray-500 text-sm w-6 h-6 mr-2 focus:ring-gray-400 focus:ring-opacity-25 border border-gray-300 rounded"
                              type="checkbox" v-model="form.read_customer" />
                            Read Only to Customer Data
                          </label>
                          <label class="inline-flex ml-2 text-sm">
                            <input
                              class="text-gray-500 text-sm w-6 h-6 mr-2 focus:ring-gray-400 focus:ring-opacity-25 border border-gray-300 rounded"
                              type="checkbox" v-model="form.delete_customer" />
                            Enable Delete Customer
                          </label>
                          <label class="inline-flex ml-2 text-sm">
                            <input
                              class="text-gray-500 text-sm w-6 h-6 mr-2 focus:ring-gray-400 focus:ring-opacity-25 border border-gray-300 rounded"
                              type="checkbox" v-model="form.enable_customer_export" />
                            Enable Export Data
                          </label>
                          <label class="inline-flex ml-2 text-sm">
                            <input
                              class="text-gray-500 text-sm w-6 h-6 mr-2 focus:ring-gray-400 focus:ring-opacity-25 border border-gray-300 rounded"
                              type="checkbox" v-model="form.activity_log" />
                            Enable Activity Log Access
                          </label>
                          <label class="inline-flex ml-2 text-sm">
                            <input
                              class="text-gray-500 text-sm w-6 h-6 mr-2 focus:ring-gray-400 focus:ring-opacity-25 border border-gray-300 rounded"
                              type="checkbox" v-model="form.system_setting" />
                           System Setting
                          </label>
                          <label for="permission" class="block text-gray-700 text-sm font-bold mb-2">Permission
                            :</label>
                          <!-- <select multiple>
                          <option v-for="row in col" v-bind:key="row.id" class="capitalize"> {{ row.name.replace(/_/g, " ") }}</option>
                        </select> -->
                          <div class="mt-1 flex rounded-md shadow-sm" v-if="col.length !== 0">
                            <multiselect deselect-label="Selected already" :options="col" track-by="id" label="name"
                              v-model="form.permission" :allow-empty="true" :multiple="true" :taggable="true">
                            </multiselect>
                          </div>
                        </fieldset>
                      </div>
                      <div class="mb-4">
                        <fieldset class="mt-4 border border-solid border-gray-300 p-3 rounded-md">
                          <legend class="text-gray-700 text-sm font-bold">Incident Control </legend>

                          <div class="max-w-full text-sm flex">

                            <label class="inline-flex ml-2">
                              <input
                                class="text-indigo-500 w-6 h-6 mr-2 focus:ring-indigo-400 focus:ring-opacity-25 border border-gray-300 rounded"
                                type="checkbox" v-model="form.read_incident" />
                              Incident Read Permission
                            </label>
                            <label class="inline-flex ml-2">
                              <input
                                class="text-indigo-500 w-6 h-6 mr-2 focus:ring-indigo-400 focus:ring-opacity-25 border border-gray-300 rounded"
                                type="checkbox" v-model="form.write_incident" />
                              Incident Write Permission
                            </label>
                            <label class="inline-flex ml-2">
                              <input
                                class="text-indigo-500 w-6 h-6 mr-2 focus:ring-indigo-400 focus:ring-opacity-25 border border-gray-300 rounded"
                                type="checkbox" v-model="form.incident_only" />
                              Incident Only Access Permission
                            </label>


                          </div>
                        </fieldset>
                      </div>
                      <div class="mb-4">
                        <fieldset class="mt-4 border border-solid border-gray-300 p-3 rounded-md">
                          <legend class="text-gray-700 text-sm font-bold">Billing Control </legend>

                          <div class="max-w-full text-sm flex">
                            <label class="inline-flex ml-2">
                              <input
                                class="text-red-500 w-6 h-6 mr-2 focus:ring-red-400 focus:ring-opacity-25 border border-gray-300 rounded"
                                type="checkbox" v-model="form.bill_generation" />
                              Bill Generate
                            </label>
                            <label class="inline-flex ml-2">
                              <input
                                class="text-red-500 w-6 h-6 mr-2 focus:ring-red-400 focus:ring-opacity-25 border border-gray-300 rounded"
                                type="checkbox" v-model="form.bill_receipt" />
                              Bill Receipt
                            </label>
                            <label class="inline-flex ml-2">
                              <input
                                class="text-red-500 w-6 h-6 mr-2 focus:ring-red-400 focus:ring-opacity-25 border border-gray-300 rounded"
                                type="checkbox" v-model="form.edit_invoice" />
                              Edit Invoice Permission
                            </label>
                            <label class="inline-flex ml-2">
                              <input
                                class="text-red-500 w-6 h-6 mr-2 focus:ring-red-400 focus:ring-opacity-25 border border-gray-300 rounded"
                                type="checkbox" v-model="form.delete_invoice" />
                              Delete Invoice Permission
                            </label>

                          </div>
                        </fieldset>
                      </div>
                      <div class="mb-4">
                        <fieldset class="mt-4 border border-solid border-gray-300 p-3 rounded-md">
                          <legend class="text-gray-700 text-sm font-bold">Radius Control </legend>

                          <div class="max-w-full text-sm flex">
                            <label class="inline-flex ml-2">
                              <input
                                class="text-red-500 w-6 h-6 mr-2 focus:ring-red-400 focus:ring-opacity-25 border border-gray-300 rounded"
                                type="checkbox" v-model="form.radius_read" />
                              Read Radius Information
                            </label>
                            <label class="inline-flex ml-2">
                              <input
                                class="text-red-500 w-6 h-6 mr-2 focus:ring-red-400 focus:ring-opacity-25 border border-gray-300 rounded"
                                type="checkbox" v-model="form.radius_write" />
                              Update Radius Information
                            </label>


                          </div>
                        </fieldset>
                      </div>
                      <div class="mb-4">
                        <fieldset class="mt-4 border border-solid border-gray-300 p-3 rounded-md">
                          <legend class="text-gray-700 text-sm font-bold">Public IP Control </legend>

                          <div class="max-w-full text-sm flex">
                            <label class="inline-flex ml-2">
                              <input
                                class="text-blue-500 w-6 h-6 mr-2 focus:ring-red-400 focus:ring-opacity-25 border border-gray-300 rounded"
                                type="checkbox" v-model="form.read_only_ip" />
                              Read-Only IP
                            </label>
                            <label class="inline-flex ml-2">
                              <input
                                class="text-blue-500 w-6 h-6 mr-2 focus:ring-red-400 focus:ring-opacity-25 border border-gray-300 rounded"
                                type="checkbox" v-model="form.add_ip" />
                              Add IP
                            </label>
                            <label class="inline-flex ml-2">
                              <input
                                class="text-blue-500 w-6 h-6 mr-2 focus:ring-red-400 focus:ring-opacity-25 border border-gray-300 rounded"
                                type="checkbox" v-model="form.edit_ip" />
                              Edit IP
                            </label>
                            <label class="inline-flex ml-2">
                              <input
                                class="text-blue-500 w-6 h-6 mr-2 focus:ring-red-400 focus:ring-opacity-25 border border-gray-300 rounded"
                                type="checkbox" v-model="form.delete_ip" />
                              Delete IP
                            </label>


                          </div>
                        </fieldset>
                      </div>
                      <div class="mb-4">
                        <fieldset class="mt-4 border border-solid border-gray-300 p-3 rounded-md">
                          <legend class="text-gray-700 text-sm font-bold">Report Control </legend>

                          <div class="max-w-full text-sm flex">
                            <label class="inline-flex ml-2">
                              <input
                                class="text-red-500 w-6 h-6 mr-2 focus:ring-red-400 focus:ring-opacity-25 border border-gray-300 rounded"
                                type="checkbox" v-model="form.incident_report" />
                              View Incident Report
                            </label>
                            <label class="inline-flex ml-2">
                              <input
                                class="text-red-500 w-6 h-6 mr-2 focus:ring-red-400 focus:ring-opacity-25 border border-gray-300 rounded"
                                type="checkbox" v-model="form.bill_report" />
                              View Bill Receipt Report
                            </label>
                            <label class="inline-flex ml-2">
                              <input
                                class="text-red-500 w-6 h-6 mr-2 focus:ring-red-400 focus:ring-opacity-25 border border-gray-300 rounded"
                                type="checkbox" v-model="form.radius_report" />
                              View Radius User Report
                            </label>
                            <label class="inline-flex ml-2">
                              <input
                                class="text-red-500 w-6 h-6 mr-2 focus:ring-red-400 focus:ring-opacity-25 border border-gray-300 rounded"
                                type="checkbox" v-model="form.ip_report" />
                              View IP Usage Report
                            </label>

                          </div>
                        </fieldset>
                      </div>
                    </div>
                  </div>
                  <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                      <button wire:click.prevent="submit" type="submit"
                        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"
                        v-show="!editMode">Save</button>
                    </span>
                    <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                      <button wire:click.prevent="submit" type="submit"
                        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"
                        v-show="editMode">Update</button>
                    </span>
                    <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
                      <button @click="closeModal" type="button"
                        class="inline-flex justify-center w-full rounded-md border border-gray-300 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5">Cancel</button>
                    </span>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
        <span v-if="roles.links">
          <pagination class="mt-6" :links="roles.links" />
        </span>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import Pagination from "@/Components/Pagination";
import Multiselect from "@suadelabs/vue3-multiselect";
import { reactive, ref, onMounted } from "vue";
import { router } from '@inertiajs/vue3';
export default {
  name: "Role",
  components: {
    AppLayout,
    Pagination,
    Multiselect,
  },
  props: {
    roles: Object,
    col: Object,
    menus: Object,
    errors: Object,
  },
  setup(props) {
    const form = reactive({
      id: null,
      name: null,
      permission: null,
      delete_customer: null,
      read_customer: null,
      read_incident: null,
      write_incident: null,
      edit_invoice: null,
      bill_generation: null,
      bill_receipt: null,
      radius_read: null,
      radius_write: null,
      incident_report: null,
      bill_report: null,
      radius_report: null,
      incident_only: null,
      read_only_ip: null,
      add_ip: null,
      edit_ip: null,
      delete_ip: null,
      ip_report: null,
      enable_customer_export: null,
      activity_log: null,
      system_setting: null,
    });
    const search = ref("");
    let editMode = ref(false);
    let isOpen = ref(false);

    function resetForm() {
      form.name = null;
      form.permission = null;
      form.read_customer = null;
      form.delete_customer = null;
      form.read_incident = null;
      form.write_incident = null;
      form.edit_invoice = null;
      form.delete_invoice = null;
      form.bill_generation = null;
      form.bill_receipt = null;
      form.radius_read = null;
      form.radius_write = null;
      form.incident_report = null;
      form.bill_report = null;
      form.radius_report = null;
      form.incident_only = null;
      form.read_only_ip = null;
      form.add_ip = null;
      form.edit_ip = null;
      form.delete_ip = null;
      form.ip_report = null;
      form.enable_customer_export = null;
      form.activity_log = null;
      form.system_setting = null;
    }
    function submit() {
      if (!editMode.value) {
        form._method = "POST";
        router.post("/role", form, {
          preserveState: true,
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
      } else {
        form._method = "PUT";
        router.post("/role/" + form.id, form, {
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
    }
    function edit(data) {
      form.id = data.id;
      form.name = data.name;
      if (data.permission) {
        let permission_array = data.permission.split(",");
        form.permission = props.col.filter((d) => permission_array.includes(d.name));
      }
      form.read_customer = (data.read_customer) ? true : false;
      form.delete_customer = (data.delete_customer) ? true : false;
      form.read_incident = (data.read_incident) ? true : false;
      form.write_incident = (data.write_incident) ? true : false;
      form.edit_invoice = (data.edit_invoice) ? true : false;
      form.delete_invoice = (data.delete_invoice) ? true : false;
      form.bill_generation = (data.bill_generation) ? true : false;
      form.bill_receipt = (data.bill_receipt) ? true : false;
      form.radius_read = (data.radius_read) ? true : false;
      form.radius_write = (data.radius_write) ? true : false;
      form.incident_report = (data.incident_report) ? true : false;
      form.bill_report = (data.bill_report) ? true : false;
      form.radius_report = (data.radius_report) ? true : false;
      form.incident_only = (data.incident_only) ? true : false;
      form.read_only_ip = (data.read_only_ip) ? true : false;
      form.add_ip = (data.add_ip) ? true : false;
      form.edit_ip = (data.edit_ip) ? true : false;
      form.delete_ip = (data.delete_ip) ? true : false;
      form.ip_report = (data.ip_report) ? true : false;
      form.enable_customer_export = (data.enable_customer_export) ? true : false;
      form.activity_log = (data.activity_log) ? true : false;
      form.system_setting = (data.system_setting) ? true : false;
      editMode.value = true;
      openModal();
    }

    function deleteRow(data) {
      if (!confirm("Are you sure want to remove?")) return;
      data._method = "DELETE";
      router.post("/role/" + data.id, data, {
        preserveState: true,
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
      closeModal();
      resetForm();
    }
    function openModal() {
      isOpen.value = true;
    }
    function getPerm(d) {
      let perm_array = "";
      let perm = "";
      let count = 0;
      if (d) {
        perm_array = d.split(",");
        perm_array.forEach((e) => {
          count++;
          if (count % 6 === 0) {
            perm += '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">' + e + "</span><br />";
          } else {
            perm += '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">' + e + "</span>";
          }
        });
      }
      return perm;
    }

    const closeModal = () => {
      isOpen.value = false;
      resetForm();
      editMode.value = false;
    };
    const searchTsp = () => {
      console.log("search value is" + search.value);
      router.get("/role/", { role: search.value }, { preserveState: true });
    };

    onMounted(() => {
      //   form.permission = props.col.filter((d) => d.name == 1)[0],
      props.col.map(function (x) {
        return (x.col_data = "<label :class='capitalize'>" + x.name + "</label>");
      });

    });
    return { form, submit, getPerm, editMode, isOpen, openModal, closeModal, edit, deleteRow, searchTsp, search };
  },
};
</script>
