<template>
  <div v-if="!radius_data" wire:loading class="w-full flex flex-col items-center justify-center">
    <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-purple-500"></div>
    <h2 class="text-center text-gray-600 text-sm font-semibold mt-2">Loading...</h2>
  </div>
  <div v-if="radius_data">
    <div v-if="radius_data != 'no_data'">
      <div class="bg-white  text-left">

        <div class="border-t border-gray-200shadow overflow-hidden sm:rounded-lg">
          <dl>
            <div class="bg-gray-50 px-2 py-2 sm:grid sm:grid-cols-4 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">PPPOE</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-3">{{ radius_data.username }}</dd>
            </div>
            <div class="bg-white px-2 py-2 sm:grid sm:grid-cols-4 sm:gap-4 sm:px-6" v-if="radius_srv">
              <dt class="text-sm font-medium text-gray-500">Service Plan</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-3">
                <multiselect deselect-label="Selected already" :options="radius_srv" track-by="srvid" label="srvname"
                  v-model="form.srv" :allow-empty="false" :multiple="false"> </multiselect>


              </dd>

            </div>
            <!-- <div class="bg-white px-2 py-2 sm:grid sm:grid-cols-4 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">Account Type</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-3">{{ radius_data.sale_channel }}</dd>
          </div> -->
            <div class="bg-gray-50 px-2 py-2 sm:grid sm:grid-cols-4 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">Account Status</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-3">

                <label class="flex-auto items-center text-sm font-medium text-gray-700 mt-1">Active</label>
                <Switch v-model="form.status" :class="form.status ? 'bg-green-600' : 'bg-red-600'"
                  class="relative inline-flex items-center h-6 rounded-full w-11 mx-2">
                  <span class="sr-only">Account Status </span>
                  <span :class="!form.status ? 'translate-x-6' : 'translate-x-1'"
                    class="inline-block w-4 h-4 transform bg-white rounded-full" />
                </Switch>
                <label class="flex-auto items-center text-sm font-medium text-gray-700 mt-1">Disabled</label>
              </dd>
            </div>

            <div class="bg-gray-50 px-2 py-2 sm:grid sm:grid-cols-4 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">Registered On</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-3">{{ formatDate(radius_data.createdon) }}</dd>
            </div>
            <div class="bg-white px-2 py-2 sm:grid sm:grid-cols-4 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">Registered By</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-3">{{ radius_data.createdby }}</dd>
            </div>
            <div class="bg-gray-50 px-2 py-2 sm:grid sm:grid-cols-4 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">Current IP</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-3">{{ radius_data.framedipaddress }}</dd>
            </div>
            <div class="bg-white px-2 py-2 sm:grid sm:grid-cols-4 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">Online Start Time</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-3">{{ formatDate(radius_data.acctstarttime) }}
              </dd>
            </div>
            <div class="bg-gray-50 px-2 py-2 sm:grid sm:grid-cols-4 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">Active Session Time</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-3">{{ formatSecond(radius_data.acctsessiontime)
                }}</dd>
            </div>
            <div class="bg-white px-2 py-2 sm:grid sm:grid-cols-4 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">Last Logoff</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-3">
                <label v-if="radius_data.acctstoptime">{{ formatDate(radius_data.acctstoptime) }}</label>
              </dd>
            </div>
            <div class="bg-gray-50 px-2 py-2 sm:grid sm:grid-cols-4 sm:gap-4 sm:px-6"
              v-if="radius_data.callingstationid">
              <dt class="text-sm font-medium text-gray-500">Caller ID</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-3">{{ radius_data.callingstationid }}</dd>
            </div>
            <div class="bg-gray-50 px-2 py-2 sm:grid sm:grid-cols-4 sm:gap-4 sm:px-6" v-if="radius_data.expiration">
              <dt class="text-sm font-medium text-gray-500">Expiration</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-3">
                <input type="date"
                  class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                  id="start_date" placeholder="Enter Start Date" v-model="form.expiration" />
              </dd>
            </div>


          </dl>
        </div>
        <div class="w-full flex justify-end mt-2">
          <button @click="saveRadius" type="button"
            class="inline-flex text-center items-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 shadow-sm focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150"
            v-if="role.radius_write">SAVE <i class="fa fas fa-save ml-2"></i></button>

          <!-- <button @click="disableRadius" type="button" class="ml-2 inline-flex py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 shadow-sm focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150">Disable</button> -->
        </div>


      </div>
    </div>
    <div v-else>
      <h2 class="text-center text-gray-600 text-sm font-semibold mt-2">No Data</h2>
      <button @click="createRadius" type="button"
        class="inline-flex text-center items-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 shadow-sm focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150"
        v-if="role.radius_write">CREATE <i class="fa fas fa-save ml-2"></i></button>

    </div>
  </div>
</template>

<script>
import { ref, onMounted, onUpdated, inject } from "vue";
import { router } from '@inertiajs/vue3';
import { Switch } from "@headlessui/vue";
import { useForm } from "@inertiajs/vue3";
import Multiselect from "@suadelabs/vue3-multiselect";
export default {
  name: "CustomerRadius",
  props: ["data"],
  components: {
    Switch,
    Multiselect,
  },
  setup(props) {
    const role = inject("role");
    const radius_srv = ref();
    const radius_data = ref();
    const form = useForm({
      id: props.data,
      srv: null,
      status: null,
      expiration: null,
    });


    const getLog = async () => {
      let url = "/getRadiusInfo/" + props.data;
      try {
        const res = await fetch(url);
        const data = await res.json();
        return data;
      } catch (err) {
        console.error(err);
      }
    };
    const getRadiusServices = async () => {
      let url = "/getRadiusServices";
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
        if (d[0]) {
          radius_data.value = d[0];
          form.status = (radius_data.value.enableuser == 1) ? true : false;
          form.expiration = formatDate(radius_data.value.expiration);
          getRadiusServices().then((e) => {
            if (e) {
              radius_srv.value = e;
              form.srv = e.filter((d) => d.srvid == radius_data.value.srvid)[0];
            } else {
              radius_srv.value = "no_data";
              console.log("no data");
            }
          });
        } else {
          radius_data.value = "no_data";
          console.log("no data");
        }
      });
    };

    const saveRadius = () => {
      let mydata = Object.create({});
      mydata.id = props.data;
      router.post("/saveRadius/" + props.data, form, {
        onSuccess: (page) => {
          getLog().then((d) => {
            radius_data.value = d[0];
            Toast.fire({
              icon: "success",
              title: page.props.flash.message,
            });
          });
        },

        onError: (errors) => {
          console.log("error ..".errors);
        },
      });
    };
    const createRadius = () => {
      let mydata = Object.create({});
      mydata.id = props.data;
      router.post("/createRadius/" + props.data, form, {
        onSuccess: (page) => {
          getLog().then((d) => {
            radius_data.value = d[0];
            Toast.fire({
              icon: "success",
              title: page.props.flash.message,
            });
          });
        },

        onError: (errors) => {
          console.log("error ..".errors);
        },
      });
    };
    const enableRadius = () => {
      let mydata = Object.create({});
      mydata.id = props.data;
      router.post("/enableRadius/" + props.data, props.data, {
        onSuccess: (page) => {
          getLog().then((d) => {
            radius_data.value = d[0];
            Toast.fire({
              icon: "success",
              title: page.props.flash.message,
            });
          });
        },

        onError: (errors) => {
          console.log("error ..".errors);
        },
      });
    };
    const disableRadius = () => {
      let mydata = Object.create({});
      mydata.id = props.data;
      router.post("/disableRadius/" + props.data, props.data, {
        onSuccess: (page) => {
          getLog().then((d) => {
            radius_data.value = d[0];
            Toast.fire({
              icon: "success",
              title: page.props.flash.message,
            });
          });
        },

        onError: (errors) => {
          console.log("error ..".errors);
        },
      });
    };
    const formatDate = (dt) => {
      var date = new Date(dt);
      var year = date.getFullYear();
      var month = date.getMonth() + 1;
      var newdt = date.getDate();
      console.log();
      if (newdt < 10) {
        newdt = "0" + newdt;
      }
      if (month < 10) {
        month = "0" + month;
      }
      // var string = newdt  + "/" + month +  "/" + year;
      //var string = year + "-" + month + "-" + newdt + " " + date.getHours() + ":" + date.getMinutes() + ":" + date.getSeconds();
      var string = year + "-" + month + "-" + newdt;
      return string;
    };
    const formatSecond = (seconds) => {
      if (seconds) {
        if (seconds < 3600) {
          return new Date(seconds * 1000).toISOString().substring(14, 19);
        } else {
          return new Date(seconds * 1000).toISOString().substring(11, 16);
        }
      }
      return 0;
    };


    onMounted(() => {
      calculate();

    });
    onUpdated(() => {

    });

    return { radius_data, radius_srv, formatDate, enableRadius, disableRadius, saveRadius, createRadius, formatSecond, form, role };
  },
};
</script>

<style></style>