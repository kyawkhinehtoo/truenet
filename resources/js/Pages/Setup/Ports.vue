<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">DN Setup</h2>

    </template>

    <div class="py-2">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between space-x-2 items-end mb-2 px-1 md:px-0">
          <div class="relative flex flex-wrap z-0">
            <div v-if="pops.length !== 0">
              <multiselect deselect-label="Selected already" :options="pops" track-by="id" label="site_name"
                v-model="search.pop" :allow-empty="true" :multiple="true" placeholder="Select Multiple POP site"
                class="multi rounded-md shadow-sm text-sm border-0 placeholder-gray-300 text-gray-600">
              </multiselect>
            </div>
            <div class="ml-4">
              <span
                class="z-10 h-full leading-snug font-normal ß text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-3"><i
                  class="fas fa-search"></i></span>
              <input type="text" placeholder="Search here..."
                class="border-0 p-3 placeholder-gray-300 text-gray-600 relative bg-white rounded text-sm shadow outline-none focus:outline-none focus:ring-0 w-full pl-10"
                id="search" v-model="search.general" v-on:keyup.enter="searchPort" />


            </div>
            <div class="ml-4">
              <button @click="searchPort"
                class="inline-flex items-center px-4 py-3 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">Search
                <i class="fa fas fa-search text-white ml-2"></i></button>
            </div>



          </div>
          <button @click="() => { showDN = true, editMode = false }"
            class="inline-flex items-center px-4 py-3 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">Create
            <i class="fa fa-solid fa-circle-plus ml-2"></i>

          </button>
        </div>


        <div class="col-1">

          <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg" v-if="overall.data">
            <table class="min-w-full divide-y divide-gray-200 table-auto ">
              <thead class="bg-gray-50">
                <tr class="text-left">
                  <th scope="col" class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">No.</th>
                  <th scope="col" class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">DN Name</th>
                  <th scope="col" class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">POP Name</th>
                  <th scope="col" class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">Total SN</th>
                  <th scope="col" class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">Description</th>
                  <th scope="col" class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">Location</th>
                  <th scope="col" class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">Input dbm</th>
                  <th scope="col" class="relative px-6 py-3"><span class="sr-only">Action</span></th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="(row, index) in overall.data" v-bind:key="row.id">
                  <td class="px-6 py-3 font-medium">{{ overall.from + index }}</td>
                  <td class="px-6 py-3 font-medium">{{ row.name }}</td>
                  <td class="px-6 py-3 font-medium">{{ getPOPName(row.pop) }}</td>
                  <td class="px-6 py-3 font-medium">{{ row.ports }}</td>
                  <td class="px-6 py-3 font-medium">{{ row.description }}</td>
                  <td class="px-6 py-3 font-medium">{{ row.location }}</td>
                  <td class="px-6 py-3 font-medium">{{ row.input_dbm }}</td>
                  <td class="px-6 py-3 font-medium text-right">
                    <a href="#" @click="editDN(row)" class="text-indigo-600 hover:text-indigo-900">Edit</a> |
                    <a href="#" @click="confirmDelete(row.id)" class="text-red-600 hover:text-red-900">Delete</a>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <span v-if="overall.total" class="w-full block mt-4">
            <label class="text-xs text-gray-600">{{ overall.data.length }} DN List in Current Page. Total Number of DNs
              : {{
                overall.total }}</label>
          </span>
          <span v-if="overall.links">
            <pagination class="mt-6" :links="overall.links" />
          </span>

          <!-- <div v-show="tab == 1">
       <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg" v-if="overall">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No.</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DN Name</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Ports</th>
                <th scope="col" class="relative px-6 py-3"><span class="sr-only">Action</span></th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="(row, index) in overall.data" v-bind:key="row.name">
                <td class="px-6 py-3 whitespace-nowrap">{{ index + 1 }}</td>
                <td class="px-6 py-3 whitespace-nowrap">{{ row.name }}</td>
                <td class="px-6 py-3 whitespace-nowrap">{{ row.ports }}</td>
                <td class="px-6 py-3 whitespace-nowrap text-right text-sm font-medium">
                  <a href="#" @click="confirmDelete(row.name)" class="text-red-600 hover:text-red-900">Delete</a>
                </td>
              </tr>
            </tbody>
          </table>
           
        </div>
         <span v-if="dns.links">
          <pagination class="mt-6" :links="overall.links" />
        </span>
       </div> -->

        </div>



      </div>
    </div>
  </app-layout>
  <jet-confirmation-modal :show="dn_id" @close="dn_id = null">
    <template #title> Delete Node</template>
    <template #content> Are you sure you would like to delete this DN ? It might effect to Customer Data ! </template>
    <template #footer>
      <jet-secondary-button @click="dn_id = null"> Cancel </jet-secondary-button>
      <jet-danger-button class="ml-2" @click="deleteNode"> Delete </jet-danger-button>
    </template>
  </jet-confirmation-modal>
  <jet-dialog-modal :show="showDN" @close="showDN = false">
    <template #title> Add New Port </template>
    <template #content>
      <div>
        <div v-if="$page.props.errors[0]" class="text-red-500">{{ $page.props.errors[0] }}</div>
        <div class="mt-4 text-sm">
          <div class="mb-4 md:col-span-1">
            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">DN Name :</label>
            <input type="text"
              class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
              id="name" placeholder="Enter DN Name" v-model="form.name" />
            <div v-if="$page.props.errors.name" class="text-red-500">{{ $page.props.errors.name }}
            </div>
          </div>
          <div class="mb-4 md:col-span-1" v-if="pops.length !== 0">
            <label for="pop" class="block text-gray-700 text-sm font-bold mb-2">POP Site :</label>
            <multiselect deselect-label="Selected already" :options="pops" track-by="id" label="site_name"
              v-model="form.pop" :allow-empty="true" :multiple="false" :searchable="true" :prevent-autofocus="true"
              :max-height="200" placeholder="Select POP site" @select="POPSelect">
            </multiselect>
          </div>
          <div class="mb-4 md:col-span-1" v-if="pop_devices.length !== 0">
            <label for="pop" class="block text-gray-700 text-sm font-bold mb-2">OLT :</label>
            <multiselect deselect-label="Selected already" :options="pop_devices" track-by="id" label="device_name"
              v-model="form.pop_device_id" :allow-empty="true">
            </multiselect>
          </div>
          <div class="mb-4 md:col-span-1">
            <label for="location" class="block text-gray-700 text-sm font-bold mb-2">OLT Port No. </label>
            <div class="grid grid-cols-3 gap-2">
              <div class="col-span-1">
                <label for="gpon_frame" class="block text-gray-700 text-sm font-bold mb-2">Frame :</label>
                <input type="number"
                  class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                  id="location" placeholder="Enter Frame No." v-model="form.gpon_frame" />
                <div v-if="$page.props.errors.gpon_frame" class="text-red-500">{{ $page.props.errors.gpon_frame }}
                </div>
              </div>
              <div class="col-span-1">
                <label for="gpon_slot" class="block text-gray-700 text-sm font-bold mb-2">Slot :</label>
                <input type="number"
                  class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                  id="location" placeholder="Enter Slot No." v-model="form.gpon_slot" />
                <div v-if="$page.props.errors.gpon_slot" class="text-red-500">{{ $page.props.errors.gpon_slot }}
                </div>
              </div>
              <div class="col-span-1">
                <label for="gpon_port" class="block text-gray-700 text-sm font-bold mb-2">Port :</label>
                <input type="number"
                  class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                  id="location" placeholder="Enter Port No." v-model="form.gpon_port" />
                <div v-if="$page.props.errors.gpon_port" class="text-red-500">{{ $page.props.errors.gpon_port }}
                </div>
              </div>


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
            <label for="location" class="block text-gray-700 text-sm font-bold mb-2">DN Location :</label>
            <input type="text"
              class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
              id="location" placeholder="Enter Location (Lat,Long)" v-model="form.location" />
            <div v-if="$page.props.errors.location" class="text-red-500">{{ $page.props.errors.location }}
            </div>
          </div>
          <div class="mb-4 md:col-span-1">
            <label for="input_dbm" class="block text-gray-700 text-sm font-bold mb-2">DN Input dbm :</label>
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
      <jet-secondary-button @click="cancelDN"> Close </jet-secondary-button>
      <jet-button class="ml-2" @click="saveDN"> Save </jet-button>
    </template>
  </jet-dialog-modal>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import Pagination from "@/Components/Pagination";
import JetButton from "@/Jetstream/PrimaryButton";
import JetDialogModal from "@/Jetstream/DialogModal";
import JetSecondaryButton from "@/Jetstream/SecondaryButton";
import JetDangerButton from "@/Jetstream/DangerButton";
import JetInput from "@/Jetstream/TextInput";
import JetInputError from "@/Jetstream/InputError";
import JetConfirmationModal from "@/Jetstream/ConfirmationModal";
import { reactive, ref } from "vue";
import { router } from '@inertiajs/vue3';
import { useForm } from "@inertiajs/vue3";
import Multiselect from "@suadelabs/vue3-multiselect";
export default {
  name: "ports",
  components: {
    AppLayout,
    Pagination,
    JetButton,
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

    dns_all: Object,
    sns: Object,
    overall: Object,
    pops: Object,
    errors: Object,
  },
  setup(props) {
    let dn_id = ref(null);
    let showDN = ref(false);
    let pop_devices = ref("");
    let editMode = ref(false);

    const search = useForm({
      general: null,
      pop: null,
    });
    const resetSearch = () => {
      search.reset();
    }

    const form = useForm({
      id: null,
      dn_id: null,
      dn: null,
      name: null,
      description: null,
      location: null,
      input_dbm: null,
      pop: null,
      pop_device_id: null,
      gpon_frame: null,
      gpon_slot: null,
      gpon_port: null,
      tab: 1,
    });
    function confirmDelete(data) {
      dn_id.value = data;
    }
    function resetForm() {
      form.id = null;
      form.name = null;
      form.dn = null;
      form.description = null;
      form.location = null;
      form.input_dbm = null;
      form.pop = null;
      form.pop_device_id = null;
      form.gpon_frame = null;
      form.gpon_slot = null;
      form.gpon_port = null;
    }
    function isJsonString(str) {

      try {
        let parsedValue = jsonParser(str);
        console.log(parsedValue);
        return typeof parsedValue === 'object' && parsedValue !== null;
      } catch (e) {
        console.log('not json');
        return false;
      }

    }
    function jsonParser(blob) {
      let parsed = JSON.parse(blob);
      if (typeof parsed === 'string') parsed = jsonParser(parsed);
      return parsed;
    }
    function getPOPName(blob) {
      // return isJsonString(blob) ? jsonParser(blob).site_name : null;
      let pop_data = props.pops.filter((d) => d.id == blob)[0];
      if(pop_data)
      return pop_data.site_name;
    }
    const POPSelect = async (pops) => {
      try {
        const d = await getOLT(pops.id);
        if (d && !isEmptyObject(d)) {
          pop_devices.value = d;
          form.pop_device_id = null;
          form.gpon_frame = null;
          form.gpon_slot = null;
          form.gpon_port = null;
        } else {
          pop_devices.value = null;
          form.pop_device_id = null;
          form.gpon_frame = null;
          form.gpon_slot = null;
          form.gpon_port = null;
        }
        return true;
      } catch (err) {
        console.error("Error in POPSelect:", err);
        return false;
      }
    };


    const getOLT = async (id) => {
      const url = "/getOLTByPOP/" + id;
      try {
        const res = await fetch(url);
        if (!res.ok) throw new Error(`HTTP error! Status: ${res.status}`);
        const data = await res.json();
        return data;
      } catch (err) {
        console.error("Error fetching OLT:", err);
        return null;
      }
    };

    function isEmptyObject(value) {
      // Check if it's an array
      if (Array.isArray(value)) {
        console.log('array');
        // If the array is empty, return true
        if (value.length === 0) {
          console.log('empty array');
          return true;
        }
        // Check if the array contains only empty objects
        return value.every(item => typeof item === 'object' && Object.keys(item).length === 0);
      }

      // Check if it's an object
      if (value && typeof value === 'object') {
        return Object.keys(value).length === 0;
      }

      // If it's neither an object nor an array, return false
      return false;
    }
    async function editDN(data) {
      form.id = data.id;
      form.name = data.name;
      form.description = data.description;
      form.location = data.location;
      form.input_dbm = data.input_dbm;
      form.pop = data.pop ? props.pops.filter((d) => d.id == data.pop)[0] : null;

      if (form.pop) {
        const popSelected = await POPSelect(form.pop);
        if (popSelected) {
          console.log("POP Devices Value:", pop_devices.value);
          form.pop_device_id = data.pop_device_id ? pop_devices.value.find(d => d.id === data.pop_device_id) : null;
        }
      }

      form.gpon_frame = data.gpon_frame;
      form.gpon_slot = data.gpon_slot;
      form.gpon_port = data.gpon_port;
      showDN.value = true;
      editMode.value = true;
    }

    function saveDN() {
      if (!editMode.value) {
        form._method = "POST";
        form.post(route("port.store"), {
          preserveState: true,
          onSuccess: (page) => {
            showDN.value = false;
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
        form.put("/port/" + form.id, {
          preserveState: true,
          onSuccess: (page) => {
            showDN.value = false;
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
    function cancelDN() {
      showDN.value = false;
      resetForm();
    }
    function searchPort() {
      search._method = "POST";
      search.post(route("port.search"), {
        onSuccess: (page) => { },
        onError: (errors) => {
          console.log(errors);
        },
      });

    }
    function deleteNode() {
      let data = Object({});
      data.id = dn_id.value;
      data._method = "DELETE";

      router.post("/port/" + data.id, data, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: (page) => {
          dn_id.value = false;
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
    return {
      dn_id, saveDN, editDN, cancelDN, showDN, form, editMode, deleteNode, confirmDelete, searchPort, search, getPOPName, pop_devices, POPSelect


    };
  },
};
</script>

<style>
.multiselect__tags {
  min-height: 45px !important;
  display: block;
  padding: 8px 38px 0 8px !important;
  border-radius: 5px;
  border: 1px solid #e8e8e8;
  background: #fff;
  font-size: 14px;
}
</style>