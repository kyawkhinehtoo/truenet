<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">Bill Reminder</h2>
    </template>


    <div class="py-12">
      <BillReminderProgress :progress="smsprogress" @dismiss="dismiss" v-if="showSmsProgress" />
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow sm:rounded-lg py-4 px-4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-7 gap-4 items-end">
          <div class="col-span-1 sm:col-span-3">
            <label for="days" class="block text-sm font-medium text-gray-700">Days Before Service Expiry:</label>
            <div class="flex items-center space-x-2">
              <div class="flex-grow">
                <VueDatePicker v-model="expiration" :range="{ partialRange: false }" placeholder="Please choose Expiry Date" 
                  :enable-time-picker="false" model-type="yyyy-MM-dd" id="order_date"
                  class="w-full text-gray-700 text-sm focus:ring focus:ring-indigo-500 focus:border-indigo-500 focus:ring-opacity-10 focus:outline-none" />
              </div>
              <button @click="createList"
                class="inline-flex cursor-pointer justify-center py-2.5 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 items-center">
                Generate <i class="ml-1 fa fa-list text-white"></i>
              </button>
            </div>
          </div>

          <button @click="emptyList"
            class="col-span-1 inline-flex cursor-pointer justify-center py-2.5 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 items-center">
            Empty List<i class="ml-1 fa fa-trash text-white"></i>
          </button>

          <button @click="downloadExcel"
            class="col-span-1 inline-flex cursor-pointer justify-center py-2.5 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 items-center">
            Export <i class="ml-1 fa fa-download text-white"></i>
          </button>
          
          <a :href="route('bill-reminders.upload-view')" target="_blank"
            class="col-span-1 inline-flex cursor-pointer justify-center py-2.5 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 items-center">
            Import <i class="ml-1 fa fa-upload text-white"></i>
          </a>
          
          <button @click="sendAllReminder" v-if="smsgateway && smsgateway?.status == '1'"
            class="col-span-1 inline-flex cursor-pointer justify-center py-2.5 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 items-center">
            Send All <i class="ml-2 fa fa-sms fa-xl text-white"></i>
          </button>
        </div>
        <!-- Filter Form -->

        <div class="mt-2 bg-white  shadow-sm sm:rounded-lg p-4 z-20">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Filter Customers</h3>
          <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
            <div>
              <label for="filter_id" class="block text-sm font-medium text-gray-700">Customer ID</label>
              <input type="text" id="filter_id" v-model="filters.id"
                class="mt-1 py-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                placeholder="Search by ID">
            </div>
            <div>
              <label for="filter_name" class="block text-sm font-medium text-gray-700">Customer Name</label>
              <input type="text" id="filter_name" v-model="filters.name"
                class="mt-1 py-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                placeholder="Search by name">
            </div>
            <div>
              <label for="filter_phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
              <input type="text" id="filter_phone" v-model="filters.phone"
                class="mt-1 py-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                placeholder="Search by phone">
            </div>
            <div class="col-span-2">
              <label for="filter_expiry" class="block text-sm font-medium text-gray-700">Expiry Date</label>
            
                <VueDatePicker v-model="filters.expiry_date" :range="{ partialRange: false }" placeholder="Please choose Expiry Date" 
                :enable-time-picker="false" model-type="yyyy-MM-dd" id="filter_expiry"
                class="mt-1 w-full z-100 text-gray-700 text-sm focus:ring focus:ring-indigo-500 focus:border-indigo-500 focus:ring-opacity-10 focus:outline-none" />
              </div>
            <div class="flex items-center mt-6">
              <input type="checkbox" id="filter_no_phone" v-model="filters.no_phone"
                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
              <label for="filter_no_phone" class="ml-2 block text-sm text-gray-700">Show only customers without
                phone</label>
            </div>
          </div>
          <div class="mt-4 flex justify-end">
            <button @click="clearNoPhoneCustomers"
              class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
              Clear No Phone Customer
            </button>
            <button @click="applyFilters"
              class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
              Apply Filters
            </button>
            <button @click="resetFilters"
              class="ml-3 inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
              Reset
            </button>
          </div>
        </div>


        <div class="mt-2 bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 ">
          <!-- Success Message -->
          <div v-if="$page.props.flash.success" class="bg-green-50 border-l-4 border-green-400 p-4 mb-6">
            <div class="flex">
              <div class="flex-shrink-0">
                <CheckCircleIcon class="h-5 w-5 text-green-400" />
              </div>
              <div class="ml-3">
                <p class="text-sm text-green-700">
                  {{ $page.props.flash.success }}
                </p>
              </div>
            </div>
          </div>
          <!-- Customers Table -->
          <div v-if="customers.data.length > 0">

            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th scope="col"
                      class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      <input type="checkbox" v-model="selectAll" @change="toggleSelectAll"
                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                    </th>
                    <th scope="col"
                      class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      No.</th>
                    <th scope="col"
                      class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      ID</th>
                    <th scope="col"
                      class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Name</th>
                    <th scope="col"
                      class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Phone</th>
                    <th scope="col"
                      class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Expiry</th>
                    <th scope="col"
                      class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Days Remaining</th>
                    <th scope="col"
                      class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      SMS Status</th>

                    <th scope="col"
                      class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Action</th>

                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 text-sm">
                  <tr v-for="customer in customers.data" :key="customer.id">
                    <td class="px-2 py-2 whitespace-nowrap">
                      <input type="checkbox" v-model="selectedCustomers" :value="customer.id"
                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                    </td>
                    <td class="px-2 py-2 whitespace-nowrap">{{ customer.id }}</td>
                    <td class="px-2 py-2 whitespace-nowrap">{{ customer.ftth_id }}</td>
                    <td class="px-2 py-2 whitespace-nowrap">{{ customer.name }}</td>
                    <td class="px-2 py-2 whitespace-nowrap">{{ customer.phone_1 }}</td>
                    <td class="px-2 py-2 whitespace-nowrap">{{ customer.service_off_date }}</td>
                    <td class="px-2 py-2 whitespace-nowrap">{{ customer.days_remaining }}</td>
                    <td class="px-2 py-2 whitespace-nowrap">{{ customer.sms_status }}</td>
                    <td class="px-2 py-2 whitespace-nowrap">
                      <a href="#" @click="edit(customer)" class="text-yellow-600 hover:text-yellow-900"><i
                          class="fas fa-pen"></i></a>
                      |
                      <a href="#" @click="deleteIt(customer)" class="text-red-600 hover:text-red-900"><i
                          class="fas fa-trash"></i></a>


                    </td>

                  </tr>
                </tbody>
              </table>
            </div>

            <div class="mt-6 flex gap-2">
              <a type="button" @click="deleteSelected"
                class="inline-flex cursor-pointer justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 items-center"
                :disabled="selectedCustomers.length === 0">
                Delete Selected Customers <i class="ml-1 fa fa-trash"></i>
              </a>
              <a type="button" @click="sendReminders"
                class="inline-flex cursor-pointer justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 items-center"
                :disabled="selectedCustomers.length === 0" v-if="smsgateway && smsgateway?.status == '1'">
                Send Reminders to Selected Customers <i class="ml-2 fa fa-sms fa-xl"></i>
              </a>
            </div>

          </div>
        </div>
        <div class="mt-2p-4 ">
          <span v-if="customers.total" class="w-full block mt-4">
            <label class="text-xs text-gray-600">{{ customers.data.length }} Customers in Current Page. Total Number of
              Customers : {{ customers.total }}</label>
          </span>

          <span v-if="customers.links">
            <pagination class="mt-4" :links="customers.links" />
          </span>
          <!-- No Customers Message -->
          <div v-else class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
            <div class="flex">
              <div class="flex-shrink-0">
                <ExclamationIcon class="h-5 w-5 text-yellow-400" />
              </div>
              <div class="ml-3">
                <p class="text-sm text-yellow-700">
                  No customers found with service expiry within the next {{ daysBeforeExpiry }} days.
                </p>
              </div>
            </div>
          </div>


        </div>

        <div ref="editing" class="fixed z-10 inset-0 overflow-y-auto ease-out duration-400" v-if="editing">
          <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity">
              <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <!-- This element is to trick the browser into centering the modal contents. -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>​
            <div
              class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-7xl sm:w-full"
              role="dialog" aria-modal="true" aria-labelledby="modal-headline">

              <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 shadow sm:rounded-lg">

                <h6 class="md:min-w-full text-indigo-700 text-sm uppercase font-bold block pt-1 no-underline">Edit Data
                </h6>

                <div class="hidden sm:block" aria-hidden="true">
                  <div class="py-5">
                    <div class="border-t border-gray-200"></div>
                  </div>
                </div>
                <div class="md:grid ">


                  <div class="mb-4 md:col-span-1">

                    <label for="ftth_id" class="block text-gray-700 text-sm font-bold mb-2">Customer ID :</label>
                    <input type="text"
                      class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                      id="ftth_id" placeholder="Enter Customer ID" v-model="form.ftth_id" />

                    <div v-if="$page.props.errors.name" class="text-red-500">{{ $page.props.errors.name }}</div>

                    <label for="name" class="mt-4 block text-gray-700 text-sm font-bold mb-2">Customer Name :</label>
                    <input type="text"
                      class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                      id="name" placeholder="Enter Customer Name" v-model="form.name" />

                    <div v-if="$page.props.errors.name" class="text-red-500">{{ $page.props.errors.name }}</div>

                    <label for="phone" class="mt-4 block text-gray-700 text-sm font-bold mb-2">Contact No.
                      :</label>
                    <input type="text"
                      class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                      id="phone" placeholder="Enter Phone No." v-model="form.phone_1" />
                    <div v-if="$page.props.errors.phone_1" class="text-red-500">{{ $page.props.errors.phone_1 }}</div>

                    <label for="service_off_date" class="mt-4 block text-gray-700 text-sm font-bold mb-2">Expiry Date.
                      :</label>
                    <input type="date"
                      class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                      id="service_off_date" v-model="form.service_off_date" />
                    <div v-if="$page.props.errors.service_off_date" class="text-red-500">{{
        $page.props.errors.service_off_date }}</div>
                    <label class="mt-4 block text-gray-700 text-sm font-bold mb-2">
                      <input
                        class="text-green-500 w-6 h-6 mr-2 focus:ring-green-400 focus:ring-opacity-25 border border-gray-300 rounded"
                        type="checkbox" v-model="form.reset_sms" />
                      Reset SMS
                    </label>
                  </div>


                </div>



              </div>

              <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">

                <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                  <button @click="updateData" type="button"
                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">Update</button>
                </span>
                <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
                  <button @click="closeData" type="button"
                    class="inline-flex justify-center w-full rounded-md border border-gray-300 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5">Close</button>
                </span>
              </div>

            </div>
          </div>
        </div>

      </div>
    </div>
  </app-layout>
</template>

<script>
import { ref, watch, onMounted } from 'vue';
import { router, Head, useForm,Link } from '@inertiajs/vue3';
import AppLayout from "@/Layouts/AppLayout";
import Pagination from "@/Components/Pagination";
import BillReminderProgress from "@/Components/BillReminderProgress";
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
export default {
  name: "Bill Reminder",
  components: {
    AppLayout,
    BillReminderProgress,
    VueDatePicker,
    Link,
    Pagination
  },

  props: {
    customers: Array,
    smsgateway:Object,
    daysBeforeExpiry: Number,
    today: String,
    endDate: String
  },

  setup(props) {
    const selectedCustomers = ref([]);
    const selectAll = ref(false);
    const editing = ref(false);
    const days = ref(props.daysBeforeExpiry < 1 ? props.daysBeforeExpiry : 1);
    const expiration = ref("")
    const smsprogress = ref(0);
    const showSmsProgress = ref(false);
    const form = useForm({
      id: null,
      ftth_id: null,
      customer_id: null,
      name: null,
      phone_1: null,
      service_off_date: null,
      reset_sms: false,
    });
    const filters = ref({
      id: '',
      name: '',
      phone: '',
      expiry_date: '',
      no_phone: false // Add new filter property
    });
    const applyFilters = () => {
      router.get(route('bill-reminders.index'), {
        filter_id: filters.value.id,
        filter_name: filters.value.name,
        filter_phone: filters.value.phone,
        filter_expiry: filters.value.expiry_date,
        filter_no_phone: filters.value.no_phone, // Add new filter to request
        days: days.value
      }, {
        preserveState: true,
        replace: true
      });
    };

    const resetFilters = () => {
      filters.value = {
        id: '',
        name: '',
        phone: '',
        expiry_date: '',
        no_phone: false // Reset new filter
      };

      router.get(route('bill-reminders.index'), {
        days: days.value
      }, {
        preserveState: true,
        replace: true
      });
    };
    const edit = (customer) => {
      form.id = customer.id;
      form.ftth_id = customer.ftth_id;
      form.customer_id = customer.customer_id;
      form.name = customer.name;
      form.phone_1 = customer.phone_1;
      form.service_off_date = customer.service_off_date;
      form.reset_sms = false;
      editing.value = true;
    };
    const updateData = () => {
      form.put(route('bill-reminders.update', form.id), {
        onSuccess: (page) => {
          Toast.fire({
            icon: "success",
            title: page.props.flash.message,
          });
          editing.value = false;
          form.reset();
        }
      });
    };
    const closeData = () => {
      editing.value = false;
      form.reset();
    };
    const deleteIt = (customer) => {
      Toast.fire({
        title: 'Delete Customer?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        position: "center",
        timer: 10000,
        toast: false
      }).then((result) => {
        if (result.isConfirmed) {
          router.delete(route('bill-reminders.destroy', customer.id), {
            onSuccess: (page) => {
              Toast.fire({
                icon: "success",
                title: page.props.flash.message,
              });
            }
          });
        }
      });
    };
    const reminderForm = useForm({
      customer_ids: []
    });

    const toggleSelectAll = () => {
      if (selectAll.value) {
        selectedCustomers.value = props.customers.data.map(customer => customer.id);
      } else {
        selectedCustomers.value = [];
      }
    };
    // watch(selectedCustomers, (newVal) => {
    //   selectAll.value = newVal.length === props.customers.length;
    // });
    const createList = () => {
      Toast.fire({
        title: 'Generate List?',
        text: `Generate reminder list for customers expiring in ${days.value} days? This action will empty current list.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, generate it!',
        position: "center",
        timer: 10000,
        toast: false
      }).then((result) => {
        if (result.isConfirmed) {
          router.post(route('bill-reminders.create-reminder'), { expiration: expiration.value }, {
            onSuccess: (page) => {
              Toast.fire({
                icon: "success",
                title: page.props.flash.message,
              });
            }
          });
        }
      });
    };

    const emptyList = () => {
      Toast.fire({
        title: 'Empty List?',
        text: "This will remove all customers from the reminder list. Continue?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, empty it!',
        position: "center",
        timer: 10000,
        toast: false
      }).then((result) => {
        if (result.isConfirmed) {
          router.post(route('bill-reminders.empty-reminder'), {}, {
            onSuccess: (page) => {
              Toast.fire({
                icon: "success",
                title: page.props.flash.message,
              });
            }
          });
        }
      });
    };
    const deleteSelected = () => {
      Toast.fire({
        title: 'Are you sure you want to delete them?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete them!',
        cancelButtonText: 'No, cancel!',
        position: "center",
        timer: 10000,
        toast: false
      }).then((result) => {
        if (result.isConfirmed) {
          reminderForm.customer_ids = selectedCustomers.value;
          reminderForm.post(route('bill-reminders.delete-selected'), {
            onSuccess: (page) => {
              Toast.fire({
                icon: "success",
                title: page.props.flash.message,
              });
              selectAll.value = false;
              selectedCustomers.value = [];
            }
          });
        }
      });
    };
    const sendReminders = () => {
      Toast.fire({
        title: 'Send Reminders?',
        text: "Send SMS reminders to selected customers?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, send reminders!',
        position: "center",
        timer: 10000,
        toast: false,
      }).then((result) => {
        if (result.isConfirmed) {
          reminderForm.customer_ids = selectedCustomers.value;
          reminderForm.post(route('bill-reminders.send'), {
            preserveState: true,
            preserveScroll: true,
            replace: true,
            onSuccess: (page) => {
              Toast.fire({
                icon: "success",
                title: page.props.flash.message,
                timer: 200,
              });
            }
          });
        }
      });
    };

    const sendAllReminder = () => {
      Toast.fire({
        title: 'Send to All?',
        text: "Send SMS reminders to all customers?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, send to all!',
        position: "center",
        timer: 10000,
        toast: false
      }).then((result) => {
        if (result.isConfirmed) {
          router.post(route('bill-reminders.send-all'), {}, {
            replace: true,
            preserveState: true,
            preserveScroll: true,
            onSuccess: (page) => {
              Toast.fire({
                icon: "success",
                title: page.props.flash.message,
              });
            }
          });
        }
      });
    };
    const openUploadView = () => {
      router.visit(route('bill-reminders.upload-view'));
    }
    const clearNoPhoneCustomers = () => {
      if (confirm('Are you sure you want to remove all customers without phone numbers from the reminder list?')) {
        router.post(route('bill-reminders.clear-no-phone'), {}, {
          onSuccess: (page) => {
            Toast.fire({
              icon: "success",
              title: page.props.flash.message,
            });
          }
        });
      }
    };
    const downloadExcel = () => {

      axios.post(route('bill-reminders.download-excel'),
        {
          filter_id: filters.value.id,
          filter_name: filters.value.name,
          filter_phone: filters.value.phone,
          filter_expiry: filters.value.expiry_date,
          filter_no_phone: filters.value.no_phone, // Add new filter to request
          days: days.value
        }
        , { responseType: "blob" }).then((response) => {
          var a = document.createElement("a");
          document.body.appendChild(a);
          a.style = "display: none";
          var blob = new Blob([response.data], {
            type: response.headers["content-type"],
          });
          const link = document.createElement("a");
          link.href = window.URL.createObjectURL(blob);
          link.download = `billreminder_${new Date().getTime()}.xlsx`;
          link.click();
        });
    };
    const dismiss = (e) => {

      if (e) {

        showSmsProgress.value = !e;
      }
    };
    onMounted(() => {
      window.Echo.channel('reminderprogressbar')
        .listen('.remindersms', (e) => {
          smsprogress.value = e.progress;
          if (typeof props.customers == 'object') {
            props.customers.data.map(function (x) {
              if (x.id == e.id) {
              
                return x.sms_status = 'sent';
              }
            });
          }
          if (smsprogress.value > 0) {
            showSmsProgress.value = true;
          }
        });
    });
    return {
      days,
      editing,
      closeData,
      updateData,
      edit,
      deleteIt,
      deleteSelected,
      selectedCustomers,
      selectAll,
      form,
      reminderForm,
      toggleSelectAll,
      createList,
      emptyList,
      sendAllReminder,
      sendReminders,
      downloadExcel,
      openUploadView,
      filters,
      applyFilters,
      resetFilters,
      clearNoPhoneCustomers,
      showSmsProgress,
      smsprogress,
      dismiss,
      expiration
    };
  }
}
</script>
