<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">Announcement Logs : {{ announcement.name }}</h2>
    </template>

    <div class="py-2">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-auto shadow-xl sm:rounded-lg" v-if="logs.data">
          <a @click="doExcel"
            class="cursor-pointer inline-flex items-center px-4 py-2 bg-green-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition">Export
            Excel <i class="ml-1 fa fa-download text-white"></i></a>

          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  No
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  FTTH
                  ID</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Sender</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Template Name</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Type
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Status</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Created At</th>


              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 text-sm">
              <tr v-for="(row, index) in logs.data" v-bind:key="row.id">
                <td class="px-6 py-3 text-xs font-medium whitespace-nowrap">{{ (index += logs.from) }}</td>
                <td class="px-6 py-3 text-xs font-medium whitespace-nowrap">{{ row.ftth_id }}</td>
                <td class="px-6 py-3 text-xs font-medium whitespace-nowrap">{{ row.sender_name }}</td>
                <td class="px-6 py-3 text-xs font-medium whitespace-nowrap">{{ row.announcement_name }}</td>
                <td class="px-6 py-3 text-xs font-medium whitespace-nowrap capitalize">{{ row.type }}</td>
                <td class="px-6 py-3 text-xs font-medium whitespace-nowrap capitalize">{{ row.status }}</td>
                <td class="px-6 py-3 text-xs font-medium whitespace-nowrap">{{ row.date }}</td>


              </tr>
            </tbody>
          </table>
        </div>
        <span v-if="logs.total" class="w-full block mt-4">
          <label class="text-xs text-gray-600">{{ logs.data.length }} Announcements in Current Page. Total Number of
            Announcements : {{ logs.total }}</label>
        </span>

        <span v-if="logs.links">
          <pagination class="mt-4" :links="logs.links" />
        </span>
      </div>
    </div>

  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import Pagination from "@/Components/Pagination";
import { useForm } from "@inertiajs/vue3";
import { onMounted, onUpdated, provide, ref } from "vue";
import { router } from '@inertiajs/vue3';
import Multiselect from "@suadelabs/vue3-multiselect";
export default {
  name: "Announcement",
  components: {
    AppLayout,
    Pagination
  },
  props: {
    logs: Object,
    announcement: Object,
    errors: Object,
  },
  setup(props) {
    const parameter = ref("");
    function doExcel() {
      let parm = Object.create({});
      parm.id = props.announcement.id;
      parameter.value = parm;
      axios.post("/exportAnnouncementLogExcel", parameter.value).then((response) => {
        console.log(response);
        var a = document.createElement("a");
        document.body.appendChild(a);
        a.style = "display: none";
        let blob = new Blob([response.data], { type: "text/csv" }),
          url = window.URL.createObjectURL(blob);
        a.href = url;
        a.download = "AnnouncementLog.csv";
        a.click();
        window.URL.revokeObjectURL(url);
      });
    }
    const deleteRow = (data) => {
      if (!confirm("Are you sure want to Delete?")) return;
      data._method = "DELETE";
      router.post("/announcement/" + data.id, data);
    };

    return { doExcel };
  },
};
</script>
