<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">
        Township Setup
      </h2>
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
              id="search" v-model="search" v-on:keyup.enter="searchTsp">
          </div>
          <button @click="openModal"
            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
            Create</button>
        </div>
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg" v-if="townships.data">
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
                  City
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Township</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Short
                  Code</th>
                <th scope="col" class="relative px-6 py-3"><span class="sr-only">Action</span></th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="(row, index) in townships.data " v-bind:key="row.id">
                <td class="px-6 py-3 whitespace-nowrap">{{ townships.from + index }}</td>
                <td class="px-6 py-3 whitespace-nowrap">{{ row.city_name }}</td>
                <td class="px-6 py-3 whitespace-nowrap">{{ row.name }}</td>
                <td class="px-6 py-3 whitespace-nowrap">{{ row.short_code }}</td>
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
                      <div class="mb-4" v-if="cities.length !== 0">
                        <label for="city" class="block text-gray-700 text-sm font-bold mb-2">City:</label>
                        <multiselect deselect-label="Selected already" :options="cities" track-by="id" label="name"
                          v-model="form.city_id" :allow-empty="true"></multiselect>
                        <div v-if="$page.props.errors.city_id" class="text-red-500">{{ $page.props.errors.city_id[0] }}
                        </div>

                      </div>
                      <div class="mb-4">
                        <label for="township" class="block text-gray-700 text-sm font-bold mb-2">Township:</label>
                        <input type="text"
                          class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                          id="township" placeholder="Enter Township" v-model="form.name">
                        <div v-if="$page.props.errors.name" class="text-red-500">{{ $page.props.errors.name[0] }}</div>

                      </div>
                      <div class="mb-4">
                        <label for="short_code" class="block text-gray-700 text-sm font-bold mb-2">Short Code:</label>
                        <input type="text"
                          class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                          id="short_code" placeholder="Enter Short Code" v-model="form.short_code">
                        <div v-if="$page.props.errors.short_code" class="text-red-500">{{
                          $page.props.errors.short_code[0]
                        }}</div>

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
        <span v-if="townships.links">
          <pagination class="mt-6" :links="townships.links" />
        </span>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout';
import Pagination from '@/Components/Pagination';
import { reactive, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import Multiselect from "@suadelabs/vue3-multiselect";
export default {
  name: 'Township',
  components: {
    AppLayout,
    Multiselect,
    Pagination
  },
  //props: ['townships', 'errors'], 
  props: {
    townships: Object,
    cities: Object,
    errors: Object
  },
  setup(props) {

    const form = reactive({
      id: null,
      name: null,
      city_id: null,
      short_code: null
    })
    const search = ref('')
    let editMode = ref(false)
    let isOpen = ref(false)

    function resetForm() {
      form.name = null
      form.city_id = null
      form.short_code = null
    }
    function submit() {
      if (!editMode.value) {
        form._method = 'POST';
        router.post('/township', form, {
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

        router.post('/township/' + form.id, form, {
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
    function edit(data) {
      form.id = data.id
      form.name = data.name
      form.city_id = props.cities.filter((d) => d.id == data.city_id)[0];
      form.short_code = data.short_code
      editMode.value = true
      openModal()
    }

    function deleteRow(data) {
      if (!confirm('Are you sure want to remove?')) return;
      data._method = 'DELETE';
      router.post("/township/" + data.id, data, {
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
      router.get('/township/', { township: search.value }, { preserveState: true })
    }

    return { form, submit, editMode, isOpen, openModal, closeModal, edit, deleteRow, searchTsp, search }
  },




}
</script>
