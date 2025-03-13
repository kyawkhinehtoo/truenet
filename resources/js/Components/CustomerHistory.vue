<template>
  <div v-if="!logs" wire:loading class=" w-full flex flex-col items-center justify-center">
    <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-purple-500"></div>
    <h2 class="text-center text-gray-600 text-sm font-semibold mt-2">Loading...</h2>
  </div>
  <div v-if="logs">
    <table class="min-w-full divide-y divide-gray-200 ">
      <thead class="bg-gray-50 text-left">
        <tr>
          <th scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tra">No.</th>
          <th scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider ">Updated On</th>
          <th scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider ">Actor</th>
          <th scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider ">Action</th>

        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-200 text-sm text-left">
        <tr v-for="(row, index) in logs" v-bind:key="row.id">
          <td class="px-3 py-3 text-xs font-medium tracking-wider ">{{ index + 1 }}</td>
          <td class="px-3 py-3 text-xs font-medium tracking-wider ">{{ formatDate(row.created_at) }}</td>
          <td class="px-3 py-3 text-xs font-medium tracking-wider ">{{ row.actor_name }}</td>
          <td class="px-3 py-3 text-xs font-medium tracking-wider ">
            <span v-if="row.general"><span v-html="row.general"></span> <br /> </span>
          </td>


        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import { ref, onMounted } from "vue";
export default {
  name: "CustomerHistory",
  props: ["data"],
  setup(props) {
    const logs = ref();
    const getLog = async () => {
      let url = "/getCustomerHistory/" + props.data;
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
    const formatDate = (dt) => {
      var date = new Date(dt);
      var year = date.getFullYear();
      var month = date.getMonth() + 1;
      var newdt = date.getDate();
      console.log();
      if (newdt < 10) {
        newdt = '0' + newdt;
      }
      if (month < 10) {
        month = '0' + month;
      }
      var string = year + '-' + month + '-' + newdt + ' ' + date.getHours() + ':' + date.getMinutes() + ':' + date.getSeconds();
      return string;
    }
    onMounted(() => {
      calculate();
    });
    return { logs, formatDate };
  },
};
</script>

<style></style>