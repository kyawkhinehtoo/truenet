<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">SN Setup</h2>
    </template>

    <div class="py-2">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">


        <div class="flex justify-between space-x-2 items-end mb-2 px-1 mt-2 md:px-0">
          <div class="relative flex flex-wrap z-0">

            <span
              class="z-10 h-full leading-snug font-normal  text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-3"><i
                class="fas fa-search"></i></span>
            <input type="text" placeholder="Search here..."
              class="border-0 px-3 py-3 placeholder-gray-300 text-gray-600 relative  bg-white rounded text-sm shadow outline-none focus:outline-none focus:ring w-full pl-10"
              id="search" v-model="search" v-on:keyup.enter="searchPort" />

          </div>
          <button @click="() => {
            (showSN = true), (editMode = false);
          }
            "
            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
            Create
          </button>
        </div>


        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg" v-if="overall.data">
          <table class="min-w-full divide-y divide-gray-200 table-auto ">
            <thead class="bg-gray-50">
              <tr class="text-left">
                <th scope="col" class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">No.</th>
                <th scope="col" class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">DN/Port</th>
                <th scope="col" class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">SN Name</th>
                <th scope="col" class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">Total Customers </th>
                <th scope="col" class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">Description</th>
                <th scope="col" class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">Location</th>
                <th scope="col" class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">Input dbm</th>
                <th scope="col" class="relative px-6 py-3"><span class="sr-only">Action</span></th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 max-h-64 ">
              <tr v-for="(row, index) in overall.data" v-bind:key="row.id">
                <td class="px-6 py-3 font-medium">{{ overall.from + index }}</td>
                <td class="px-6 py-3 font-medium">{{ row.dn_name }}</td>
                <td class="px-6 py-3 font-medium">{{ row.name }}</td>
                <td class="px-6 py-3 font-medium">{{ row.ports }}</td>
                <td class="px-6 py-3 font-medium">{{ row.description }}</td>
                <td class="px-6 py-3 font-medium">{{ row.location }}</td>
                <td class="px-6 py-3 font-medium">{{ row.input_dbm }}</td>
                <td class="px-6 py-3 text-xs font-medium text-right">
                  <a href="#" @click="editSN(row)" class="text-indigo-600 hover:text-indigo-900">Edit</a> |
                  <a href="#" @click="confirmDelete(row.id)" class="text-red-600 hover:text-red-900">Delete</a>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <span v-if="overall.total" class="w-full block mt-4">
          <label class="text-xs text-gray-600">{{ overall.data.length }} SN Port List in Current Page. Total Number of
            SN
            Ports : {{ overall.total }}</label>
        </span>
        <span v-if="overall.links">
          <pagination class="mt-6" :links="overall.links" />
        </span>


      </div>
    </div>
  </app-layout>
  <jet-confirmation-modal :show="id" @close="id = null">
    <template #title> Delete Node</template>
    <template #content> Are you sure you would like to delete this API token? </template>
    <template #footer>
      <jet-secondary-button @click="id = null"> Cancel </jet-secondary-button>
      <jet-danger-button class="ml-2" @click="deleteNode"> Delete </jet-danger-button>
    </template>
  </jet-confirmation-modal>
  <jet-dialog-modal :show="showSN" @close="showSN = false">
    <template #title> Add New Port </template>
    <template #content>
      <div v-if="$page.props.errors[0]" class="text-red-500">{{ $page.props.errors[0] }}</div>
      <div>
        <div class="mt-4 text-sm">
          <div class="mt-1 flex rounded-md shadow-sm" v-if="dns.length !== 0">
            <multiselect deselect-label="Selected already" :options="dns" track-by="id" label="name"
              v-model="form.dn_id" :allow-empty="true"></multiselect>
          </div>
          <div class="mb-4 md:col-span-1">
            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">SN Name :</label>
            <input type="text"
              class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
              id="name" placeholder="Enter SN Name" v-model="form.name" />
            <div v-if="$page.props.errors.name" class="text-red-500">{{ $page.props.errors.name }}
            </div>
          </div>
          <div class="mb-4 md:col-span-1">
            <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Description :</label>
            <textarea
              class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
              id="description" placeholder="Enter Description" v-model="form.description" />
            <div v-if="$page.props.errors.description" class="text-red-500">{{ $page.props.errors.description }}
            </div>
          </div>
          <div class="mb-4 md:col-span-1">
            <label for="location" class="block text-gray-700 text-sm font-bold mb-2">SN Location :</label>
            <input type="text"
              class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
              id="location" placeholder="Enter Location (Lat,Long)" v-model="form.location" />
            <div v-if="$page.props.errors.location" class="text-red-500">{{ $page.props.errors.location }}
            </div>
          </div>
          <div class="mb-4 md:col-span-1">
            <label for="input_dbm" class="block text-gray-700 text-sm font-bold mb-2">SN Input dbm :</label>
            <input type="text"
              class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
              id="input_dbm" placeholder="Enter Input Dbm" v-model="form.input_dbm" />
            <div v-if="$page.props.errors.input_dbm" class="text-red-500">{{ $page.props.errors.input_dbm }}
            </div>
          </div>

        </div>
      </div>
    </template>
    <template #footer>
      <jet-secondary-button @click="cancelSN"> Close </jet-secondary-button>
      <primary-button class="ml-2" @click="saveSN"> Save </primary-button>
    </template>
  </jet-dialog-modal>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import Pagination from "@/Components/Pagination";
import PrimaryButton from "@/Jetstream/PrimaryButton";
import JetDialogModal from "@/Jetstream/DialogModal";
import JetSecondaryButton from "@/Jetstream/SecondaryButton";
import JetDangerButton from "@/Jetstream/DangerButton";
import JetInput from "@/Jetstream/TextInput";
import JetInputError from "@/Jetstream/InputError";
import JetConfirmationModal from "@/Jetstream/ConfirmationModal";
import { reactive, ref, onMounted, onUpdated } from "vue";
import { router } from '@inertiajs/vue3';
import { useForm } from "@inertiajs/vue3";
import Multiselect from "@suadelabs/vue3-multiselect";
export default {
  name: "SNPorts",
  components: {
    AppLayout,
    Pagination,
    PrimaryButton,
    JetDialogModal,
    JetSecondaryButton,
    JetDangerButton,
    JetConfirmationModal,
    JetInput,
    JetInputError,
    useForm,
    Multiselect,
  },
  props: {
    overall: Object,
    sns: Object,
    dns: Object,
    sns_all: Object,
    errors: Object,
  },

  setup(props) {
    let id = ref(null);
    let showSN = ref(false);
    let editMode = ref(false);
    let search = ref('');

    const form = useForm({
      id: null,
      sn: null,
      dn_id: null,
      name: null,
      description: null,
      location: null,
      input_dbm: null,

    });
    function confirmDelete(data) {
      id.value = data;
    }
    function resetForm() {
      form.id = null;
      form.dn_id = null;
      form.sn = null;
      form.name = null;
      form.description = null;
      form.location = null;
      form.input_dbm = null;
    }
    function editSN(data) {
      form.id = data.id;
      form.dn_id = props.dns.filter((d) => d.id == data.dn_id)[0];
      form.sn = props.sns_all.filter((d) => d.name == data.name)[0];
      form.name = data.name;
      form.description = data.description;
      form.location = data.location;
      form.input_dbm = data.input_dbm;
      showSN.value = true;
      editMode.value = true;
    }
    function saveSN() {
      if (!editMode.value) {
        if (form.sn)
          form.name = form.sn.name;
        form._method = "POST";
        form.post("/snport", {
          preserveState: true,
          onSuccess: (page) => {
            showSN.value = false;
            resetForm();
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
        form._method = "PUT";
        form.put("/snport/" + form.id, {
          preserveState: true,
          onSuccess: (page) => {
            showSN.value = false;
            resetForm();
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
    function cancelSN() {
      showSN.value = false;
      resetForm();
    }
    function deleteNode() {
      let data = Object({});
      data.id = id.value;
      data._method = "DELETE";

      router.post("/snport/" + data.id, data, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: (page) => {
          id.value = false;
          Toast.fire({
            icon: "success",
            title: page.props.flash.message,
          });
        },
        onError: (errors) => {
          showSN.value = false;
          console.log("error ..".errors);
        },
      });


    }
    function searchPort() {
      router.get('/snport/', { keyword: search.value }, { preserveState: true })
    }

    return { id, deleteNode, saveSN, editSN, cancelSN, showSN, form, editMode, confirmDelete, searchPort, search };
  },
};
</script>

<style></style>