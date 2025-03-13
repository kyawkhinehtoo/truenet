<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">Announcement List</h2>
    </template>

    <div class="py-2">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <Link href="/announcement"
          class="ml-2 cursor-pointer inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">Create
          <i class="ml-1 fa fa-plus text-white"></i></Link>

        <div class="bg-white overflow-auto shadow-xl sm:rounded-lg" v-if="announcements.data">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  No
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Name</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Type</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Created At</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  View Detail</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  View Log</th>


              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 text-sm">
              <tr v-for="(row, index) in announcements.data" v-bind:key="row.id">
                <td class="px-6 py-3 text-xs font-medium whitespace-nowrap">{{ announcements.from + index }}
                </td>
                <td class="px-6 py-3 text-xs font-medium whitespace-nowrap">{{ row.name }}</td>
                <td class="px-6 py-3 text-xs font-medium whitespace-nowrap">{{ row.announcement_type }}</td>
                <td class="px-6 py-3 text-xs font-medium whitespace-nowrap">{{ row.created_at }}</td>
                <td class="px-6 py-3 text-xs font-medium whitespace-nowrap"><Link
                    :href="route('announcement.detail', row.id)">Detail</Link> </td>
                <td class="px-6 py-3 text-xs font-medium whitespace-nowrap"><Link
                    :href="route('announcement.log', row.id)">View Log</Link></td>

              </tr>
            </tbody>
          </table>
        </div>
        <span v-if="announcements.total" class="w-full block mt-4">
          <label class="text-xs text-gray-600">{{ announcements.data.length }} Announcements in Current Page. Total
            Number
            of Announcements : {{ announcements.total }}</label>
        </span>

        <span v-if="announcements.links">
          <pagination class="mt-4" :links="announcements.links" />
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
import { router, Link } from "@inertiajs/vue3";
import Multiselect from "@suadelabs/vue3-multiselect";
export default {
  name: "Announcement",
  components: {
    AppLayout,
    Pagination,
    Link
  },
  props: {
    announcements: Object,
    errors: Object,
  },
  setup(props) {

    const deleteRow = (data) => {
      if (!confirm("Are you sure want to Delete?")) return;
      data._method = "DELETE";
      router.post("/announcement/" + data.id, data);
    };

    return { deleteRow };
  },
};
</script>
