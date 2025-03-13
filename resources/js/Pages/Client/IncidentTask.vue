<template>
  <app-layout>
    <div class="py-1">

      <div class="mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-2 py-2 w-full justify-items-end" v-if="!edit">
          <div class="flex w-full">
            <span
              class="z-10 mt-1 leading-snug font-normal text-center text-blueGray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
              <i class="fas fa-search"></i>
            </span>
            <input type="text" placeholder="ID/Description"
              class="pl-10 pr-12 py-2.5 w-full rounded-lg overflow-hidden text-sm text-litepie-secondary-700 placeholder-litepie-secondary-400 transition-colors bg-white border border-litepie-secondary-300 focus:border-litepie-primary-300 focus:ring focus:ring-litepie-primary-500 focus:ring-opacity-10 focus:outline-none dark:bg-litepie-secondary-800 dark:border-litepie-secondary-700 dark:text-litepie-secondary-100 dark:placeholder-litepie-secondary-500 dark:focus:border-litepie-primary-500 dark:focus:ring-opacity-20"
              id="search" tabindex="1" v-model="search" @keyup.enter="searchTask" />
          </div>
          <div class="flex w-full">
            <button
              class=" w-full btn py-3 px-8 bg-gray-400 rounded rounded-r-none focus:ring-0 focus:border-transparent focus:outline-none focus:shadow-inner  text-gray-50 text-xs font-semibold uppercase"
              :class="{ 'bg-yellow-300 text-gray-600 font-bold': search_status == 1 }" @click="goWIP">WIP</button>
            <button
              class=" w-full  btn py-3 px-8 bg-gray-400  focus:ring-0 focus:border-transparent focus:outline-none focus:shadow-inner text-gray-50 text-xs font-semibold uppercase"
              :class="{ 'bg-green-300 text-gray-600 font-bold': search_status == 2 }"
              @click="goCompleted">Completed</button>
            <button
              class=" w-full  btn py-3 px-8 bg-gray-400 rounded rounded-l-none focus:ring-0 focus:border-transparent focus:outline-none focus:shadow-inner text-gray-50 text-xs font-semibold uppercase"
              :class="{ 'bg-indigo-600 font-bold': search_status == 'all' }" @click="goAll">All</button>
          </div>
          <div class="flex w-full">
            <button
              class="w-full btn py-3 px-8 bg-gray-400 rounded rounded-r-none focus:ring-0 focus:border-transparent focus:outline-none focus:shadow-inner  text-gray-50 text-xs font-semibold uppercase"
              :class="{ 'bg-yellow-300 text-gray-600 font-bold': task_user == 'my' }" @click="goMyTasks">My
              Tasks</button>
            <button
              class="w-full btn py-3 px-8 bg-gray-400 rounded rounded-l-none focus:ring-0 focus:border-transparent focus:outline-none focus:shadow-inner text-gray-50 text-xs font-semibold uppercase"
              :class="{ 'bg-indigo-600 font-bold': task_user == 'all' }" @click="goAllTasks">All</button>
          </div>
        </div>

        <div v-if="!edit">
          <div v-if="tasks?.data" class="hidden md:block">
            <table class="min-w-full divide-y divide-gray-200 table-auto">
              <thead class="bg-gray-50">
                <tr>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider ">
                  </th>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider ">
                  </th>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Customer ID</th>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Description</th>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Assigned To</th>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Assigned By</th>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Target Date</th>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Status</th>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Action</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200 text-sm   w-full text-left">
                <tr v-for="(row, index) in tasks.data" v-bind:key="row.id">
                  <td class="px-2 py-3 whitespace-nowrap">{{ (index += tasks.from) }}</td>
                  <td class="px-6 py-3 whitespace-nowrap w-1/12"><input type="checkbox" name="status"
                      :checked="row.status == 2" @click="completeTask(row)" :disabled="!permission[0].write_incident" />
                  </td>
                  <td class="px-6 py-3 whitespace-nowrap">{{ row.ftth_id }}</td>
                  <td class="px-6 py-3 whitespace-nowrap">{{ row.description?.substring(0, 50) }}</td>
                  <td class="px-6 py-3 whitespace-nowrap">{{ getName(row.assigned) }}</td>
                  <td class="px-6 py-3 whitespace-nowrap">{{ row.incharge.match(/\b\w/g).join("") }}</td>
                  <td class="px-6 py-3 whitespace-nowrap">{{ row.target }}</td>
                  <td class="px-6 py-3 whitespace-nowrap">{{ getStatus(row.status) }}</td>
                  <td class="px-6 py-3 whitespace-nowrap"><a href="#" @click="editTask(row)" class="text-blue-600"><i
                        class="fa fa-edit"></i></a></td>

                </tr>
              </tbody>
            </table>
          </div>
          <div v-if="tasks?.data" class="grid grid-cols-1 sm:grid-cols-2 gap-2 md:hidden">
            <div class="bg-white p-4 rounded-lg shadow mt-2 space-y-1 text-gray-600" v-for="(row, index) in tasks.data"
              v-bind:key="row.id">
              <div class="flex items-center justify-between space-x-2 text-sm">
                <label class="font-bold">
                  {{ row.ftth_id }}
                </label>

                <div>
                  <span class="text-xs font-bold px-2.5 py-0.5 rounded items-center justify-center "
                    :class="{ 'bg-yellow-400': row.status == 1, 'bg-green-300': row.status != 1 }">
                    {{ getStatus(row.status) }}</span>
                </div>
              </div>
              <div class="text-sm text-gray-600">
                <label for="details" class="w-full font-bold">
                  Description
                </label>
                <p class="font-sm p-2 capitalize">
                  {{ row.description }}
                </p>
              </div>
              <span class="mt-4">
                <div class="text-xs text-gray-700">
                  Assigned To : {{ row.assigned_to }}
                </div>
                <div class="text-xs text-gray-700">
                  Assigned By : {{ row.incharge }}
                </div>
                <div class="text-xs text-gray-700">
                  Target Date : {{ row.target }}
                </div>
              </span>

              <div class="flex flex-wrap justify-end ">
                <div>
                  <a type="button" @click="editTask(row)" href="#"
                    class="bg-green-100 text-green-800 text-xs font-medium  px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-30 items-center justify-center ">
                    <span class="text-sm">Edit</span>
                    <i class="fa fa-angle-double-right fa-lg ml-2" aria-hidden="true"></i>
                  </a>

                </div>

              </div>
            </div>

          </div>

          <span v-if="tasks.total" class="w-full block mt-4">
            <label class="text-xs text-gray-600">{{ tasks.data.length }} Tasks in Current Page. Total Number of
              Tasks : {{ tasks.total }}</label>
          </span>
          <span v-if="tasks.links">
            <pagination class="mt-4" :links="tasks.links" />
          </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-2 w-full bg-white p-4" v-if="edit">
          <div class="py-2 col-span-1 w-full ">
            <div class="flex md:justify-end">
              <label for="assigned" class="block text-sm font-medium text-gray-700 md:mt-2 md:mr-2"> Customer ID :
              </label>
            </div>
          </div>
          <div class="md:py-2 md:col-span-3 col-span-1">
            <div class="flex">
              <label for="ftth_id" class="block text-sm font-bold text-gray-700 md:mt-2 md:mr-2">
                {{ form.data?.ftth_id }}
              </label>
            </div>
          </div>
          <div class="py-2 col-span-1 w-full ">
            <div class="flex md:justify-end">
              <label for="assigned" class="block text-sm font-medium text-gray-700 md:mt-2 md:mr-2"> Ticket Type :
              </label>
            </div>
          </div>
          <div class="md:py-2 md:col-span-3 col-span-1">
            <div class="flex">
              <label for="type" class="block text-sm font-bold text-gray-700 md:mt-2 md:mr-2 capitalize">
                {{ form.data?.type.replace("_", " ") }}
              </label>
            </div>
          </div>
          <div class="py-2 col-span-1 w-full" v-if="form.data?.topic">
            <div class="flex md:justify-end">
              <label for="assigned" class="block text-sm font-medium text-gray-700 md:mt-2 md:mr-2"> Ticket Topic :
              </label>
            </div>
          </div>
          <div class="md:py-2 md:col-span-3 col-span-1" v-if="form.data?.topic">
            <div class="flex">
              <label for="type" class="block text-sm font-bold text-gray-700 md:mt-2 md:mr-2 capitalize">
                {{ form.data?.topic.replace("_", " ") }}
              </label>
            </div>
          </div>
          <div class="py-2 col-span-1 w-full">
            <div class="flex md:justify-end">
              <label for="assigned" class="block text-sm font-medium text-gray-700 md:mt-2 md:mr-2"> Ticket Detail :
              </label>
            </div>
          </div>
          <div class="md:py-2 md:col-span-3 col-span-1">
            <div class="flex">
              <label for="type" class="block text-sm font-bold text-gray-700 md:mt-2 md:mr-2  whitespace-normal">
                {{ form.data?.incident_description }}
              </label>
            </div>
          </div>
          <div class="py-2 col-span-1 w-full">
            <div class="flex md:justify-end">
              <label for="assigned" class="block text-sm font-medium text-gray-700 md:mt-2 md:mr-2"> Ticket Opened At :
              </label>
            </div>
          </div>
          <div class="md:py-2 md:col-span-3 col-span-1">
            <div class="flex">
              <label for="type" class="block text-sm font-bold text-gray-700 md:mt-2 md:mr-2  whitespace-normal">
                {{ form.data?.date }} : {{ form.data?.time }}
              </label>
            </div>
          </div>
          <div class="py-2 col-span-1 w-full ">
            <div class="flex md:justify-end">
              <label for="assigned" class="block text-sm font-medium text-gray-700 md:mt-2 md:mr-2"> Assigned : </label>
            </div>
          </div>
          <div class="md:py-2 md:col-span-3 col-span-1">
            <div class="flex rounded-md shadow-sm">
              <div class="flex rounded-md shadow-sm w-full" v-if="noc.length !== 0">
                <multiselect deselect-label="Selected already" :options="noc" track-by="id" label="name"
                  v-model="form.assigned" :allow-empty="false" :multiple="true"></multiselect>
              </div>
              <p v-if="$page.props.errors.assigned" class="mt-2 text-sm text-red-500">{{ $page.props.errors.assigned }}
              </p>
            </div>
          </div>
          <div class="py-2 col-span-1 sm:col-span-1">
            <div class="flex md:justify-end">
              <label for="target" class="block text-sm font-medium text-gray-700 md:mt-2 md:mr-2"> Target : </label>
            </div>
          </div>
          <div class="md:py-2 col-span-1 md:col-span-3">
            <div class="flex rounded-md shadow-sm">
              <input type="date" v-model="form.target" name="target" id="target"
                class="form-input focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" />

            </div>
            <p v-if="$page.props.errors.target" class="mt-2 text-sm text-red-500">{{ $page.props.errors.target }}</p>
          </div>
          <div class="py-2 col-span-1 sm:col-span-1">
            <div class="flex md:justify-end">
              <label for="description" class="block text-sm font-medium text-gray-700 md:mt-2 md:mr-2"> Description :
              </label>
            </div>
          </div>
          <div class="md:py-2 col-span-1 md:col-span-3">
            <div class="flex rounded-md shadow-sm">
              <textarea v-model="form.description" name="description" id="description"
                class="form-input focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"></textarea>

            </div>
            <p v-if="$page.props.errors.description" class="mt-2 text-sm text-red-500">{{ $page.props.errors.description
              }}
            </p>
          </div>
          <div class="py-2 col-span-1 sm:col-span-1">
            <div class="flex md:justify-end">
              <label for="description" class="block text-sm font-medium text-gray-700 md:mt-2 md:mr-2"> Status :
              </label>
            </div>
          </div>
          <div class="md:py-2 col-span-1 md:col-span-3">
            <div class="flex rounded-md shadow-sm">
              <select v-model="form.status"
                class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300">
                <option value="1">WIP</option>
                <option value="2">Completed</option>
                <option value="0">Deleted</option>
              </select>

            </div>
            <p v-if="$page.props.errors.status" class="mt-2 text-sm text-red-500">{{ $page.props.errors.status }}</p>
          </div>
          <div class="col-span-1 md:col-span-4 flex md:justify-end justify-between mt-4">

            <a href="#" @click="saveTask()"
              class="text-center px-4 py-3 bg-green-500 border border-transparent rounded-sm font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-400 active:bg-green-600 focus:outline-none focus:border-gray-900 disabled:opacity-25 transition mr-1"><span
                v-if="!editMode">Save Task</span><span v-if="editMode">Update Task</span><i
                class="fas fa-save opacity-75 ml-1 text-sm"></i></a>
            <a href="#" @click="cancelTask()"
              class="text-center px-4 py-3 bg-gray-500 border border-transparent rounded-sm font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-400 active:bg-gray-600 focus:outline-none focus:border-gray-900 disabled:opacity-25 transition">Cancel<i
                class="fas fa-save opacity-75 ml-1 text-sm"></i></a>
          </div>
        </div>
      </div>
    </div>

    <div v-if="loading" wire:loading
      class="fixed top-0 left-0 right-0 bottom-0 w-full h-screen z-50 overflow-hidden bg-gray-700 opacity-75 flex flex-col items-center justify-center">
      <div class="loader ease-linear rounded-full border-4 border-t-4 border-gray-200 h-12 w-12 mb-4"></div>
      <h2 class="text-center text-white text-xl font-semibold">Loading...</h2>
      <p class="w-1/3 text-center text-white">This may take a few seconds, please don't close this page.</p>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/IncidentLayout";
import NoData from "@/Components/NoData";
import Pagination from "@/Components/Pagination";
import { ref, onMounted, reactive, inject ,provide} from "vue";
import Multiselect from "@suadelabs/vue3-multiselect";
import { router, Link } from "@inertiajs/vue3";
export default {
  name: "IncidentTask",
  components: {
    AppLayout,
    Pagination,
    Multiselect,
    NoData,
    Link,
  },
  props: {
    tasks: Object,
    errors: Object,
    permission: Object,
    user: Object,
    noc: Object,
    critical: Number,
    high: Number,
    normal: Number,
    role: Object,
  },
  setup(props) {
    const search = ref("");
    const sort = ref("");
    let loading = ref(false);
    let edit = ref(false);
    let editMode = ref(false);
    let selected_id = ref("");
    let search_status = ref(1);
    let task_user = ref("my");
    const formatter = ref({
      date: "YYYY-MM-DD",
      month: "MMM",
    });
    let isOpen = ref(false);
    provide("user", props.user);

    const form = reactive({
      id: null,
      assigned: null,
      target: null,
      status: 1,
      description: null,
      incident_id: null,
      data: null,
    });
    function resetForm() {
      form.id = null;
      form.assigned = null;
      form.target = null;
      form.status = 1;
      form.description = null;
      form.data = null;
      form.incident_id = props.data
    }
    function editTask(data) {

      form.id = data.id;
      form.assigned = isJsonString(data.assigned) ? JSON.parse(data.assigned) : props.noc.filter((d) => d.id == data.assigned)[0];
      form.description = data.description;
      form.incident_id = data.incident_id;
      form.status = data.status;
      form.target = data.target;
      form.data = data;
      editMode.value = true;
      edit.value = true;
    }
    function completeTask(data) {
      if (data.status != 2) {
        Toast.fire({
          title: "Are you sure?",
          text: "Do you want to mark as completed !",
          icon: "warning",
          timer: false,
          position: "center",
          showCancelButton: true,
          dangerMode: true,
        }).then((x) => {
          if (x.isConfirmed) {
            completeIt(data, 2)
          }
        });
      } else {
        Toast.fire({
          title: "Are you sure?",
          text: "Do you want to mark as WIP !",
          icon: "warning",
          timer: false,
          position: "center",
          showCancelButton: true,
          dangerMode: true,
        }).then((x) => {
          if (x.isConfirmed) {
            completeIt(data, 1)
          }
        });
      }

    }
    function completeIt(data, status) {
      form.id = data.id;
      form.assigned = data.assigned;
      form.description = data.description;
      form.incident_id = data.incident_id;
      form.status = status;
      form.target = data.target;
      editMode.value = true;
      edit.value = false;
      saveTask();
    }
    function saveTask() {
      if (editMode.value != true) {
        form._method = "POST";
        router.post("/addTask", form, {
          preserveState: true,
          onSuccess: (page) => {
            edit.value = false;
            resetForm();
            Toast.fire({
              icon: "success",
              title: page.props.flash.message,
            });
          },
          onError: (errors) => {
            console.log("error .." + errors);
          },
        });
      } else {
        form._method = "PUT";
        router.post("/editTask/" + form.id, form, {
          onSuccess: (page) => {
            edit.value = false;
            editMode.value = false;
            resetForm();
            Toast.fire({
              icon: "success",
              title: page.props.flash.message,
            });
          },
          onError: (errors) => {
            console.log("error .." + errors);
          },
        });
      }
    }
    function cancelTask() {
      edit.value = false;
      preserveState = true;
    }
    function getStatus(data) {
      let status = "WIP";
      if (data == 0) {
        status = "Deleted";
      } else if (data == 1) {
        status = "WIP";
      } else {
        status = "Completed";
      }
      return status;
    }
    function searchTask() {
      let url = "/mytask/";
      router.get(url, { keyword: search.value, status: search_status.value, task: task_user.value }, { preserveState: true });
    }
    function changeStatus() {
      let url = "/mytask/";
      if (search.value != null) {
        url = url + "?keyword=" + search.value;
      }
      if (search_status.value != "") {
        url = url + "&status=" + search_status.value;
      }
      if (task_user.value != "") {
        url = url + "&task=" + task_user.value;
      }
      router.get(url, { keyword: search.value }, { preserveState: true });
    }
    function goWIP() {
      search_status.value = 1;
      changeStatus();
    }
    function goCompleted() {
      search_status.value = 2;
      changeStatus();
    }
    function goAll() {
      search_status.value = 'all';
      changeStatus();
    }

    function goMyTasks() {
      task_user.value = 'my';
      changeStatus();
    }
    function goAllTasks() {
      task_user.value = 'all';
      changeStatus();
    }
    const getName = (data) => {
      if (!isJsonString(data)) {
        let assign = Object(props.noc.filter((x) => x.id == data)[0]);
        return assign.name?.match(/\b\w/g).join("");
      } else {
        let assign = JSON.parse(data);
        console.log(assign);
        if (!Array.isArray(assign) && assign instanceof Object) {
          return assign.name?.match(/\b\w/g).join("");
        } else {
          let name = '' + assign.map((e) => {
            console.log(e.name);
            return e.name?.match(/\b\w/g).join("");
          });
          console.log(name);
          return name;
        }

      }
    }
    function isJsonString(str) {
      console.log(str);
      try {
        const parsedValue = JSON.parse(str);
        return typeof parsedValue === 'object' && parsedValue !== null;
      } catch (e) {
        console.log('not json');
        return false;
      }

    }

    return { search, loading, form, formatter, edit, editMode, search_status, task_user, getName, getStatus, editTask, completeIt, saveTask, cancelTask, completeTask, searchTask, goWIP, goCompleted, goAll, goMyTasks, goAllTasks, changeStatus };
  },
};
</script>