<template>
  <div class="flex w-full justify-end">
    <a v-if="!add && permission[0].write_incident == 1" href="#" @click="addFile()"
      class="-mt-2 mb-2 text-center items-center px-4 py-3 bg-indigo-500 border border-transparent rounded-sm font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-400 active:bg-indigo-600 focus:outline-none focus:border-gray-900 disabled:opacity-25 transition mr-1">Add
      File<i class="fas fa-plus-circle opacity-75 lg:ml-1 text-sm"></i></a>
    <a v-if="add" href="#" @click="closeFile()"
      class="-mt-2 mb-2 text-center items-center px-4 py-3 bg-indigo-500 border border-transparent rounded-sm font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-400 active:bg-indigo-600 focus:outline-none focus:border-gray-900 disabled:opacity-25 transition mr-1">Close<i
        class="fas fa-times-circle opacity-75 lg:ml-1 text-sm"></i></a>
  </div>
  <div v-if="!file_list" wire:loading class=" w-full flex flex-col items-center justify-center">
    <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-purple-500"></div>
    <h2 class="text-center text-gray-600 text-sm font-semibold mt-2">Loading...</h2>
  </div>
  <div v-if="file_list && !add">
    <table class="min-w-full divide-y divide-gray-200 table-auto">
      <thead class="bg-gray-50 w-full flex block table text-left">
        <tr>

          <th scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider w-1/12">ID</th>
          <th scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider w-full">File</th>
          <th scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider w-1/12"> Action
          </th>
        </tr>
      </thead>
      <tbody
        class="bg-white divide-y divide-gray-200 text-sm max-h-64  w-full overflow-auto scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-white block text-left">
        <tr v-for="(row, index) in file_list" v-bind:key="row.id" class="flex">
          <td class="px-6 py-3 whitespace-nowrap tracking-wider w-1/12">{{ index + 1 }}</td>
          <td class="px-6 py-3 whitespace-nowrap tracking-wider w-full"> {{ row.name }} </td>
          <td class="px-6 py-3 whitespace-nowrap tracking-wider text-right w-1/12 mr-4"
            v-if="permission[0].write_incident == 1"><a :href="row.path" target="_blank"><i
                class="fas fa-eye text-indigo-600"></i></a> | <a href="#" @click="deleteFile(row)"
              class="text-red-600"><i class="fa fa-trash"></i></a></td>
          <td class="px-6 py-3 whitespace-nowrap tracking-wider text-right w-1/12 mr-4" v-else><a :href="row.path"
              target="_blank"><i class="fas fa-eye text-indigo-600"></i></a> </td>
        </tr>
      </tbody>
    </table>
  </div>
  <div v-if="add">
    <upload :data="incident_id" @status="checkUpload" />
  </div>

</template>

<script>
import { ref, onMounted, reactive, inject } from "vue";
import Upload from "@/Components/Upload";
import { router } from '@inertiajs/vue3';
export default {
  name: "File",
  components: {
    Upload,
  },
  props: ["data"],
  setup(props) {
    let loading = ref(false);
    let file_list = ref();
    const permission = inject("permission");
    let add = ref(false);

    let incident_id = ref("");

    function addFile() {
      add.value = true;
      incident_id.value = {
        'incident_id': props.data
      };
    }
    function closeFile() {
      add.value = false;
      calculate();
    }
    function deleteFile(data) {
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
          deleteIt(data);

        } else {
          Toast.fire("Your data is safe!");
        }
      });

    }
    function deleteIt(data) {
      data._method = "DELETE";
      router.post("/deleteFile/" + data.id, data, {
        onSuccess(page) {
          Toast.fire({
            icon: "success",
            title: "Your File has been Deleted !",
          });
          calculate();
        }
      });
    }
    const getFile = async () => {
      let url = "/getFile/" + props.data;
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
    const checkUpload = (s) => {
      if (s) {
        add.value = false;
        calculate();
        Toast.fire({
          icon: "success",
          title: "Successfully Uploaded the File",
        });
      }
    }
    const calculate = () => {
      getFile().then((d) => {
        file_list.value = d;
      });
    }
    onMounted(() => {
      calculate();
    });
    return { file_list, add, deleteFile, addFile, closeFile, checkUpload, incident_id, permission, loading };
  },
};
</script>
