<template>
  <div class="flex w-full justify-end">
    <a v-if="!edit && permission[0].write_incident == 1" href="#" @click="newTask()"
      class="-mt-2 mb-2 text-center items-center px-4 py-3 bg-indigo-500 border border-transparent rounded-sm font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-400 active:bg-indigo-600 focus:outline-none focus:border-gray-900 disabled:opacity-25 transition mr-1">Add
      Task<i class="fas fa-plus-circle opacity-75 lg:ml-1 text-sm"></i></a>
    <a v-if="edit && permission[0].write_incident == 1" href="#" @click="saveTask()"
      class="-mt-2 mb-2 text-center items-center px-4 py-3 bg-green-500 border border-transparent rounded-sm font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-400 active:bg-green-600 focus:outline-none focus:border-gray-900 disabled:opacity-25 transition mr-1"><span
        v-if="!editMode">Save Task</span><span v-if="editMode">Update Task</span><i
        class="fas fa-save opacity-75 lg:ml-1 text-sm"></i></a>
    <a v-if="edit" href="#" @click="cancelTask()"
      class="-mt-2 mb-2 text-center items-center px-4 py-3 bg-gray-500 border border-transparent rounded-sm font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-400 active:bg-gray-600 focus:outline-none focus:border-gray-900 disabled:opacity-25 transition mr-1">Cancel<i
        class="fas fa-save opacity-75 lg:ml-1 text-sm"></i></a>
  </div>
  <div v-if="!task_list" wire:loading class=" w-full flex flex-col items-center justify-center">
    <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-purple-500"></div>
    <h2 class="text-center text-gray-600 text-sm font-semibold mt-2">Loading...</h2>
  </div>
  <div v-if="task_list && !edit">
    <table class="min-w-full divide-y divide-gray-200 table-auto">
      <thead class="bg-gray-50 w-full flex flex-col">
        <tr>
          <th scope="col"
            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/12 "></th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-3/5">
            Description</th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">
            Assigned</th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">
            Target</th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/12">
            Status</th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/12">
            Action</th>
        </tr>
      </thead>
      <tbody
        class="bg-white divide-y divide-gray-200 text-sm max-h-64  w-full overflow-auto block scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-white  flex flex-col justify-between text-left">
        <tr v-for="row in task_list" v-bind:key="row.id" class="flex">
          <td class="px-6 py-3 whitespace-nowrap w-1/12"><input type="checkbox" name="status" :checked="row.status == 2"
              @click="completeTask(row)" :disabled="!permission[0].write_incident" /></td>
          <td class="px-6 py-3 whitespace-nowrap w-3/5">{{ (row.description.length > 50) ? row.description.substring(0,
            50)
            + ' ...' : row.description }}</td>
            <td class="px-6 py-3 whitespace-nowrap w-1/4 ">{{ getName(row.assigned) }}</td>
          <td class="px-6 py-3 whitespace-nowrap w-1/6 ">{{ row.target }}</td>
          <td class="px-6 py-3 whitespace-nowrap w-1/12">{{ getStatus(row.status) }}</td>
          <td class="px-6 py-3 whitespace-nowrap w-1/12 " v-if="permission[0].write_incident == 1"><a href="#"
              @click="editTask(row)" class="text-blue-600"><i class="fa fa-edit"></i></a> | <a href="#"
              @click="deleteTask(row)" class="text-red-600"><i class="fa fa-trash"></i></a></td>
          <td class="px-6 py-3 whitespace-nowrap w-1/12 " v-else><a href="#" @click="editTask(row)"
              class="text-blue-600"><i class="fa fa-eye"></i></a> </td>
        </tr>
      </tbody>
    </table>
  </div>
  <div v-if="edit">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-2 w-full">
      <div class="py-2 col-span-1 sm:col-span-1">
        <div class="flex">
          <label for="assigned" class="block text-sm font-medium text-gray-700 mt-2 mr-2"> Assigned : </label>
        </div>
      </div>
      <div class="py-2 col-span-3 sm:col-span-3">
        <div class="flex rounded-md shadow-sm">
          <div class="flex rounded-md shadow-sm w-full" v-if="noc.length !== 0">
            <multiselect deselect-label="Selected already" :options="noc" track-by="id" label="name"
              v-model="form.assigned" :allow-empty="false"  :multiple="true"></multiselect>
          </div>
          <p v-if="$page.props.errors.assigned" class="mt-2 text-sm text-red-500">{{ $page.props.errors.assigned }}</p>
        </div>
      </div>
      <div class="py-2 col-span-1 sm:col-span-1">
        <div class="flex">
          <label for="target" class="block text-sm font-medium text-gray-700 mt-2 mr-2"> Target : </label>
        </div>
      </div>
      <div class="py-2 col-span-3 sm:col-span-3">
        <div class="flex rounded-md shadow-sm">
          <input type="date" v-model="form.target" name="target" id="target"
            class="form-input focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" />

        </div>
        <p v-if="$page.props.errors.target" class="mt-2 text-sm text-red-500">{{ $page.props.errors.target }}</p>
      </div>
      <div class="py-2 col-span-1 sm:col-span-1">
        <div class="flex">
          <label for="description" class="block text-sm font-medium text-gray-700 mt-2 mr-2"> Description : </label>
        </div>
      </div>
      <div class="py-2 col-span-3 sm:col-span-3">
        <div class="flex rounded-md shadow-sm">
          <textarea v-model="form.description" name="description" id="description"
            class="form-input focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"></textarea>

        </div>
        <p v-if="$page.props.errors.description" class="mt-2 text-sm text-red-500">{{ $page.props.errors.description }}
        </p>
      </div>
      <div class="py-2 col-span-1 sm:col-span-1">
        <div class="flex">
          <label for="description" class="block text-sm font-medium text-gray-700 mt-2 mr-2"> Status : </label>
        </div>
      </div>
      <div class="py-2 col-span-3 sm:col-span-3">
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
    </div>
  </div>

</template>

<script>
import { ref, onMounted, reactive, inject } from "vue";
import Multiselect from "@suadelabs/vue3-multiselect";
import { router } from '@inertiajs/vue3';
export default {
  name: "Task",
  components: {
    Multiselect,
  },
  props: ["data"],
  setup(props) {
    const noc = inject("noc");
    const permission = inject("permission");
    let task_list = ref();
    let loading = ref(false);
    let edit = ref(false);
    let editMode = ref(false);
    const form = reactive({
      id: null,
      assigned: null,
      target: null,
      status: 1,
      description: null,
      incident_id: null,
    });
    function newTask() {
      edit.value = true;
      form.incident_id = props.data;
    }
    function resetForm() {
      form.id = null;
      form.assigned = null;
      form.target = null;
      form.status = 1;
      form.description = null;
      form.incident_id = props.data
    }
    function editTask(data) {

      form.id = data.id;
      form.assigned = isJsonString(data.assigned) ? JSON.parse(data.assigned) : noc.filter((d) => d.id == data.assigned)[0];
      form.description = data.description;
      form.incident_id = data.incident_id;
      form.status = data.status;
      form.target = data.target;
      editMode.value = true;
      edit.value = true;
    }
    function deleteTask(data) {
      Toast.fire({
        title: "Are you sure?",
        text: "Once deleted, you will not be able to recover this data !",
        icon: "warning",
        timer: false,
        position: "center",
        showCancelButton: true,
        dangerMode: true,
      }).then((x) => {
        if (x.isConfirmed) {
          deleteIt(data)
        } else {
          Toast.fire("Your data is safe!");
        }
      });

    }
    function deleteIt(data) {
      form.id = data.id;
      form.assigned =isJsonString(data.assigned) ? JSON.parse(data.assigned) : noc.filter((d) => d.id == data.assigned)[0];
      form.description = data.description;
      form.incident_id = data.incident_id;
      form.status = 0;
      form.target = data.target;
      editMode.value = true;
      edit.value = false;
      saveTask();
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
            calculate();
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
        router.post("/editTask/" + form.id, form, {
          onSuccess: (page) => {
            edit.value = false;
            editMode.value = false;
            resetForm();
            calculate();
            Toast.fire({
              icon: "success",
              title: page.props.flash.message,
            });
            closeModal();
          },
          onError: (errors) => {
            console.log("error ..".errors);
          },
        });
      }
    }
    function cancelTask() {
      edit.value = false;
      preserveState = true;
    }

    const getTask = async () => {
      let url = "/getTask/" + props.data;
      console.log(url);
      try {
        const res = await fetch(url);
        const data = await res.json();
        // console.log(data);
        return data;
      } catch (err) {
        console.error(err);
      }
    };
    const getName = (data) => {
      if (!isJsonString(data)) {
        let assign = Object(noc.filter((x) => x.id == data)[0]);
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
    const calculate = () => {
      getTask().then((d) => {
        task_list.value = d;
      });
    }
    onMounted(() => {
      console.log(task_list.value);
      calculate();
    });
    return { loading, task_list, edit, editMode, newTask, saveTask, cancelTask, getName, getStatus, editTask, deleteTask, completeTask, form, noc, permission };
  },
};
</script>
