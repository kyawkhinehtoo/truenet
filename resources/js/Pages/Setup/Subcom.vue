<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">
        Subcom Setup
      </h2>
    </template>

    <div class="py-2">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between space-x-2 items-end mb-2 px-1 md:px-0">
          <div class="relative flex flex-wrap z-0">
            <span
              class="z-10 h-full leading-snug font-normal absolute text-center text-gray-300  bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-3"><i
                class="fas fa-search"></i></span>
            <input type="text" placeholder="Search here..."
              class="border-0 px-3 py-3 placeholder-gray-300 text-gray-600 relative  bg-white rounded text-sm shadow outline-none focus:outline-none focus:ring w-full pl-10"
              id="search" v-model="search" v-on:keyup.enter="searchTsp">
          </div>
          <button @click="openModal"
            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
            Create</button>
        </div>
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg" v-if="subcoms.data">
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
                  Contact Person</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Status</th>

                <th scope="col" class="relative px-6 py-3"><span class="sr-only">Action</span></th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="(row, index) in subcoms.data " v-bind:key="row.id">
                <td class="px-6 py-3 whitespace-nowrap">{{ subcoms.from + index }}</td>
                <td class="px-6 py-3 whitespace-nowrap">{{ row.name }}</td>
                <td class="px-6 py-3 whitespace-nowrap">{{ row.contact_person }}</td>
                <td class="px-6 py-3 whitespace-nowrap">{{ (row.disabled) ? "Disabled" : "Enabled" }}</td>
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
              <!-- This element is to trick the browser into centering the modal contents. -->
              <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>​
              <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
                role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                <form @submit.prevent="submit">
                  <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="">
                      <div class="mb-4">
                        <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Name :</label>
                        <input type="text"
                          class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                          id="name" placeholder="Enter Name" v-model="form.name">
                        <div v-if="$page.props.errors.name" class="text-red-500">{{ $page.props.errors.name }}</div>

                      </div>
                      <div class="mb-4">
                        <label for="contact_person" class="block text-gray-700 text-sm font-bold mb-2">Contact Person
                          :</label>
                        <input type="text"
                          class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                          id="contact_person" placeholder="Enter Contact Person Name" v-model="form.contact_person">
                        <div v-if="$page.props.errors.contact_person" class="text-red-500">{{
                          $page.props.errors.contact_person }}</div>

                      </div>
                      <div class="mb-4">
                        <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email :</label>
                        <input type="email"
                          class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                          id="email" placeholder="Enter Email" v-model="form.email">
                        <div v-if="$page.props.errors.email" class="text-red-500">{{ $page.props.errors.email }}</div>

                      </div>
                      <div class="mb-4">
                        <label for="phone" class="block text-gray-700 text-sm font-bold mb-2">Phone :</label>
                        <input type="text"
                          class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                          id="phone" placeholder="Enter Phone No." v-model="form.phone">
                        <div v-if="$page.props.errors.phone" class="text-red-500">{{ $page.props.errors.phone }}</div>

                      </div>
                      <div class="mb-4">
                        <label for="disabled" class="block text-gray-700 text-sm font-bold mb-2">Disabled :</label>
                        <input
                          class="text-indigo-500 w-6 h-6 mr-2 focus:ring-indigo-400 focus:ring-opacity-25 border border-gray-300 rounded"
                          type="checkbox" v-model="form.disabled" />
                      </div>
                      <label for="rate" class="block text-gray-700 text-md font-bold">Installation Rate </label>
                      <div class="mb-4">
                        <div v-for="(rule, index) in form.rate" :key="index" class="rule inline-flex items-center"
                          v-if="form.rate">

                          <label class="block text-gray-700 text-sm font-bold">Range:</label>
                          <input type="text" v-model="rule.range" pattern="^(<\d+|>\d+|\d+-\d+)$"
                            placeholder="e.g., <200"
                            class="focus:ring-indigo-500 focus:border-indigo-500 block w-full m-4 shadow-sm sm:text-sm border-gray-300 rounded-md">

                          <label class="block text-gray-700 text-sm font-bold">Rate:</label>
                          <input type="number" v-model="rule.rate" placeholder="e.g., 25000"
                            class="focus:ring-indigo-500 focus:border-indigo-500 block w-full m-4 shadow-sm sm:text-sm border-gray-300 rounded-md">

                          <button type="button" @click="removeRule(index)" class="btn"><i
                              class="fas fa-minus-circle text-yellow-600"></i></button>
                        </div>

                        <button type="button" @click="addRule" class="btn"><i
                            class="fas fa-plus-circle text-indigo-600"></i></button>
                      </div>

                    </div>
                  </div>
                  <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                      <button wire:click.prevent="submit" type="submit"
                        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"
                        v-show="!editMode">
                        Save
                      </button>
                    </span>
                    <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                      <button wire:click.prevent="submit" type="submit"
                        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"
                        v-show="editMode">
                        Update
                      </button>
                    </span>
                    <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">

                      <button @click="closeModal" type="button"
                        class="inline-flex justify-center w-full rounded-md border border-gray-300 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                        Cancel
                      </button>
                    </span>
                  </div>
                </form>

              </div>
            </div>
          </div>
        </div>
        <span v-if="subcoms.links">
          <pagination class="mt-6" :links="subcoms.links" />
        </span>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout';
import Pagination from '@/Components/Pagination';
import { reactive, ref } from 'vue';
import { router } from '@inertiajs/vue3'
export default {
  name: 'Subcom',
  components: {
    AppLayout,
    Pagination
  },
  props: {
    subcoms: Object,
    errors: Object
  },
  setup(props) {
    const rateRules = ref([
      { range: '1-200', rate: 25000 },
      { range: '201-300', rate: 30000 }
      // You can add more initial rules if needed
    ]);
    const form = reactive({
      id: null,
      name: null,
      contact_person: null,
      email: null,
      phone: null,
      disabled: 0,
      rate: rateRules.value
    });

    const addRule = () => {
      form.rate.push({ range: '', rate: '' });
    };

    const removeRule = (index) => {
      form.rate.splice(index, 1);
    };
    const parseRange = (range) => {
      const parts = range.split('-');
      if (parts.length === 1) {
        const value = parseInt(parts[0].replace('<', '').trim(), 10);
        return [0, value];
      } else {
        const min = parseInt(parts[0].trim(), 10);
        const max = parseInt(parts[1].trim(), 10);
        return [min, max];
      }
    };
    const search = ref('')
    let editMode = ref(false)
    let isOpen = ref(false)

    function resetForm() {
      form.name = null
      form.contact_person = null
      form.email = null
      form.phone = null
      form.disabled = 0
      form.rate = rateRules.value
    }
    function submit() {
      if (!editMode.value) {
        form._method = 'POST';
        router.post('/subcom', form, {
          preserveState: true,
          onSuccess: (page) => {
            closeModal()
            resetForm()
            Toast.fire(
              {
                icon: 'success',
                title: page.props.flash.message
              }
            )

          },
          onError: (errors) => {
            closeModal()
            console.log('error ..'.errors)
          }
        });
      } else {

        form._method = 'PUT'; form._method = 'PUT';

        router.post('/subcom/' + form.id, form, {
          onSuccess: (page) => {
            closeModal()
            resetForm()
            Toast.fire(
              {
                icon: 'success',
                title: page.props.flash.message
              })
          },

          onError: (errors) => {
            closeModal()
            console.log('error ..'.errors)
          }

        })

      }

    }
    function isJsonString(str) {
      try {
        JSON.parse(str);
      } catch (e) {
        return false;
      }
      return true;
    }
    function edit(data) {
      form.id = data.id
      form.name = data.name
      form.contact_person = data.contact_person
      form.email = data.email
      form.phone = data.phone
      form.disabled = (data.disabled === 1) ? true : false;
      form.rate = (isJsonString(data.rate)) ? JSON.parse(data.rate) : rateRules.value;
      editMode.value = true
      openModal()
    }

    function deleteRow(data) {
      if (!confirm('Are you sure want to remove?')) return;
      data._method = 'DELETE';
      router.post("/subcom/" + data.id, data, {
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
      closeModal()
      resetForm()
    }
    function openModal() {
      isOpen.value = true
    }
    const closeModal = () => {
      isOpen.value = false
      resetForm()
      editMode.value = false
    }
    const searchTsp = () => {
      console.log('search value is' + search.value)
      router.get('/subcom/', { subcom: search.value }, { preserveState: true })
    }

    return {
      form, submit, editMode, isOpen, openModal, closeModal, edit, deleteRow, searchTsp, search,
      rateRules,
      addRule,
      removeRule,
      parseRange
    }
  },




}
</script>
