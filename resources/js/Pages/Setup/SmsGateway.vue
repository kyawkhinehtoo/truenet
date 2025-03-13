<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">SMS Gatweay</h2>
    </template>

    <div class="py-2">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <form @submit.prevent="submit">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
              <div class="">
                <div class="mb-4">
                  <label class="inline-flex">
                    <label for="status" class="text-gray-700 text-sm font-bold mt-1">ENABLE:</label>
                    <input id="status"
                      class="text-green-500 w-6 h-6 ml-2 focus:ring-green-400 focus:ring-opacity-25 border border-gray-300 rounded"
                      type="checkbox" v-model="form.status" />

                  </label>
                </div>
                <div class="mb-4">
                  <label for="sender_id" class="block text-gray-700 text-sm font-bold mb-2">Sender ID:</label>
                  <input type="text"
                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                    id="sender_id" placeholder="Enter Sender ID" v-model="form.sender_id" />
                  <div v-if="$page.props.errors.sender_id" class="text-red-500">{{ $page.props.errors.sender_id }}</div>
                </div>
                <div class="mb-4">
                  <label for="url" class="block text-gray-700 text-sm font-bold mb-2">API URL:</label>
                  <input type="text"
                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                    id="url" placeholder="Enter API URL" v-model="form.api_url" />
                  <div v-if="$page.props.errors.api_url" class="text-red-500">{{ $page.props.errors.api_url }}</div>
                </div>
                <div class="mb-4">
                  <label for="sid" class="block text-gray-700 text-sm font-bold mb-2">API SID:</label>
                  <input type="text"
                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                    id="sid" placeholder="Enter API SID" v-model="form.sid" />
                  <div v-if="$page.props.errors.sid" class="text-red-500">{{ $page.props.errors.sid }}</div>
                </div>
                <div class="mb-4">
                  <label for="token" class="block text-gray-700 text-sm font-bold mb-2">API TOKEN:</label>
                  <input type="text"
                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                    id="token" placeholder="Enter API TOKEN" v-model="form.token" />
                  <div v-if="$page.props.errors.token" class="text-red-500">{{ $page.props.errors.token }}</div>
                </div>
                <div class="mb-4">
                  <label for="single_sms" class="block text-gray-700 text-sm font-bold mb-2">Single SMS URL:</label>
                  <input type="text"
                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                    id="single_sms" placeholder="Enter Single SMS URL" v-model="form.single_sms" />
                  <div v-if="$page.props.errors.single_sms" class="text-red-500">{{ $page.props.errors.single_sms }}</div>
                </div>
                <div class="mb-4">
                  <label for="info" class="block text-gray-700 text-sm font-bold mb-2">Info URL:</label>
                  <input type="text"
                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                    id="info" placeholder="Enter Info URL" v-model="form.info" />
                  <div v-if="$page.props.errors.info" class="text-red-500">{{ $page.props.errors.info }}</div>
                </div>
                <div class="mb-4">
                  <label for="bulk_sms" class="block text-gray-700 text-sm font-bold mb-2">Bulk SMS URL:</label>
                  <input type="text"
                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                    id="bulk_sms" placeholder="Enter Bulk SMS URL" v-model="form.bulk_sms" />
                  <div v-if="$page.props.errors.bulk_sms" class="text-red-500">{{ $page.props.errors.bulk_sms }}</div>
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
  name: "SmsGateway",
  components: {
    AppLayout,
    Pagination,
  },
  props: {
    gateway: Object,
    errors: Object,
  },

  setup(props) {
    // Update the form reactive object in setup
    const form = reactive({
      id: null,
      status: null,
      sender_id: null,
      api_url: null,
      sid: null,
      token: null,
      single_sms: null,
      info: null,
      bulk_sms: null,
    });
    let editMode = ref(false);
    function submit() {
      if (!editMode.value) {
        form._method = "POST";
        router.post("/smsgateway", form, {
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
        router.post("/smsgateway/" + form.id, form, {
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
    // Update the edit function
    function edit(data) {
      editMode.value = true;
      form.id = data.id;
      form.sender_id = data.sender_id;
      form.status = (data.status == 1) ? true : false;
      form.api_url = data.api_url;
      form.sid = data.sid;
      form.token = data.token;
      form.single_sms = data.single_sms;
      form.info = data.info;
      form.bulk_sms = data.bulk_sms;
    }
    onMounted(() => {
      if (props.gateway) {

        edit(props.gateway);
      }
    });
    onUpdated(() => {
      edit(props.gateway);
    });
    return { form, submit, editMode };
  },
};
</script>
