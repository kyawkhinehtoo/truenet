<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">User Setup</h2>
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
              id="search" v-model="search" v-on:keyup.enter="searchTsp" />
          </div>
          <button @click="openModal"
            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">Create</button>
        </div>
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg" v-if="users.data">
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
                  Name
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Phone
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Role
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Status</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Last
                  Login IP</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Last
                  Login Time</th>
                <th scope="col" class="relative px-6 py-3"><span class="sr-only">Action</span></th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="(row, index) in users.data" v-bind:key="row.id">
                <td class="px-6 py-3 whitespace-nowrap">{{ (index + users.from) }}</td>
                <td class="px-6 py-3 whitespace-nowrap">{{ row.name }}</td>
                <td class="px-6 py-3 whitespace-nowrap">{{ row.phone }}</td>
                <td class="px-6 py-3 whitespace-nowrap">{{ getRole(row.role) }}</td>
                <td class="px-6 py-3 whitespace-nowrap">{{ (row.disabled) ? 'Disabled' : 'Enabled' }}</td>
                <td class="px-6 py-3 whitespace-nowrap">{{ row.last_login_ip }}</td>
                <td class="px-6 py-3 whitespace-nowrap">{{ row.last_login_time }}</td>
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
                  <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="">
                      <div class="mb-4">
                        <label for="name" class="block text-gray-700 text-sm font-bold mb-2">User Name:</label>
                        <input type="text"
                          class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                          id="name" placeholder="Enter User Name" v-model="form.name" required />
                        <p v-if="$page.props.errors.name" class="mt-2 text-sm text-red-500">{{ $page.props.errors.name
                          }}
                        </p>
                      </div>
                      <div class="mb-4">
                        <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
                        <input type="email"
                          class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                          id="email" placeholder="Enter Email Address" v-model="form.email" required />
                        <p v-if="$page.props.errors.email" class="mt-2 text-sm text-red-500">{{ $page.props.errors.email
                          }}
                        </p>
                      </div>
                      <div class="mb-4">
                        <label for="phone" class="block text-gray-700 text-sm font-bold mb-2">Phone Number:</label>
                        <input type="tel" pattern="^[0-9]{0,11}$"
                          class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                          id="phone" placeholder="09123456789" v-model="form.phone" required />

                        <p v-if="$page.props.errors.phone" class="mt-2 text-sm text-red-500">{{ $page.props.errors.phone
                          }}
                        </p>
                      </div>
                      <div class="mb-4">
                        <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Enter Password:</label>
                        <input type="password"
                          class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                          id="password" placeholder="Enter Password" v-model="form.password" />

                        <p v-if="$page.props.errors.password" class="mt-2 text-sm text-red-500">{{
                          $page.props.errors.password }}</p>
                      </div>
                      <div class="mb-4">
                        <label for="confirm_password" class="block text-gray-700 text-sm font-bold mb-2">Confirm
                          Password:</label>
                        <input type="password"
                          :class="'mt-1 focus:ring-' + border + ' focus:border-' + border + ' block w-full shadow-sm sm:text-sm border-gray-300 rounded-md'"
                          id="confirm_password" placeholder="Confirm the Password" v-model="form.confirm_password"
                          v-on:keyup="checkPsw" />
                        <div v-if="$page.props.errors.confirm_password" class="text-red-500">{{
                          $page.props.errors.confirm_password[0] }}</div>
                        <p v-if="psw_err" class="mt-2 text-sm text-red-500">{{ psw_err }}</p>

                      </div>
                      <div class="mb-4">
                        <label for="role" class="block text-gray-700 text-sm font-bold mb-2">User Role:</label>
                        <select name="role" id="role" v-model="form.role"
                          class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                          <option v-for="row in roles" v-bind:key="row.id" v-bind:value="row.id"> {{ row.name }}
                          </option>
                        </select>
                        <div v-if="$page.props.errors.name" class="text-red-500">{{ $page.props.errors.name[0] }}</div>
                      </div>
                    </div>
                    <div class="mb-4">
                      <label for="disabled" class="block text-gray-700 text-sm font-bold mb-2">User Status:</label>
                      <label class="inline-flex ml-2">
                        <input
                          class="text-indigo-500 w-6 h-6 mr-2 focus:ring-indigo-400 focus:ring-opacity-25 border border-gray-300 rounded"
                          type="checkbox" v-model="form.disabled" />
                        Disabled
                      </label>

                      <div v-if="$page.props.errors.disabled" class="text-red-500">{{ $page.props.errors.disabled[0] }}
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
        <span v-if="users.links">
          <pagination class="mt-6" :links="users.links" />
        </span>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import Pagination from "@/Components/Pagination";
import { reactive, ref } from "vue";
import { router } from '@inertiajs/vue3';
export default {
  name: "Project",
  components: {
    AppLayout,
    Pagination,
  },
  props: {
    users: Object,
    roles: Object,
    errors: Object,
  },
  setup(props) {
    const form = reactive({
      id: null,
      name: null,
      password: null,
      confirm_password: null,
      email: null,
      phone: null,
      role: null,
      disabled: false,
    });
    const search = ref("");
    let editMode = ref(false);
    let isOpen = ref(false);
    let psw_err = ref("");
    let border = ref("indigo-500");

    function resetForm() {
      form.name = null;
      form.password = null;
      form.confirm_password = null;
      form.email = null;
      form.phone = null;
      form.role = null;
      form.disabled = false;
    }
    function checkPsw() {
      if (form.password != form.confirm_password) {
        console.log("incorrect psw");
        psw_err.value = "Password does not match !"
        border.value = "red-500"
      } else {
        console.log("correct !");
        psw_err.value = "";
        border.value = "indigo-500"
      }
    }
    function submit() {
      if (!editMode.value) {
        form._method = "POST";
        router.post("/user", form, {
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
        router.post("/user/" + form.id, form, {
          onSuccess: (page) => {
            closeModal();
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
    function edit(data) {
      form.id = data.id;
      form.name = data.name;
      form.email = data.email;
      form.phone = data.phone;
      form.role = data.role;
      form.disabled = (data.disabled == 1) ? true : false;
      editMode.value = true;
      openModal();
    }
    function getRole(data) {
      let r = props.roles.filter((obj) => obj.id == data).map(obj => obj.name)[0];
      console.log(r);
      return r;
    }
    function deleteRow(data) {
      if (!confirm("Are you sure want to remove?")) return;
      data._method = "DELETE";
      router.post("/user/" + data.id, data, {
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
    const closeModal = () => {
      isOpen.value = false;
      resetForm();
      editMode.value = false;
    };
    const searchTsp = () => {
      console.log("search value is" + search.value);
      router.get("/user/", { user: search.value }, { preserveState: true });
    };

    return { form, psw_err, border, getRole, submit, checkPsw, editMode, isOpen, openModal, closeModal, edit, deleteRow, searchTsp, search };
  },
};
</script>
<style scoped>
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

/* Firefox */
input[type=number] {
  -moz-appearance: textfield;
}
</style>