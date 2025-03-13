<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">SLA Chart</h2>
    </template>

    <div class="py-2">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between space-x-2 items-end mb-2 px-1 md:px-0">
          <div class="relative flex flex-wrap z-0">
            <span
              class="z-10 h-full leading-snug font-normal text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-3"><i
                class="fas fa-search"></i></span>
            <input type="text" placeholder="Search here..."
              class="border-0 px-3 py-3 placeholder-gray-300 text-gray-600 relative  bg-white rounded text-sm shadow outline-none focus:outline-none focus:ring w-full pl-10"
              id="search" v-model="search" v-on:keyup.enter="searchTsp" />
          </div>
          <button @click="openModal"
            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">Create</button>
        </div>
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg" v-if="slas.data">
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
                  Percentage</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Per
                  Week</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Per
                  Month</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Per
                  Year</th>
                <th scope="col" class="relative px-6 py-3"><span class="sr-only">Action</span></th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="(row, index) in slas.data" v-bind:key="row.id">
                <td class="px-6 py-3 whitespace-nowrap">{{ slas.from + index }}</td>
                <td class="px-6 py-3 whitespace-nowrap">{{ row.percentage }}</td>
                <td class="px-6 py-3 whitespace-nowrap">{{ row.weekly }}</td>
                <td class="px-6 py-3 whitespace-nowrap">{{ row.monthly }}</td>
                <td class="px-6 py-3 whitespace-nowrap">{{ row.yearly }}</td>
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
                        <label for="percentage" class="block text-gray-700 text-sm font-bold mb-2">Sla Name:</label>
                        <input type="text"
                          class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                          id="percentage" placeholder="Enter Sla Name" v-model="form.percentage" />
                        <div v-if="$page.props.errors.percentage" class="text-red-500">{{
                          $page.props.errors.percentage[0]
                        }}</div>
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
        <span v-if="slas.links">
          <pagination class="mt-6" :links="slas.links" />
        </span>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout';
import Pagination from '@/Components/Pagination';
import { reactive, ref, onMounted, onUpdated } from 'vue';
import { router } from '@inertiajs/vue3'
export default {
  name: 'Sla',
  components: {
    AppLayout,
    Pagination
  },
  props: {
    slas: Object,
    errors: Object
  },

  setup(props) {
    const wk = ref(0)
    const mo = ref(0)
    const yr = ref(0)
    const form = reactive({
      id: null,
      percentage: null,
    })
    const search = ref('')
    let editMode = ref(false)
    let isOpen = ref(false)

    function resetForm() {
      form.percentage = null
    }
    function submit() {
      if (!editMode.value) {
        form._method = 'POST';
        router.post('/sla', form, {
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

        router.post('/sla/' + form.id, form, {
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
      form.percentage = data.percentage
      editMode.value = true
      openModal()
    }

    function deleteRow(data) {
      if (!confirm('Are you sure want to remove?')) return;
      data._method = 'DELETE';
      router.post('/sla/' + data.id, data)
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
      router.get('/sla/', { sla: search.value }, { preserveState: true })
    }
    function getDay(data) {
      let minutes = (data) % 3600 / 60;
      let hours = (data) % (24 * 3600) / 3600;
      let days = (data) / (24 * 3600);
      let value = null;

      if (data >= 86400) {
        let hrs = days - Math.floor(days)
        days = days - hrs
        hrs = hrs * 24
        let min = hrs - Math.floor(hrs);
        hrs = hrs - min;
        min = Math.round(min * 60)
        value = days + " days, " + hrs + " hour, " + min + " minutes";
      }
      else if (data >= 1440) {
        let min = hours - Math.floor(hours);
        let hr = hours - min;
        min = Math.round(min * 60)
        value = hr + " hour, " + min + " minutes";
      } else {
        value = Math.round(minutes) + " minutes"
      }
      return value;
    }
    function populated() {
      props.slas.data.map(function (x) {
        let a_week = 604800;
        let a_month = 2592000;
        let a_year = 31536000;
        x.weekly = getDay(a_week - (x.percentage / 100) * a_week);
        x.monthly = getDay(a_month - (x.percentage / 100) * a_month);
        x.yearly = getDay(a_year - (x.percentage / 100) * a_year);
      });
    }
    onMounted(() => {
      populated();
    });
    onUpdated(() => {
      populated();
    });
    return { form, submit, editMode, isOpen, openModal, closeModal, edit, deleteRow, searchTsp, search }
  },




}
</script>
