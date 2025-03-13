<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">SMS Template Setup</h2>
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
        <div class="bg-white overflow-auto shadow-xl sm:rounded-lg" v-if="templates">
          <!-- <button @click="openModal" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded my-3">Create New package</button>
                 <input class="w-half rounded py-2 my-3 float-right" type="text" placeholder="Search package" v-model="search" v-on:keyup.enter="searchTsp">
                    -->

          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  No.
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Template Name</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Type</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Default</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Bulk SMS?</th>
                <th scope="col" class="relative px-6 py-3"><span class="sr-only">Action</span></th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="(row, index) in templates.data" v-bind:key="row.id" :class="{ 'text-gray-400': !row.status }">
                <td class="px-6 py-3 text-left text-sm font-medium whitespace-nowrap">{{ templates.from + index }}</td>
                <td class="px-6 py-3 text-left text-sm font-medium whitespace-nowrap">{{ row.name }}</td>
                <td class="px-6 py-3 text-left text-sm font-medium whitespace-nowrap uppercase">{{ row.type }}</td>
                <td class="px-6 py-3 text-left text-sm font-medium whitespace-nowrap">{{ row.item_data }}</td>
                <td class="px-6 py-3 text-left text-sm font-medium whitespace-nowrap">{{ row.is_bulk?"Yes":"No" }}</td>
                <td class="px-6 py-3 text-sm font-medium whitespace-nowrap text-right">
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
                class="inline-block bg-gray-50 align-bottom rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-5xl sm:w-full"
                role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                <form @submit.prevent="submit">
                  <div class="shadow bg-white sm:rounded-md sm:overflow-hidden">
                    <div class="px-4 py-5 bg-white space-y-2 sm:p-6">
                      <div class="py-2">
                        <label for="name" class="block text-sm font-medium text-gray-700"> Enter Template Name </label>
                        <div class="mt-1 rounded-md shadow-sm">
                          <input type="text" v-model="form.name" name="name" id="name"
                            class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-md sm:text-sm border-gray-300"
                            placeholder="Template Name" required />
                          <div v-if="$page.props.errors.name" class="text-red-500">{{ $page.props.errors.name }}</div>

                        </div>
                      </div>

                      <div class="py-2">
                        <label for="name" class="block text-sm font-medium text-gray-700"> Template Type</label>
                        <div class="grid grid-cols-2 gap-2 mt-1">
                          <div >
                            <label class="flex-auto items-center text-sm font-medium text-gray-700 ">SMS</label>
                            <Switch v-model="form.type" :class="form.type ? 'bg-green-700' : 'bg-indigo-700'"
                              class="relative inline-flex items-center h-6 rounded-full w-11 mx-2">
                              <span class="sr-only">Template Type </span>
                              <span :class="form.type ? 'translate-x-6' : 'translate-x-1'"
                                class="inline-block w-4 h-4 transform bg-white rounded-full" />
                            </Switch>
                            <label class="flex-auto items-center text-sm font-medium text-gray-700 ">Email</label>
                          </div>
                          <div v-if="!form.type">
                            
                              <label class="inline-flex items-center">
                                <input type="checkbox" 
                                  class="text-green-500 w-4 h-4 mr-2 focus:ring-green-400 focus:ring-opacity-25 border border-gray-300 rounded"
                                  v-model="form.is_bulk" />
                                <span class="text-sm font-medium text-gray-700">Bulk SMS (No Template variable will be applied)</span>
                              </label>
                            
                          </div>
                        </div>
                       
                      </div>
                      <div class="py-2">
                        <label for="default_name" class="block text-sm font-medium text-gray-700"> Default For </label>
                        <div class="mt-1 rounded-md shadow-sm">
                          <multiselect deselect-label="Selected already" :options="default_names" track-by="key"
                            label="name" v-model="form.default_name" :allow-empty="true"></multiselect>
                          <div v-if="$page.props.errors.default_name" class="text-red-500">{{
                            $page.props.errors.default_name }}</div>
                        </div>
                      </div>
                      <div class="py-2" v-if="form.type">
                        <label for="name" class="block text-sm font-medium text-gray-700"> Emails Title </label>
                        <div class="mt-1 rounded-md shadow-sm">
                          <input type="text" v-model="form.title" name="title" id="title"
                            class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-md sm:text-sm border-gray-300"
                            placeholder="Enter Emails Title" />
                        </div>
                      </div>
                      <div class="py-2" v-if="form.type">
                        <label for="name" class="block text-sm font-medium text-gray-700"> Emails for CC </label>
                        <div class="mt-1 rounded-md shadow-sm">
                          <input type="text" v-model="form.cc_email" name="cc_email" id="cc_email"
                            class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-md sm:text-sm border-gray-300"
                            placeholder="Enter Emails in Comma" />
                        </div>
                      </div>
                      <div class="py-2" v-if="form.type">
                        <label for="type" class="block text-sm font-medium text-gray-700"> Body </label>
                        <div class="mt-1">
                          <QuillEditor theme="snow" v-model:content="form.body" contentType="html" toolbar="full" />
                        </div>
                      </div>
                      <div class="py-2" v-else>
                        <label for="type" class="block text-sm font-medium text-gray-700"> Body </label>
                        <div class="mt-1">
                          <textarea v-model="form.body"
                            class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-md sm:text-sm border-gray-300"
                            v-on:input="updateText"> </textarea>
                          <div v-if="$page.props.errors.body" class="text-red-500">{{ $page.props.errors.body }}</div>
                          <div class="font-normal text-gray-600 text-xs text-right">Total Words: {{ characterCount }} |
                            SMS
                            Per
                            Msg :
                            {{ smsCount }}
                          </div>
                        </div>
                      </div>
                      <div v-if="!form.is_bulk">
                        <h5 class="mb-2 text-md font-bold tracking-tight text-gray-900 dark:text-white w-1/2">General
                          Purpose
                          Announcement Template</h5>
                        <div class="font-normal text-gray-700 dark:text-gray-400 text-sm">
                          <div v-pre>
                            {{customer_name}}, {{ftth_id}}, {{package_name}}, {{package_speed}}, <br />
                            {{package_price}}, {{package_currency}}, {{package_type}}
                          </div>
  
  
                        </div>
                        <h5 class="mb-2 text-md font-bold tracking-tight text-gray-900 dark:text-white w-1/2">
                          Bill Related Template</h5>
                        <div class="font-normal text-gray-700 dark:text-gray-400 text-sm">
  
                          <div v-pre>
                            {{ftth_id}}, {{bill_number}}, {{period_covered}}, {{month}}, {{year}},
                            {{bill_to}},<br />
                            {{attn}}, {{usage_days}}, {{total_payable}}, {{payment_duedate}}, {{url}}
                          </div>
                         
                        </div>
                        <h5 class="mb-2 text-md font-bold tracking-tight text-gray-900 dark:text-white w-1/2">
                          SMS Reminder Template</h5>
                        <div class="font-normal text-gray-700 dark:text-gray-400 text-sm">
  
                          <div v-pre>
                            {{customer_name}}, {{ftth_id}},{{service_off_date}},{{days_remaining}},{{package_name}},{{package_price}}
                          </div>
                         
                        </div>
                      </div>
                      <div v-pre v-else>
                        No Variable Available !
                      </div>

                     


                    </div>


                  </div>
                  <div class="px-4  bg-gray-50 text-right sm:px-6">
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                      <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                        <button wire:click.prevent="submit" type="submit"
                          class="inline-flex justify-center w-full px-4 py-2 bg-gray-800 border border-gray-300 rounded-md font-medium text-base leading-6 sm:text-sm text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150"
                          v-show="!editMode">Save</button>
                      </span>
                      <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                        <button wire:click.prevent="submit" type="submit"
                          class="inline-flex justify-center w-full px-4 py-2 bg-gray-800 border border-gray-300 rounded-md font-medium text-base leading-6 sm:text-sm text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150"
                          v-show="editMode">Update</button>
                      </span>
                      <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
                        <button @click="closeModal" type="button"
                          class="inline-flex justify-center w-full rounded-md border border-gray-300 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5">Cancel</button>
                      </span>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
        <span v-if="templates.links">
          <pagination class="mt-6" :links="templates.links" />
        </span>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import { reactive, ref, watch, onMounted, onUpdated } from "vue";
import { router } from '@inertiajs/vue3';
import { QuillEditor } from "@vueup/vue-quill";
import { Switch } from "@headlessui/vue";
import Multiselect from "@suadelabs/vue3-multiselect";
import Pagination from '@/Components/Pagination';
export default {
  name: "package",
  components: {
    AppLayout,
    Pagination,
    QuillEditor,
    Switch,
    Multiselect
  },
  //props: ['packages', 'errors'],
  props: {
    templates: Object,
    errors: Object,
  },
  setup(props) {
    const search = ref("");
    let editMode = ref(false);
    let isOpen = ref(false);
    const characterCount = ref(0);
    const smsCount = ref(0);

    let default_names = ref([{
      key: 'general',
      name: 'General',
    }, {
      key: 'bill_invoice',
      name: 'Bill Invoice',
    }, {
      key: 'unpaid_reminder',
      name: 'Unpaid Reminder',
    },
    {
      key: 'just_reminder_sms',
      name: 'Just Reminder',
    }]);
    const form = reactive({
      id: null,
      name: null,
      title: null,
      cc_email: null,
      body: null,
      type: ref(false),
      is_bulk: false,
      default_name: default_names.value.filter((d) => d.key == 'general')[0],
    });

    function resetForm() {
      form.id = null;
      form.name = null;
      form.title = null;
      form.cc_email = null;
      form.body = null;
      form.type = ref(false);
      form.is_bulk = false;
      form.default_name = default_names.value.filter((d) => d.key == 'general')[0];
      characterCount.value = 0;
      smsCount.value = 0;
    }
    function submit() {
      if (!editMode.value) {
        form._method = "POST";
        router.post("/template", form, {
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

            console.log("error ..".errors);
          },
        });
      } else {
        form._method = "PUT";
        router.post("/template/" + form.id, form, {
          onSuccess: (page) => {
            form.componentKey++;
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
      form.title = data.title;
      form.cc_email = data.cc_email;
      form.body = data.body;
      form.type = (data.type == 'email') ? true : false;
      form.is_bulk = (data.is_bulk) ? true : false;
      form.default_name = JSON.parse(data.default_name);
      editMode.value = true;
      openModal();
      if (form.body)
        updateText();
    }

    function deleteRow(data) {
      if (!confirm("Are you sure want to remove?")) return;
      data._method = "DELETE";
      router.post("/template/" + data.id, data);
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
    const updateText = () => {
      characterCount.value = form.body.length;
      smsCount.value = Math.ceil(form.body.length / 140);
    };

    onMounted(() => {
      if (props.templates) {
        props.templates.data.map(function (x) {
          let default_name = JSON.parse(x.default_name);
          console.log(default_name);
          x.item_data = `${default_name.name}`;
        });
      }


    });
    onUpdated(() => {
      if (props.templates) {
        props.templates.data.map(function (x) {
          let default_name = JSON.parse(x.default_name);
          x.item_data = `${default_name.name}`;
        });
      }

    });
    return { form, search, submit, editMode, isOpen, openModal, closeModal, edit, deleteRow, default_names, characterCount, updateText, smsCount };
  },
};
</script>
<style scoped>
@import "@vueup/vue-quill/dist/vue-quill.snow.css";

input[type="number"]::-webkit-inner-spin-button {
  opacity: 1;
}

.noborder {
  border: none;
}

/* CHECKBOX TOGGLE SWITCH */
/* @apply rules for documentation, these do not work as inline style */

.toggle-checkbox:checked {
  @apply: right-0 border-green-400;
  right: 0;
  border-color: #68d391;
}

.toggle-checkbox:checked+.toggle-label {
  @apply: bg-green-400;
  background-color: #68d391;
}

.toggle-checkbox:active,
.toggle-checkbox:focus {
  outline: 0px;
  outline-offset: 0px;
  --tw-ring-inset: var(--tw-empty,
      /*!*/
      /*!*/
    );
  --tw-ring-offset-width: 0px;
  --tw-ring-offset-color: #fff;
  --tw-ring-color: rgb(0 0 0 / 6%);
  --tw-ring-offset-shadow: var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);
  --tw-ring-shadow: var(--tw-ring-inset) 0 0 0 calc(2px + var(--tw-ring-offset-width)) var(--tw-ring-color);
  box-shadow: var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow, 0 0 #0000);
}
</style>
