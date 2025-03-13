<template>
  <!-- Advance Search -->
  <div class="bg-white shadow sm:rounded-t-lg py-2 px-2 md:px-2">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-2 w-full">
      <div class="col-span-1 sm:col-span-1">
        <label for="sh_general" class="block text-sm font-medium text-gray-700">General </label>
        <div class="mt-1 flex rounded-md shadow-sm">
          <span
            class="z-10 leading-snug font-normal  text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
            <i class="fas fa-user"></i>
          </span>
          <input type="text" v-model="sh_general" name="sh_general" id="sh_general"
            class="pl-10 py-2.5 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
            placeholder="Customer/Company Name etc." tabindex="1" />
        </div>

      </div>
      <div class="col-span-1 sm:col-span-1">
        <label for="sh_package_speed" class="block text-sm font-medium text-gray-700">Package </label>
        <div class="mt-1 flex rounded-md shadow-sm">
          <span
            class="z-10 leading-snug font-normal text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
            <i class="fas fa-user"></i>
          </span>
          <select id="sh_package_speed" v-model="sh_package_speed" name="sh_package"
            class="pl-10 py-2.5 block w-full px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            tabindex="2">
            <option value="0">-Choose Package-</option>
            <option v-for="row in package_speed" v-bind:key="row.speed" :value="row.speed + '|' + row.type">{{
              row.item_data }}</option>
          </select>
        </div>

      </div>
      <div class="col-span-1 sm:col-span-1">
        <label for="sh_township" class="block text-sm font-medium text-gray-700">Township </label>
        <div class="mt-1 flex rounded-md shadow-sm">
          <span
            class="z-10 leading-snug font-normal  text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
            <i class="fas fa-user"></i>
          </span>
          <select id="sh_township" v-model="sh_township" name="sh_township"
            class="pl-10 block w-full py-2.5 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            tabindex="3">
            <option value="0">-Choose Township -</option>
            <option value="empty">-No Township -</option>
            <option v-for="row in townships" v-bind:key="row.id" :value="row.id">{{ row.name }}</option>
          </select>
        </div>

      </div>
      <div class="col-span-1 sm:col-span-1">
        <label for="sh_status" class="block text-sm font-medium text-gray-700">Customer Status </label>
        <div class="mt-1 flex rounded-md shadow-sm">
          <span
            class="z-10 leading-snug font-normal  text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
            <i class="fas fa-user"></i>
          </span>
          <select id="sh_status" v-model="sh_status" name="sh_status"
            class="pl-10 block w-full py-2.5 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            tabindex="7">
            <option value="0">-Choose Status -</option>
            <option v-for="row in status" v-bind:key="row.id" :value="row.id">{{ row.name }}</option>
          </select>
        </div>

      </div>
      <div class="col-span-1 sm:col-span-1">
        <label for="sh_package" class="block text-sm font-medium text-gray-700">Prefer Date </label>
        <div class="mt-1 flex rounded-md shadow-sm">
          
            <VueDatePicker v-model="sh_prefer" :range="{ partialRange: true }" placeholder="Please choose Prefer Installation  Date" 
            :enable-time-picker="false" model-type="yyyy-MM-dd" id="sh_package"
            class="text-gray-400 text-sm focus:ring focus:ring-indigo-500 focus:border-indigo-500 focus:ring-opacity-10 focus:outline-none" />
        </div>

      </div>
      <div class="col-span-1 sm:col-span-1">
        <label for="sh_installation" class="block text-sm font-medium text-gray-700">Installation Date </label>
        <div class="mt-1 flex rounded-md shadow-sm">
            <VueDatePicker v-model="sh_installation" :range="{ partialRange: true }" placeholder="Please choose Installation Date" 
            :enable-time-picker="false" model-type="yyyy-MM-dd" id="sh_installation"
            class="text-gray-400 text-sm focus:ring focus:ring-indigo-500 focus:border-indigo-500 focus:ring-opacity-10 focus:outline-none" />
        </div>

      </div>
      <div class="col-span-1 sm:col-span-1">
        <label for="sh_dn" class="block text-sm font-medium text-gray-700">DN </label>
        <div class="mt-1 flex rounded-md shadow-sm">
          <multiselect deselect-label="Selected already" :options="dn" track-by="name" label="name" v-model="sh_dn"
            :allow-empty="true" @select="DNSelect" placeholder="Please Choose DN"></multiselect>
        </div>

      </div>
      <div class="col-span-1 sm:col-span-1">
        <label for="sh_sn" class="block text-sm font-medium text-gray-700">SN </label>
        <div class="mt-1 flex rounded-md shadow-sm" v-if="res_sn">
          <multiselect deselect-label="Selected already" :options="res_sn" track-by="id" label="name" v-model="sh_sn"
            :allow-empty="true"></multiselect>
        </div>
        <div v-else>
          <input type="text"
            class="py-2.5 mt-1 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm text-gray-500 border-gray-300"
            value="Please Choose SN" disabled />
        </div>

      </div>
      <!-- third row -->
      <div class="col-span-1 sm:col-span-1">
        <label for="sh_vlan" class="block text-sm font-medium text-gray-700">VLAN </label>
        <div class="mt-1 flex rounded-md shadow-sm">
          <span
            class="z-10 leading-snug font-normal  text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
            <i class="fas fa-user"></i>
          </span>
          <input type="number" v-model="sh_vlan" name="sh_vlan" id="sh_vlan"
            class="pl-10 py-2.5 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
            placeholder="VLAN ID" tabindex="9" />
        </div>
      </div>
      <div class="col-span-1 sm:col-span-1">
        <label for="onu_serial" class="block text-sm font-medium text-gray-700">ONU Serial </label>
        <div class="mt-1 flex rounded-md shadow-sm" v-if="onuSerials">
          <multiselect deselect-label="Selected already" :options="onuSerials" track-by="onu_serial" label="onu_serial"
            v-model="sh_onu_serial" :allow-empty="true"></multiselect>
        </div>
      </div>
      <div class="col-span-1 sm:col-span-1">
        <label for="sale_person" class="block text-sm font-medium text-gray-700">Sale Person</label>
        <div class="mt-1 flex rounded-md shadow-sm" v-if="salePersons">
          <multiselect deselect-label="Selected already" :options="salePersons" track-by="id" label="name"
            v-model="sh_sale_person" :allow-empty="true"></multiselect>
        </div>
      </div>
      <div class="col-span-1 sm:col-span-1">
        <label for="installation_team" class="block text-sm font-medium text-gray-700">Installation Team</label>
        <div class="mt-1 flex rounded-md shadow-sm" v-if="installationTeams">
          <multiselect deselect-label="Selected already" :options="installationTeams" track-by="id" label="name"
            v-model="sh_installation_team" :allow-empty="true"></multiselect>
        </div>
      </div>
      <!-- end third row -->
    </div>
  </div>
  <div class="mb-2 py-2 px-2 md:px-2 bg-white shadow rounded-b-lg flex justify-between">
    <div class="flex">
      <a @click="doSearch"
        class="cursor-pointer inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">Search
        <i class="ml-1 fa fa-search text-white" tabindex="9"></i></a>
      <a @click="clearSearch"
        class="ml-2 cursor-pointer inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring focus:ring-gray-200 disabled:opacity-25 transition">Reset
        <i class="ml-1 fa fa-undo-alt text-white" tabindex="10"></i></a>
    </div>
    <div class="flex" v-if="role.enable_customer_export">
      <a @click="doExcel"
        class="cursor-pointer inline-flex items-center px-4 py-2 bg-green-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition">Export
        Excel <i class="ml-1 fa fa-download text-white" tabindex="11"></i></a>
    </div>
  </div>
  <!-- End of Advance Search -->
</template>

<script>
import { reactive, inject, ref } from "vue";
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import Multiselect from "@suadelabs/vue3-multiselect";
export default {
  name: "AdvanceSearch",
  emits: ["search_parameter"],
  components: {
    VueDatePicker,
    Multiselect,
  },
  setup(props, context) {
    let res_sn = ref("");
    const packages = inject("packages");
    const projects = inject("projects");
    const townships = inject("townships");
    const status = inject("status");
    const dn = inject("dn");
    const package_speed = inject("package_speed");
    const salePersons = inject("salePersons");
    const installationTeams = inject("installationTeams");
    const onuSerials = inject("onuSerials");
    const role = inject("role");

    const formatter = ref({
      date: "YYYY-MM-DD",
      month: "MMM",
    });
    let sh_general = ref("");
    let sh_installation = ref("");
    let sh_prefer = ref("");
    let sh_package = ref(0);
    let sh_township = ref(0);
    let sh_status = ref(0);
    let sh_partner = ref(0);
    let sh_dn = ref(0);
    let sh_sn = ref(0);
    let sh_package_speed = ref(0);
    let sh_vlan = ref(0);
    let sh_onu_serial = ref(0);
    let sh_installation_team = ref(0);
    let sh_sale_person = ref(0);

    const clearSearch = () => {
      let myurl = Object.create({});
      sh_general.value = "";
      sh_package.value = 0;
      sh_township.value = 0;
      sh_partner.value = 0;
      sh_status.value = 0;
      sh_dn.value = 0;
      sh_sn.value = 0;
      sh_vlan.value = 0;
      sh_onu_serial.value = 0;
      sh_installation_team.value = 0;
      sh_sale_person.value = 0;
      sh_package_speed.value = 0;
      sh_installation.value = "";
      sh_prefer.value = "";
      res_sn.value = null;
      context.emit("search_parameter", myurl);
    };
    const doExcel = () => {
      let myurl = Object.create({});

      if (sh_general.value != "") {
        myurl.general = sh_general.value;
      }
      if (sh_package.value != 0) {
        myurl.package = sh_package.value;
      }
      if (sh_township.value != 0) {
        myurl.township = sh_township.value;
      }
      if (sh_partner.value != 0) {
        myurl.project = sh_partner.value;
      }

      if (sh_status.value != 0) {
        myurl.status = sh_status.value;
      }
      if (sh_dn.value != 0) {
        myurl.dn = sh_dn.value;
      }
      if (sh_sn.value != 0) {
        myurl.sn = sh_sn.value;
      }
      if (sh_vlan.value != 0) {
        myurl.sh_vlan = sh_vlan.value;
      }
      if (sh_onu_serial.value != 0) {
        myurl.sh_onu_serial = sh_onu_serial.value;
      }
      if (sh_installation_team.value != 0) {
        myurl.sh_installation_team = sh_installation_team.value;
      }
      if (sh_sale_person.value != 0) {
        myurl.sh_sale_person = sh_sale_person.value;
      }
      if (sh_package_speed.value != 0) {
        myurl.package_speed = sh_package_speed.value;
      }
      if (sh_installation.value.from != "" && sh_installation.value.to != "") {
        myurl.installation = sh_installation.value;
      }
      if (sh_prefer.value.from != "" && sh_prefer.value.to != "") {
        myurl.prefer = sh_prefer.value;
      }
      axios.post("/exportExcel", myurl, { responseType: "blob" }).then((response) => {
        console.log(response);
        var a = document.createElement("a");
        document.body.appendChild(a);
        a.style = "display: none";
        var blob = new Blob([response.data], {
          type: response.headers["content-type"],
        });
        const link = document.createElement("a");
        link.href = window.URL.createObjectURL(blob);
        link.download = `customers_${new Date().getTime()}.xlsx`;
        link.click();
      });
    };

    const doSearch = () => {
      let myurl = Object.create({});

      if (sh_general.value != "") {
        myurl.general = sh_general.value;
      }
      if (sh_package.value != 0) {
        myurl.package = sh_package.value;
      }
      if (sh_township.value != 0) {
        myurl.township = sh_township.value;
      }
      if (sh_partner.value != 0) {
        myurl.project = sh_partner.value;
      }

      if (sh_status.value != 0) {
        myurl.status = sh_status.value;
      }
      if (sh_dn.value != 0) {
        myurl.dn = sh_dn.value;
      }
      if (sh_sn.value != 0) {
        myurl.sn = sh_sn.value;
      }
      if (sh_package_speed.value != 0) {
        myurl.package_speed = sh_package_speed.value;
      }
      if (sh_installation.value.from != "" && sh_installation.value.to != "") {
        myurl.installation = sh_installation.value;
      }
      if (sh_prefer.value.from != "" && sh_prefer.value.to != "") {
        myurl.prefer = sh_prefer.value;
      }
      if (sh_vlan.value != 0) {
        myurl.sh_vlan = sh_vlan.value;
      }
      if (sh_onu_serial.value != 0) {
        myurl.sh_onu_serial = sh_onu_serial.value;
      }
      if (sh_installation_team.value != 0) {
        myurl.sh_installation_team = sh_installation_team.value;
      }
      if (sh_sale_person.value != 0) {
        myurl.sh_sale_person = sh_sale_person.value;
      }
      context.emit("search_parameter", myurl);
    };

    function DNSelect(dn) {
      getSN(dn.id).then((d) => {
        console.log(d);
        if (d) {
          sh_sn.value = null;
          res_sn.value = d;
        } else {
          sh_sn.value = null;
          res_sn.value = null;
        }
      });
    }
    const getSN = async (dn) => {
      let url = "/getDnId/" + dn;
      try {
        const res = await fetch(url);
        const data = await res.json();
        return data;
      } catch (err) {
        console.error(err);
      }
    };
    return {
      doExcel,
      res_sn,
      formatter,
      sh_general,
      sh_general,
      sh_installation,
      sh_prefer,
      sh_package,
      sh_township,
      sh_status,
      sh_partner,
      sh_dn,
      sh_sn,
      sh_package_speed,
      packages,
      projects,
      townships,
      status,
      dn,
      package_speed,
      doSearch,
      clearSearch,
      DNSelect,
      salePersons,
      installationTeams,
      onuSerials,
      sh_vlan,
      sh_onu_serial,
      sh_installation_team,
      sh_sale_person,
      role
    };
  },
};
</script>

<style>
.dp__main * {
  font-size: 1em !important;
}
</style>