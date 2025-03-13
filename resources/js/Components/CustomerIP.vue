<template>
  <div class="flex w-full justify-end">
    <a v-if="!add && permission && !role.read_only_ip && role.add_ip" href="#" @click="openIP()"
      class="-mt-2 mb-2 text-center items-center px-4 py-3 bg-indigo-500 border border-transparent rounded-sm font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-400 active:bg-indigo-600 focus:outline-none focus:border-gray-900 disabled:opacity-25 transition mr-1">Assign
      IPv4<i class="fas fa-plus-circle opacity-75 lg:ml-1 text-sm"></i></a>

  </div>
  <div v-if="!ip_list" wire:loading class=" w-full flex flex-col items-center justify-center">
    <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-purple-500"></div>
    <h2 class="text-center text-gray-600 text-sm font-semibold mt-2">Loading...</h2>
  </div>

  <div v-if="ip_list">
    <table class="min-w-full divide-y divide-gray-200 table-auto ">
      <thead class="bg-gray-50">
        <tr class="text-left">

          <th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">ID</th>
          <th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">IP Address</th>
          <th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">Description</th>
          <th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase text-right">Annual Fees</th>
          <th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">From Date</th>
          <th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">To Date</th>
          <th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">Created At</th>
          <th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">Updated At</th>
          <th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase"></th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-200 text-sm max-h-64 ">
        <tr v-for="(row, index) in ip_list" v-bind:key="row.id">
          <td class="px-4 py-3 text-xs font-medium">{{ index + 1 }}</td>
          <td class="px-4 py-3 text-xs font-medium"> {{ row.ip_address }} </td>
          <td class="px-4 py-3 text-xs font-medium">
            <p v-if="row.description.length > 30">{{ row.description.substring(0, 30) + '...' }}</p>
            <p v-else>{{ row.description }}</p>
          </td>
          <td class="px-4 py-3 text-xs font-medium whitespace-nowrap text-right"> {{ row.annual_charge }} {{
            row.currency.toUpperCase() }} </td>
          <td class="px-4 py-3 text-xs font-medium whitespace-nowrap"> {{ row.start_date }} </td>
          <td class="px-4 py-3 text-xs font-medium whitespace-nowrap"> {{ (row.end_date) ? row.end_date : 'N/A' }} </td>
          <td class="px-4 py-3 text-xs font-medium"> {{ row.created_at }} </td>
          <td class="px-4 py-3 text-xs font-medium"> {{ row.updated_at }} </td>
          <td class="px-4 py-3 text-xs font-medium whitespace-nowrap">

            <a href="#" @click="view(row)" class="text-indigo-600"><i class="fa fa-eye "></i></a>
            <a href="#" @click="edit(row)" class="text-yellow-600 ml-2" v-if="!role.read_only_ip && role.edit_ip"> <i
                class="fa fa-pen "></i></a>
            <a href="#" @click="deleteIP(row)" class="text-red-600 ml-2" v-if="!role.read_only_ip && role.delete_ip"> <i
                class="fa fa-trash"></i></a>
          </td>

        </tr>
      </tbody>
    </table>
  </div>

  <jet-dialog-modal :show="add">
    <template #title>
      <span v-if="editMode & !viewMode">Update IP </span>
      <span v-if="!editMode && !viewMode">Add New IP </span>
      <span v-if="!editMode && viewMode">View Details</span>
    </template>
    <template #content>
      <div>
        <div v-if="$page.props.errors[0]" class="text-red-500">{{ $page.props.errors[0] }}</div>
        <div class="mt-4 text-sm grid grid-cols-1 md:grid-cols-2 gap-6">

          <div class="mb-4 md:col-span-2">
            <label for="ip_address" class="block text-gray-700 text-sm font-bold mb-2">IP Address : (IPv4, /32)</label>
            <input type="text"
              class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
              id="ip_address" placeholder="Enter IPv4 Address" v-model="form.ip_address" @input="formatIpAddress" />
            <div v-if="$page.props.errors.ip_address" class="text-red-500">{{ $page.props.errors.ip_address }}
            </div>
          </div>

          <div class="mb-4 md:col-span-2">
            <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Description :</label>
            <textarea
              class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
              id="description" placeholder="Enter Description" v-model="form.description" />
            <div v-if="$page.props.errors.description" class="text-red-500">{{ $page.props.errors.description }}
            </div>
          </div>

          <div class="mb-4 md:col-span-1">
            <label for="annual_charge" class="block text-gray-700 text-sm font-bold mb-2">Annual Fees :</label>
            <input type="number"
              class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
              id="annual_charge" placeholder="Enter Annual Fees" v-model="form.annual_charge" min="0" />
            <div v-if="$page.props.errors.annual_charge" class="text-red-500">{{ $page.props.errors.annual_charge }}
            </div>
          </div>
          <div class="mb-4 md:col-span-1">
            <label for="currency" class="block text-gray-700 text-sm font-bold mb-2"> Currency Type :</label>
            <div class="md:mt-4 flex">
              <label class="flex-auto items-center"> <input type="radio" class="form-radio h-5 w-5 text-green-600"
                  checked name="currency" v-model="form.currency" value="baht" /><span
                  class="ml-2 text-gray-700 text-sm">TBH</span></label>

              <label class="flex-auto items-center"> <input type="radio" class="form-radio h-5 w-5 text-indigo-600"
                  name="currency" v-model="form.currency" value="usd" /><span
                  class="ml-2 text-gray-700 text-sm">USD</span> </label>

              <label class="flex-auto items-center"> <input type="radio" class="form-radio h-5 w-5 text-yellow-600"
                  name="currency" v-model="form.currency" value="mmk" /><span
                  class="ml-2 text-gray-700 text-sm">MMK</span> </label>
            </div>
          </div>


          <div class="mb-4 md:col-span-2">
            <label for="start_date" class="block text-gray-700 text-sm font-bold mb-2">From Date:</label>
            <input type="date"
              class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
              id="start_date" placeholder="Enter Start Date" v-model="form.start_date" @change="updateEndDate" />
            <div v-if="$page.props.errors.start_date" class="text-red-500">{{ $page.props.errors.start_date }}
            </div>
          </div>

          <div class="mb-4 md:col-span-2">
            <label for="end_date" class="block text-gray-700 text-sm font-bold mb-2">To Date:</label>
            <input type="date"
              class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
              id="end_date" placeholder="Enter End Date" v-model="form.end_date" />
            <div v-if="$page.props.errors.end_date" class="text-red-500">{{ $page.props.errors.end_date }}
            </div>
          </div>

        </div>
      </div>
    </template>
    <template #footer>
      <jet-secondary-button @click="add = false"> Close </jet-secondary-button>
      <jet-button class="ml-2" @click="save" v-if="!viewMode"> <span v-if="editMode">Update</span> <span
          v-else>Save</span> </jet-button>
    </template>
  </jet-dialog-modal>
</template>

<script>
import { ref, onMounted, reactive, watch, inject } from "vue";
import { router } from '@inertiajs/vue3';
import JetButton from "@/Jetstream/PrimaryButton";
import JetDialogModal from "@/Jetstream/DialogModal";
import JetSecondaryButton from "@/Jetstream/SecondaryButton";
import JetDangerButton from "@/Jetstream/DangerButton";
import JetInput from "@/Jetstream/TextInput";
import JetInputError from "@/Jetstream/InputError";
import JetConfirmationModal from "@/Jetstream/ConfirmationModal";
import { useForm } from "@inertiajs/vue3";
export default {
  name: "CustomerIP",
  components: {
    JetButton,
    JetDialogModal,
    JetSecondaryButton,
    JetDangerButton,
    JetConfirmationModal,
    JetInput,
    JetInputError,
  },
  props: {
    data: Number,
    permission: Boolean,
    errors: Object,
  },
  setup(props) {
    const role = inject("role");
    const form = useForm({
      id: null,
      ip_usage_history_id: null,
      ip_address: null,
      description: null,
      annual_charge: null,
      currency: 'baht',
      start_date: null,
      end_date: null,
      customer_id: props.data,
    });
    function reset() {
      form.id = null;
      form.ip_address = null;
      form.ip_usage_history_id = null;
      form.description = null;
      form.annual_charge = null;
      form.currency = 'baht';
      form.start_date = null;
      form.end_date = null;
      form.customer_id = props.data;
    }
    let loading = ref(false);
    let ip_list = ref();
    let add = ref(false);
    let editMode = ref(false);
    let viewMode = ref(false);
    function openIP() {
      reset();
      add.value = true;
      editMode.value = false;
    }
    function closeIP() {
      add.value = false;
      reset();
      calculate();
    }
    function deleteIP(data) {
      if (props.permission) {
        Toast.fire({
          title: "Are you sure?",
          text: "Once deleted, you will not be able to recover this data !",
          icon: "warning",
          timer: false,
          position: "center",
          showCancelButton: true,
          dangerMode: true,
        }).then((x) => {
          if (x.isConfirmed) {
            deleteIt(data);

          } else {
            Toast.fire("Your data is safe!");
          }
        });
      }


    }
    function deleteIt(data) {
      data._method = "DELETE";
      router.post("/publicIP/" + data.id, data, {
        onSuccess(page) {
          Toast.fire({
            icon: "success",
            title: "Your IP has been Deleted !",
          });
          calculate();
        }
      });
    }
    const getIP = async () => {
      let url = "/getCustomerIp/" + props.data;
      console.log(url);
      try {
        const res = await fetch(url);
        const data = await res.json();
        console.log(data);
        return data;
      } catch (err) {
        console.error(err);
      }
    };

    const calculate = () => {
      getIP().then((d) => {
        ip_list.value = d;
      });
    }
    function view(data) {
      form.id = data.id;
      form.ip_usage_history_id = data.id;
      form.ip_address = data.ip_address;
      form.description = data.description;
      form.annual_charge = data.annual_charge;
      form.currency = data.currency;
      form.start_date = data.start_date;
      form.end_date = data.end_date;
      form.customer_id = data.customer_id;
      add.value = true;
      viewMode.value = true;
    }
    function edit(data) {
      form.id = data.id;
      form.ip_usage_history_id = data.id;
      form.ip_address = data.ip_address;
      form.description = data.description;
      form.annual_charge = data.annual_charge;
      form.currency = data.currency;
      form.start_date = data.start_date;
      form.end_date = data.end_date;
      form.customer_id = data.customer_id;
      add.value = true;
      viewMode.value = false;
      editMode.value = true;
    }
    function save() {
      if (!editMode.value) {
        form._method = "POST";
        form.post("/publicIP", {
          preserveState: true,
          onSuccess: (page) => {
            add.value = false;
            reset();
            calculate();
            Toast.fire({
              icon: "success",
              title: page.props.flash.message,
            });
          },
          onError: (errors) => {

            console.log("error ..".errors);
          },
        });

      } else {
        form._method = "PATCH";
        form.put("/publicIP/" + form.id, {

          preserveState: true,
          onSuccess: (page) => {
            add.value = false;
            reset();
            calculate();
            Toast.fire({
              icon: "success",
              title: page.props.flash.message,
            });
          },
          onError: (errors) => {

            console.log("error ..".errors);
          },
        });
      }
    }
    function formatIpAddress() {
      // Remove any non-numeric and non-dot characters from the input
      form.ip_address = form.ip_address.replace(/[^0-9.]/g, '');

      // Split the input into parts separated by dots
      let parts = form.ip_address.split('.');

      // Limit each part to a maximum of 3 characters
      parts = parts.map(part => part.slice(0, 3));

      // Join the parts back with dots
      form.ip_address = parts.join('.');
    }
    const updateEndDate = () => {
      // Check if the selected start_date is a valid date
      const startDate = new Date(form.start_date);
      if (!isNaN(startDate.getTime())) {
        // Increment the start_date by one year
        const endDate = new Date(startDate);
        endDate.setFullYear(endDate.getFullYear() + 1);
        endDate.setDate(endDate.getDate() - 1);
        // Format the end_date as "YYYY-MM-DD" to match the input type "date"
        const formattedEndDate = endDate.toISOString().slice(0, 10);

        // Set the end_date in the form data
        form.end_date = formattedEndDate;
      }
    };
    onMounted(() => {

      calculate();
    });
    return { ip_list, add, loading, form, editMode, viewMode, role, save, view, deleteIP, openIP, closeIP, edit, formatIpAddress, updateEndDate };
  },
};
</script>
