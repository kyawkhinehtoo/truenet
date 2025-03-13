<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">Menu Setup</h2>
    </template>

    <div class="py-2">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between space-x-2 items-end mb-2 px-1 md:px-0">
          <button @click="() => {
            (show = true), (editMode = false);
          }
            "
            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
            Create
          </button>
        </div>
      </div>
      <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg" v-if="menus">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No.
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Name
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Display
                Name</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">URL
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Font
                Awesome</th>
              <th scope="col" class="relative px-6 py-3"><span class="sr-only">Action</span></th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="(row, index) in menus" v-bind:key="row.id">
              <td class="px-6 py-3 whitespace-nowrap">{{ index + 1 }}</td>
              <td class="px-6 py-3 whitespace-nowrap">{{ row.name }}</td>
              <td class="px-6 py-3 whitespace-nowrap">{{ row.display }}</td>
              <td class="px-6 py-3 whitespace-nowrap">{{ row.url }}</td>
              <td class="px-6 py-3 whitespace-nowrap"><i :class="'fas text-gray-500 ' + row.fa"></i></td>
              <td class="px-6 py-3 whitespace-nowrap text-right text-sm font-medium">
                <a href="#" @click="edit(row)" class="text-indigo-600 hover:text-indigo-900">Edit</a> |
                <a href="#" @click="confirmDelete(row.id)" class="text-red-600 hover:text-red-900">Delete</a>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </app-layout>

  <jet-confirmation-modal :show="menu_id" @close="menu_id = null">
    <template #title> Delete Menu</template>
    <template #content> Are you sure you would like to delete this Menu? </template>
    <template #footer>
      <jet-secondary-button @click="menu_id = null"> Cancel </jet-secondary-button>
      <jet-danger-button class="ml-2" @click="deleteMenu"> Delete </jet-danger-button>
    </template>
  </jet-confirmation-modal>

  <jet-dialog-modal :show="show" @close="show = false">
    <template #title> Add New Menu </template>
    <template #content>
      <div>
        <div v-if="$page.props.errors[0]" class="text-red-500">{{ $page.props.errors[0] }}</div>
        <div class="mt-4 text-sm">
          <jet-input type="text" class="mt-1 block w-full" placeholder="Menu Name" ref="text" v-model="form.name" />
          <jet-input-error :message="form.error" class="mt-2" />

          <jet-input type="text" class="mt-1 block w-full" placeholder="Display Name" ref="text"
            v-model="form.display" />
          <jet-input-error :message="form.error" class="mt-2" />

          <div class="mt-1 flex">
            <label class="flex-auto items-center mt-3"> <input type="radio" class="form-radio h-5 w-5 text-blue-600"
                checked name="type" v-model="form.type" value="route" /><span class="ml-2 text-gray-700">Route</span>
            </label>

            <label class="flex-auto items-center mt-3"> <input type="radio" class="form-radio h-5 w-5 text-green-600"
                name="type" v-model="form.type" value="url" /><span class="ml-2 text-gray-700">URL</span> </label>
          </div>

          <jet-input type="text" class="mt-1 block w-full" placeholder="Menu URL" ref="text" v-model="form.url" />
          <jet-input-error :message="form.error" class="mt-2" />

          <jet-input type="text" class="mt-1 block w-full" placeholder="Font Awesome" ref="text" v-model="form.fa" />
          <jet-input-error :message="form.error" class="mt-2" />
          <div class="mt-2 inline-flex">
            <div class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in">
              <input type="checkbox" name="admin" id="toggle"
                class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer"
                v-model="form.admin" :checked="form.admin == 1" />
              <label for="toggle"
                class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer"></label>
            </div>
            <label for="toggle" class="relative">Admin</label>
          </div>
        </div>
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
export default {
  name: "menu",
  components: {
    AppLayout,
    JetButton,
    JetDialogModal,
    JetSecondaryButton,
    JetDangerButton,
    JetConfirmationModal,
    JetInput,
    JetInputError,
    useForm,
  },
  props: {
    menus: Object,
    errors: Object,
  },

  setup(props) {
    let menu_id = ref(null);
    let show = ref(false);
    let editMode = ref(false);
    const form = useForm({
      id: null,
      name: null,
      display: null,
      url: "#",
      fa: null,
      admin: 0,
      type: "route",
    });
    function confirmDelete(data) {
      menu_id.value = data;
    }
    function resetForm() {
      form.id = null;
      form.name = null;
      form.display = null;
      form.url = "#";
      form.fa = null;
      form.admin = 0;
      form.type = "route";
    }
    function edit(data) {
      form.id = data.id;
      form.name = data.name;
      form.display = data.display;
      form.url = data.url;
      form.fa = data.fa;
      form.type = data.type;
      form.admin = (data.admin == 1) ? true : false;
      show.value = true;
      editMode.value = true;
    }

    function save() {
      if (!editMode.value) {
        form._method = "POST";
        form.post("/menu", {
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
        form.put("/menu/" + form.id, {
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
    function cancel() {
      show.value = false;
      resetForm();
    }
    function deleteMenu() {
      let data = Object({});
      data.id = menu_id.value;
      data._method = "DELETE";

      router.post("/menu/" + data.id, data, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: (page) => {
          menu_id.value = false;
          Toast.fire({
            icon: "success",
            title: page.props.flash.message,
          });
        },
        onError: (errors) => {
          show.value = false;
          console.log("error ..".errors);
        },
      });
    }

    return { menu_id, save, edit, cancel, show, form, editMode, deleteMenu, confirmDelete };
  },
};
</script>

<style scoped>
.toggle-checkbox:checked {
  @apply: right-0 border-green-400;
  right: 0;
  border-color: #68d391;
}

.toggle-checkbox:checked+.toggle-label {
  @apply: bg-green-400;
  background-color: #68d391;
}
</style>