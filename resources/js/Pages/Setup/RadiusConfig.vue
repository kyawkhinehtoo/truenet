<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">Radius Configuration</h2>
    </template>

    <div class="py-2">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <form @submit.prevent="submit">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
              <div class="">
                <div class="mb-4">
                  <label class="inline-flex">
                    <label for="enabled" class="text-gray-700 text-sm font-bold mt-1">ENABLE:</label>
                    <input id="enabled"
                      class="text-green-500 w-6 h-6 ml-2 focus:ring-green-400 focus:ring-opacity-25 border border-gray-300 rounded"
                      type="checkbox" v-model="form.enabled" />

                  </label>
                </div>
                <div class="mb-4">
                  <label for="server_url" class="block text-gray-700 text-sm font-bold mb-2">Server:</label>
                  <input type="text"
                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                    id="server_url" placeholder="Enter Radius Server" v-model="form.server_url" />
                  <div v-if="$page.props.errors.server_url" class="text-red-500">{{ $page.props.errors.server_url }}
                  </div>
                </div>
                <div class="mb-4">
                  <label for="port" class="block text-gray-700 text-sm font-bold mb-2">Port:</label>
                  <input type="text"
                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                    id="port" placeholder="Enter Radius Port" v-model="form.port" />
                  <div v-if="$page.props.errors.port" class="text-red-500">{{ $page.props.errors.port }}</div>
                </div>
                <div class="mb-4">
                  <label for="admin_user" class="block text-gray-700 text-sm font-bold mb-2">Admin User:</label>
                  <input type="text"
                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                    id="admin_user" placeholder="Enter Admin User" v-model="form.admin_user" />
                  <div v-if="$page.props.errors.admin_user" class="text-red-500">{{ $page.props.errors.admin_user }}
                  </div>
                </div>
                <div class="mb-4">
                  <label for="admin_password" class="block text-gray-700 text-sm font-bold mb-2">Admin Password:</label>
                  <input type="text"
                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                    id="admin_password" placeholder="Enter Admin Password" v-model="form.admin_password" />
                  <div v-if="$page.props.errors.admin_password" class="text-red-500">{{
                    $page.props.errors.admin_password }}
                  </div>
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
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import Pagination from "@/Components/Pagination";
import { reactive, ref, onMounted, onUpdated } from "vue";
import { router } from '@inertiajs/vue3';
export default {
  name: "RadiusConfig",
  components: {
    AppLayout,
    Pagination,
  },
  props: {
    config: Object,
    errors: Object,
  },

  setup(props) {
    const form = reactive({
      id: null,
      server_url: null,
      port: null,
      enabled: null,
      admin_user: null,
      admin_password: null,
    });
    let editMode = ref(false);
    function submit() {
      if (!editMode.value) {
        form._method = "POST";
        router.post("/radiusconfig", form, {
          preserveState: true,
          onSuccess: (page) => {
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
        router.post("/radiusconfig/" + form.id, form, {
          onSuccess: (page) => {
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
      editMode.value = true;
      form.id = data.id;
      form.enabled = (data.enabled == 1) ? true : false;
      form.server_url = data.server;
      form.admin_user = data.admin_user;
      form.admin_password = data.admin_password;
      form.port = data.port;
    }


    onMounted(() => {
      if (props.config.length > 0) {

        edit(props.config[0]);
      }
    });
    onUpdated(() => {

    });
    return { form, submit, editMode };
  },
};
</script>
