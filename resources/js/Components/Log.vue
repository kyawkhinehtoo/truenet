<template>
<div v-if="!logs" wire:loading class=" w-full flex flex-col items-center justify-center">
              <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-purple-500"></div>
              <h2 class="text-center text-gray-600 text-sm font-semibold mt-2">Loading...</h2>
  </div>
  <div v-if="logs">
  <table class="min-w-full divide-y divide-gray-200 table-auto">
    <thead class="bg-gray-50 w-full flex block table text-left">
      <tr>
        <th scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider w-1/12 flex">No.</th>
        <th scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4 flex-1">Date</th>
        <th scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider w-1/3 flex-1">Actor</th>
        <th scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider w-full flex-1">Action</th>
     </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200 text-sm max-h-64 w-full overflow-auto scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-white block text-left">
      <tr v-for="(row, index) in logs" v-bind:key="row.id" class="flex">
        <td class="px-3 py-3 text-xs font-medium whitespace-wrap tracking-wider w-1/12 flex">{{ index + 1 }}</td>
        <td class="px-3 py-3 text-xs font-medium whitespace-wrap tracking-wider w-1/4 flex-1">{{ row.created_at }}</td>
        <td class="px-3 py-3 text-xs font-medium whitespace-wrap tracking-wider w-1/3 flex-1">{{ row.name }}</td>
        <td class="px-3 py-3 text-xs font-medium whitespace-wrap tracking-wider w-full flex-1">{{ row.action }}</td>

      </tr>
    </tbody>
  </table>
  </div>
</template>

<script>
import { ref, onMounted } from "vue";
export default {
  name: "Log",
  props: ["data"],
  setup(props) {
    const logs = ref("");
    const getLog = async () => {
      let url = "/getLog/" + props.data;
      try {
        const res = await fetch(url);
        const data = await res.json();
        return data;
      } catch (err) {
        console.error(err);
      }
    };
    const calculate = () => {
      getLog().then((d) => {
        logs.value = d;
      });
    };
    onMounted(()=>{
        calculate();
    });
    return {logs};
  },
};
</script>

<style>
</style>