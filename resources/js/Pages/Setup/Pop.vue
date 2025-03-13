<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">POP Setup</h2>

    </template>

    <div class="py-2">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between space-x-2 items-end mb-2 px-1 md:px-0">
          <div class="relative flex flex-wrap z-0">

            <span
              class="z-10 h-full leading-snug font-normal  text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-3"><i
                class="fas fa-search"></i></span>
            <input type="text" placeholder="Search here..."
              class="border-0 px-3 py-3 placeholder-gray-300 text-gray-600 relative  bg-white rounded text-sm shadow outline-none focus:outline-none focus:ring w-full pl-10"
              id="search" v-model="search" v-on:keyup.enter="searchPort" />

          </div>
          <button @click="() => { show = true, editMode = false }"
            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">Create</button>
        </div>

        <!-- Tabs -->

        <!-- <div class="inline-flex w-full divide-y divide-gray-200">
          <ul id="tabs" class="flex">
            <li class="px-2 lg:px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider" :class="[tab == 1 ? 'border-b-2 border-indigo-400 -mb-px' : 'opacity-50']"><a href="#" @click="tabClick(1)" preserve-state>Genaral</a></li>
            <li class="px-2 lg:px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider" :class="[tab == 2 ? 'border-b-2 border-indigo-400 -mb-px' : 'opacity-50']"><a href="#" @click="tabClick(2)" preserve-state>Details</a></li>
          </ul>
        </div> -->

        <div class="col-1">

          <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg" v-if="pops.data">
            <table class="min-w-full divide-y divide-gray-200 table-auto ">
              <thead class="bg-gray-50">
                <tr class="text-left">
                  <th scope="col" class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">No.</th>
                  <th scope="col" class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">Site Name</th>
                  <th scope="col" class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">Site Description</th>
                  <th scope="col" class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">Created At</th>
                  <th scope="col" class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">Updated At</th>
                  <th scope="col" class="px-4 py-3 text-xs font-medium text-gray-500 uppercase"><span
                      class="sr-only">Action</span></th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200 max-h-64 ">
                <tr v-for="(row, index) in pops.data" v-bind:key="row.id">
                  <td class="px-6 py-3 font-medium">{{ pops.from + index }}</td>
                  <td class="px-6 py-3 font-medium">{{ row.site_name }}</td>
                  <td class="px-6 py-3 font-medium">{{ row.site_description }}</td>
                  <td class="px-6 py-3 font-medium">{{ row.created_at }}</td>
                  <td class="px-6 py-3 font-medium">{{ row.updated_at }}</td>
                  <td class="px-6 py-3 font-medium text-right">
                    <a href="#" @click="edit(row)" class="text-yellow-600 ml-2"> <i class="fa fa-pen "></i></a>
                    <a href="#" @click="confirmDelete(row)" class="text-red-600 ml-2"> <i class="fa fa-trash"></i></a>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <span v-if="pops.total" class="w-full block mt-4">
            <label class="text-xs text-gray-600">{{ pops.data.length }} POP List in Current Page. Total Number of POP :
              {{
                pops.total }}</label>
          </span>
          <span v-if="pops.links">
            <pagination class="mt-6" :links="pops.links" />
          </span>
        </div>



      </div>
    </div>
  </app-layout>
  <jet-confirmation-modal :show="form.id" @close="form.id = null">
    <template #title> Delete Node</template>
    <template #content> Are you sure you would like to delete this DN ? It might effect to Customer Data ! </template>
    <template #footer>
      <jet-secondary-button @click="form.id = null"> Cancel </jet-secondary-button>
      <jet-danger-button class="ml-2" @click="deleteNode"> Delete </jet-danger-button>
    </template>
  </jet-confirmation-modal>
  <jet-dialog-modal :show="show" @close="show = false">
    <template #title> Add New Port </template>
    <template #content>
      <div class="mb-4 md:col-span-1">
        <label for="site_name" class="block text-gray-700 text-sm font-bold mb-2">POP Site Name :</label>
        <input type="text"
          class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
          id="site_name" placeholder="Enter POP Site Name" v-model="form.site_name" />
        <div v-if="$page.props.errors.site_name" class="text-red-500">{{ $page.props.errors.site_name }}
        </div>
      </div>
      <div class="mb-4 md:col-span-1">
        <label for="description" class="block text-gray-700 text-sm font-bold mb-2">POP Site Description :</label>
        <textarea
          class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
          id="description" placeholder="Enter Description" v-model="form.site_description" />
        <div v-if="$page.props.errors.site_description" class="text-red-500">{{ $page.props.errors.site_description }}
        </div>
      </div>
      <div class="mb-4 md:col-span-1">
        <label for="site_location" class="block text-gray-700 text-sm font-bold mb-2">POP Site Location :</label>
        <input type="text"
          class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
          id="site_location" placeholder="Enter Location (Lat,Long)" v-model="form.site_location"
          @input="onInputLocation" />
        <div v-if="$page.props.errors.site_location" class="text-red-500">{{ $page.props.errors.site_location }}
        </div>
      </div>
      <div class="overflow-hidden h-max ">
        <button v-on:click="devices.push({})" type="button"
          class="btn bg-indigo-600 hover:bg-indigo-700 text-sm shadow-sm rounded-md px-4 py-2 text-white font-semibold">Add
          <i class="fa fas fa-plus"></i></button>
        <table class="min-w-full text-center">
          <thead class="border-b bg-gray-50">
            <tr>
              <th scope="col" class="text-left text-sm font-bold text-gray-900 py-4">#</th>
              <th scope="col" class="text-left text-sm font-bold text-gray-900 py-4">OLT Name</th>
              <th scope="col" class="text-left text-sm font-bold text-gray-900 py-4">No. of Frame </th>
              <th scope="col" class="text-left text-sm font-bold text-gray-900 py-4">Remark</th>
              <th scope="col" class="text-left text-sm font-bold text-gray-900 py-4">#</th>
            </tr>
          </thead>
          <tbody>
            <tr class="bg-white border-b" v-for="(device, index) in devices" :key="index">
              <td class="text-sm text-gray-900 font-light w-10 py-4 whitespace-nowrap">{{ index + 1 }}</td>
              <td class="text-sm text-gray-900 font-light w-96 py-4 whitespace-nowrap">

                <input type="text" name="description" id="description" v-model="device.device_name"
                  class="form-control w-full text-sm text-normal text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none" />
              </td>
              <td class="text-sm text-gray-900 font-light w-20 py-4 whitespace-nowrap"><input type="number" name="price"
                  id="price" v-model="device.qty"
                  class="form-control w-full text-sm text-normal text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none"
                  min="1" /></td>
              <td class="text-sm text-gray-900 font-light w-28 py-4 whitespace-nowrap"><input type="text" name="remark"
                  id="remark" v-model="device.remark"
                  class="form-control w-full text-sm text-normal text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none" />
              </td>

              <td class="text-sm text-gray-900 font-light w-28 py-4 whitespace-nowrap"> <button
                  v-on:click="devices.splice(index, 1)" type="button"
                  class="btn bg-yellow-600 hover:bg-yellow-700 text-lg shadow-sm rounded-md px-2 py-0 text-white font-semibold">&times;</button>
              </td>
            </tr>

          </tbody>
        </table>
      </div>
    </template>
    <template #footer>
      <jet-secondary-button @click="cancel"> Close </jet-secondary-button>
      <jet-button class="ml-2" @click="save"> Save </jet-button>
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
  name: "pop",
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
    pops: Object,
    pop_devices: Object,
    errors: Object,
  },
  setup(props) {
    let show = ref(false);
    let search = ref('');
    let editMode = ref(false);

    const devices = ref([{
      id: '',
      device_name: '',
      qty: 1,
      remark: '',
    }]);


    const form = useForm({
      id: null,
      site_name: null,
      site_description: null,
      site_location: null,
      devices: null,
    });
    function confirmDelete(data) {
      form.id = data;
    }

    function resetForm() {
      form.id = null;
      form.site_name = null;
      form.site_description = null;
      form.site_location = null;
      form.devices = null;
      devices.value = [];
    }
    function edit(data) {
      form.id = data.id;
      form.site_name = data.site_name;
      form.site_description = data.site_description;
      form.site_location = data.site_location;
      show.value = true;
      editMode.value = true;

      if (props.pop_devices) {
        devices.value.shift();
        props.pop_devices.forEach(e => {
          if (e.pop_id == data.id) {
            var newObj = {
              id: e.id,
              device_name: e.device_name,
              qty: e.qty,
              remark: e.remark,
            }
            devices.value.push(newObj);
          }
        });
      }
    }
    function cancel() {
      resetForm();
      show.value = false;
      editMode.value = false;


    }
    function save() {
      if (!editMode.value) {
        form._method = "POST";
        form.devices = devices;
        form.post("/pop", {
          preserveState: true,
          onSuccess: (page) => {
            show.value = false;
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
        form.devices = devices;
        form.put("/pop/" + form.id, {
          preserveState: true,
          onSuccess: (page) => {
            show.value = false;
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
    function searchPort() {
      router.get('/pop/', { keyword: search.value }, { preserveState: true })
    }
    const onInputLocation = (event) => {
      // Allow only valid format "12.3423423,19.324324"
      const regex = /^-?\d+(\.\d+)?,-?\d+(\.\d+)?$/;
      const input = event.target.value;

      // If the input matches the format, update the location value
      if (regex.test(input)) {
        console.log(input);
        form.site_location = input;
      }
    };
    function deleteNode() {
      let data = Object({});
      data.id = form.id;
      data._method = "DELETE";
      router.post("/pop/" + data.id, data, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: (page) => {
          form.id = null;
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

    return { show, form, editMode, search, devices, onInputLocation, cancel, deleteNode, confirmDelete, searchPort, save, edit };
  },
};
</script>

<style></style>