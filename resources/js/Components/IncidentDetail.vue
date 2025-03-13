<template>
  <div class="grid grid-cols-5 gap-2">
    <!-- date -->
    <div class="py-2 col-span-1 sm:col-span-1">
      <div class="mt-1 flex">
        <label for="date" class="block text-sm font-medium text-gray-700 mt-2 mr-2">Incident Detail : </label>
      </div>
    </div>
    <div class="py-2 col-span-2 sm:col-span-2">
      <div class="mt-1 flex rounded-md shadow-sm">
        <input type="date" v-model="form.date" name="date" id="date"
          class="form-input focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" />
      </div>
      <p v-if="$page.props.errors.date" class="mt-2 text-sm text-red-500">{{ $page.props.errors.date }}</p>
    </div>
    <div class="py-2 col-span-2 sm:col-span-2">
      <div class="mt-1 flex rounded-md shadow-sm">
        <input type="time" v-model="form.time" name="time"
          class="form-input focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" />
      </div>
      <p v-if="$page.props.errors.time" class="mt-2 text-sm text-red-500">{{ $page.props.errors.time }}</p>
    </div>
    <!-- end of date -->
    <!-- ticket id -->
    <!--
                    <div class="py-2 col-span-2 sm:col-span-2">
                      <div class="mt-1 flex">
                        <input type="text" v-model="form.code" name="code" id="code" class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" />
                        <p v-if="$page.props.errors.code" class="mt-2 text-sm text-red-500">{{ $page.props.errors.code }}</p>
                      </div>
                    </div>
                    -->
    <!-- end of ticket id -->
    <!-- user id -->
    <div class="py-2 col-span-1 sm:col-span-1">
      <div class="mt-1 flex">
        <label for="first_name" class="block text-sm font-medium text-gray-700 mt-2 mr-2"> User ID : </label>
      </div>
    </div>
    <div class="py-2 col-span-4 sm:col-span-4">
      <div class="mt-1 flex rounded-md shadow-sm" v-if="customers.length !== 0">
        <multiselect deselect-label="Selected already" :options="customers" track-by="id" label="ftth_id"
          v-model="form.customer_id" :allow-empty="false"></multiselect>
      </div>
      <p v-if="$page.props.errors.customer" class="mt-2 text-sm text-red-500">{{ $page.props.errors.customer }}</p>
    </div>
    <!-- end of user id -->
    <!-- person incharge  -->
    <div class="py-2 col-span-1 sm:col-span-1">
      <div class="mt-1 flex">
        <label for="incharge" class="block text-sm font-medium text-gray-700 mt-2 mr-2"> Person Incharge : </label>
      </div>
    </div>
    <div class="py-2 col-span-4 sm:col-span-4">
      <div class="mt-1 flex rounded-md shadow-sm" v-if="team.length !== 0">
        <multiselect deselect-label="Selected already" :options="team" track-by="id" label="name"
          v-model="form.incharge_id" :allow-empty="false" :disabled="true"></multiselect>
      </div>
      <p v-if="$page.props.errors.incharge" class="mt-2 text-sm text-red-500">{{ $page.props.errors.incharge }}</p>
    </div>
    <!-- end of person incharge -->
    <!-- type -->
    <div class="py-2 col-span-1 sm:col-span-1">
      <div class="mt-1 flex">
        <label for="type" class="block text-sm font-medium text-gray-700 mt-2 mr-2"> Type : </label>
      </div>
    </div>
    <div class="py-2 col-span-4 sm:col-span-4">
      <div class="mt-1 flex">
        <select v-model="form.type"
          class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
          required>
          <option value="default">Please Choose Ticket Type</option>
          <option value="service_complaint">Service Complaint</option>
          <option value="gaming_issue">Gaming Issue</option>
          <option value="relocation">Relocation</option>
          <option value="plan_change">Plan Change</option>
          <option value="information_update">Information Update</option>
          <option value="suspension">Suspension</option>
          <option value="resume">Resume</option>
          <option value="termination">Termination</option>
        </select>
      </div>
      <p v-if="$page.props.errors.type" class="mt-2 text-sm text-red-500">{{ $page.props.errors.type }}</p>
    </div>
    <!-- end of type -->
    <!-- topic -->
    <div class="py-2 col-span-1 sm:col-span-1" v-if="form.type == 'service_complaint'">
      <div class="mt-1 flex">
        <label for="topic" class="block text-sm font-medium text-gray-700 mt-2 mr-2"> Topic : </label>
      </div>
    </div>
    <div class="py-2 col-span-4 sm:col-span-4" v-if="form.type == 'service_complaint'">
      <div class="mt-1 flex">
        <select v-model="form.topic"
          class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300">
          <option value="no_internet">No Internet</option>
          <option value="los_redlight">LOS Redlight</option>
          <option value="slow_performance">Slow Performance</option>
          <option value="wifi_issue">Wifi Issue</option>
          <option value="onu_issue">ONU Issue</option>
          <option value="password_change">Password Changed</option>
          <option value="other">Other</option>
        </select>
      </div>
      <p v-if="$page.props.errors.topic" class="mt-2 text-sm text-red-500">{{ $page.props.errors.topic }}</p>
    </div>
    <!-- end of topic -->
    <!-- status -->
    <div class="py-2 col-span-1 sm:col-span-1">
      <div class="mt-1 flex">
        <label for="first_name" class="block text-sm font-medium text-gray-700 mt-2 mr-2"> Status : </label>
      </div>
    </div>
    <div class="py-2 col-span-4 sm:col-span-4">
      <div class="mt-1 flex">
        <select v-model="form.status"
          class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300">
          <option value="1">Open</option>
          <option value="2" disabled>Escalated</option>
          <option value="5" disabled>Resolved</option>
          <option value="3">Closed</option>
        </select>
      </div>
      <p v-if="$page.props.errors.status" class="mt-2 text-sm text-red-500">{{ $page.props.errors.status }}</p>
    </div>
    <!-- end of status -->
    <!-- close date time -->

    <div class="py-2 col-span-1 sm:col-span-1" v-if="form.status == 3">
      <div class="mt-1 flex">
        <label for="date" class="block text-sm font-medium text-gray-700 mt-2 mr-2"> Incident Close Period : </label>
      </div>
    </div>
    <div class="py-2 col-span-2 sm:col-span-2" v-if="form.status == 3">
      <div class="mt-1 flex rounded-md shadow-sm">
        <input type="date" v-model="form.close_date" name="close_date" id="close_date"
          class="form-input focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" />
      </div>
      <p v-if="$page.props.errors.close_date" class="mt-2 text-sm text-red-500">{{ $page.props.errors.close_date }}</p>
    </div>
    <div class="py-2 col-span-2 sm:col-span-2" v-if="form.status == 3">
      <div class="mt-1 flex rounded-md shadow-sm">
        <input type="time" v-model="form.close_time" name="close_time"
          class="form-input focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" />
      </div>
      <p v-if="$page.props.errors.close_time" class="mt-2 text-sm text-red-500">{{ $page.props.errors.close_time }}</p>
    </div>

    <!-- close date time -->
    <!-- resolved date time -->

    <div class="py-2 col-span-1 sm:col-span-1" v-if="form.status == 5">
      <div class="mt-1 flex">
        <label for="date" class="block text-sm font-medium text-gray-700 mt-2 mr-2"> Incident Resolved Period : </label>
      </div>
    </div>
    <div class="py-2 col-span-2 sm:col-span-2" v-if="form.status == 5">
      <div class="mt-1 flex rounded-md shadow-sm">
        <input type="date" v-model="form.resolved_date" name="close_date" id="close_date"
          class="form-input focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" />
      </div>
      <p v-if="$page.props.errors.resolved_date" class="mt-2 text-sm text-red-500">{{ $page.props.errors.resolved_date
        }}</p>
    </div>
    <div class="py-2 col-span-2 sm:col-span-2" v-if="form.status == 5">
      <div class="mt-1 flex rounded-md shadow-sm">
        <input type="time" v-model="form.resolved_time" name="close_time"
          class="form-input focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" />
      </div>
      <p v-if="$page.props.errors.resolved_time" class="mt-2 text-sm text-red-500">{{ $page.props.errors.resolved_time
        }}</p>
    </div>

    <!-- resolved date time -->
    <!-- suspension -->
    <div class="py-2 col-span-1 sm:col-span-1" v-if="form.type == 'suspension'">
      <div class="mt-1 flex">
        <label for="suspense" class="block text-sm font-medium text-gray-700 mt-2 mr-2"> Suspense Period : </label>
      </div>
    </div>
    <div class="py-2 col-span-4 sm:col-span-4" v-if="form.type == 'suspension'">
      <div class="grid grid-cols-2 gap-2">
        <div class="col-span-1 sm:col-span-1">
          <div class="mt-1 flex rounded-md shadow-sm">
            <span
              class="-mt-1 z-10 leading-snug font-normal absolute text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-3">
              <i class="fas fa-pause"></i>
            </span>
            <input type="date" v-model="form.start_date" name="start_date" id="start_date"
              class="pl-10 form-input focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" />
          </div>
          <p v-if="$page.props.errors.start_date" class="mt-2 text-sm text-red-500">{{ $page.props.errors.start_date }}
          </p>
        </div>
        <div class="col-span-1 sm:col-span-1">
          <div class="mt-1 flex rounded-md shadow-sm">
            <span
              class="-mt-1 z-10 leading-snug font-normal absolute text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-3">
              <i class="fas fa-play"></i>
            </span>
            <input type="date" v-model="form.end_date" name="end_date" id="end_date"
              class="pl-10 form-input focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" />
          </div>
          <p v-if="$page.props.errors.end_date" class="mt-2 text-sm text-red-500">{{ $page.props.errors.end_date }}</p>
        </div>
      </div>
    </div>
    <!-- end of suspension -->
    <!-- resume -->
    <div class="py-2 col-span-1 sm:col-span-1" v-if="form.type == 'resume'">
      <div class="mt-1 flex">
        <label for="resume" class="block text-sm font-medium text-gray-700 mt-2 mr-2"> Resume Date : </label>
      </div>
    </div>
    <div class="py-2 col-span-4 sm:col-span-4" v-if="form.type == 'resume'">
      <div class="mt-1 flex rounded-md shadow-sm">
        <span
          class="z-10 -mt-1 leading-snug font-normal absolute text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-3">
          <i class="fas fa-play"></i>
        </span>
        <input type="date" v-model="form.start_date" name="start_date" id="start_date"
          class="pl-10 form-input focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" />
      </div>
      <p v-if="$page.props.errors.resume" class="mt-2 text-sm text-red-500">{{ $page.props.errors.start_date }}</p>
    </div>
    <!-- end of resume -->
    <!-- termination -->
    <div class="py-2 col-span-1 sm:col-span-1" v-if="form.type == 'termination'">
      <div class="mt-1 flex">
        <label for="termination" class="block text-sm font-medium text-gray-700 mt-2 mr-2"> Termination Date : </label>
      </div>
    </div>
    <div class="py-2 col-span-4 sm:col-span-4" v-if="form.type == 'termination'">
      <div class="mt-1 flex rounded-md shadow-sm">
        <span
          class="z-10 -mt-1 leading-snug font-normal absolute text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-3">
          <i class="fas fa-stop"></i>
        </span>
        <input type="date" v-model="form.start_date" name="start_date" id="start_date"
          class="pl-10 form-input focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" />
      </div>
      <p v-if="$page.props.errors.start_date" class="mt-2 text-sm text-red-500">{{ $page.props.errors.start_date }}</p>
    </div>
    <!-- end of termination -->
    <!-- relocation address -->
    <div class="py-2 col-span-1 sm:col-span-1" v-if="form.type == 'relocation'">
      <div class="mt-1 flex">
        <label for="new_address" class="block text-sm font-medium text-gray-700 mt-2 mr-2"> New Address : </label>
      </div>
    </div>
    <div class="py-2 col-span-4 sm:col-span-4" v-if="form.type == 'relocation'">
      <div class="mt-1 flex">
        <textarea v-model="form.new_address" name="new_address" id="new_address"
          class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"> </textarea>
      </div>
      <p v-if="$page.props.errors.new_address" class="mt-2 text-sm text-red-500">{{ $page.props.errors.new_address }}
      </p>
    </div>
    <!-- end of relocation address -->
    <!-- relocation township -->
    <div class="py-2 col-span-1 sm:col-span-1" v-if="form.type == 'relocation'">
      <div class="mt-1 flex">
        <label for="new_address" class="block text-sm font-medium text-gray-700 mt-2 mr-2"> New Township : </label>
      </div>
    </div>
    <div class="py-2 col-span-4 sm:col-span-4" v-if="form.type == 'relocation'">
      <div class="mt-1 flex rounded-md shadow-sm" v-if="townships.length !== 0">
        <multiselect deselect-label="Selected already" :options="townships" track-by="id" label="name"
          v-model="form.new_township" :allow-empty="false"></multiselect>
      </div>
      <p v-if="$page.props.errors.new_township" class="mt-2 text-sm text-red-500">{{ $page.props.errors.new_township }}
      </p>
    </div>
    <!-- end of relocation township -->
    <!-- relocation latlong -->
    <div class="py-2 col-span-1 sm:col-span-1" v-if="form.type == 'relocation'">
      <div class="mt-1 flex">
        <label for="new_latlong" class="block text-sm font-medium text-gray-700 mt-2 mr-2"> New Location : </label>
      </div>
    </div>
    <div class="py-2 col-span-4 sm:col-span-4" v-if="form.type == 'relocation'">
      <div class="grid grid-cols-2 gap-2">
        <div class="col-span-1 sm:col-span-1">
          <div class="mt-1 flex rounded-md shadow-sm">
            <span
              class="z-10 -mt-1 leading-snug font-normal absolute text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-3">
              <i class="fas fa-location-arrow"></i>
            </span>
            <input type="text" v-model="form.latitude" name="latitude" id="latitude"
              class="pl-10 form-input focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
              placeholder="Latitude" />
          </div>
          <p v-if="$page.props.errors.latitude" class="mt-2 text-sm text-red-500">{{ $page.props.errors.latitude }}</p>
        </div>
        <div class="col-span-1 sm:col-span-1">
          <div class="mt-1 flex rounded-md shadow-sm">
            <span
              class="z-10 -mt-1 leading-snug font-normal absolute text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-3">
              <i class="fas fa-location-arrow"></i>
            </span>
            <input type="text" v-model="form.longitude" name="longitude" id="longitude"
              class="pl-10 form-input focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
              placeholder="Longitude" />
          </div>
          <p v-if="$page.props.errors.longitude" class="mt-2 text-sm text-red-500">{{ $page.props.errors.longitude }}
          </p>
        </div>
      </div>
    </div>
    <!-- end of relocation latlong -->
    <!-- relocation start date -->
    <div class="py-2 col-span-1 sm:col-span-1" v-if="form.type == 'relocation'">
      <div class="mt-1 flex">
        <label for="new_address" class="block text-sm font-medium text-gray-700 mt-2 mr-2"> Effective Date : </label>
      </div>
    </div>
    <div class="py-2 col-span-4 sm:col-span-4" v-if="form.type == 'relocation'">
      <div class="mt-1 flex rounded-md shadow-sm">
        <span
          class="z-10 -mt-1 leading-snug font-normal absolute text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-3">
          <i class="fas fa-truck"></i>
        </span>
        <input type="date" v-model="form.start_date" name="start_date" id="start_date"
          class="pl-10 form-input focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" />
      </div>
      <p v-if="$page.props.errors.start_date" class="mt-2 text-sm text-red-500">{{ $page.props.errors.start_date }}</p>
    </div>
    <!-- relocation end date -->
    <!-- plan change -->
    <div class="py-2 col-span-1 sm:col-span-1" v-if="form.type == 'plan_change'">
      <div class="mt-1 flex">
        <label for="new_package" class="block text-sm font-medium text-gray-700 mt-2 mr-2"> New Package: </label>
      </div>
    </div>
    <div class="py-2 col-span-4 sm:col-span-4" v-if="form.type == 'plan_change'">
      <div class="mt-1 flex rounded-md shadow-sm" v-if="packages.length !== 0">
        <multiselect deselect-label="Selected already" :options="packages" track-by="id" label="item_data"
          v-model="form.package_id" :allow-empty="false"></multiselect>
      </div>
      <p v-if="$page.props.errors.package" class="mt-2 text-sm text-red-500">{{ $page.props.errors.package }}</p>
    </div>
    <!-- end of plan change -->
    <!-- relocation start date -->
    <div class="py-2 col-span-1 sm:col-span-1" v-if="form.type == 'plan_change'">
      <div class="mt-1 flex">
        <label for="new_address" class="block text-sm font-medium text-gray-700 mt-2 mr-2"> Effective Date : </label>
      </div>
    </div>
    <div class="py-2 col-span-4 sm:col-span-4" v-if="form.type == 'plan_change'">
      <div class="mt-1 flex rounded-md shadow-sm">
        <span
          class="z-10 -mt-1 leading-snug font-normal absolute text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-3">
          <i class="fas fa-random"></i>
        </span>
        <input type="date" v-model="form.start_date" name="start_date" id="start_date"
          class="pl-10 form-input focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" />
      </div>
      <p v-if="$page.props.errors.start_date" class="mt-2 text-sm text-red-500">{{ $page.props.errors.start_date }}</p>
    </div>
    <!-- relocation end date -->
    <!-- detail -->
    <div class="py-2 col-span-1 sm:col-span-1">
      <div class="mt-1 flex">
        <label for="detail" class="block text-sm font-medium text-gray-700 mt-2 mr-2"> Detail : </label>
      </div>
    </div>
    <div class="py-2 col-span-4 sm:col-span-4">
      <div class="mt-1 flex">
        <textarea v-model="form.description" name="detail" id="detail"
          class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"> </textarea>
      </div>
      <p v-if="$page.props.errors.description" class="mt-2 text-sm text-red-500">{{ $page.props.errors.description }}
      </p>
    </div>
    <!-- end of detail -->
  </div>

  <div v-if="loading"
    class="fixed top-0 left-0 right-0 bottom-0 w-full h-screen z-50 overflow-hidden bg-gray-700 opacity-75 flex flex-col items-center justify-center">
    <div class="loader ease-linear rounded-full border-4 border-t-4 border-gray-200 h-12 w-12 mb-4"></div>
    <h2 class="text-center text-white text-xl font-semibold">Loading...</h2>
    <p class="w-1/3 text-center text-white">This may take a few seconds, please don't close this page.</p>
  </div>
</template>

<script>
import Multiselect from "@suadelabs/vue3-multiselect";
import { router } from '@inertiajs/vue3';
import { reactive, ref, onMounted, onUpdated,  inject, defineExpose } from "vue";
import { useForm } from "@inertiajs/vue3";
export default {
  name: "IncidentDetail",
  emits: ["loading"],
  emits: ["selected_id"],
  components: {
    Multiselect,
  },
  props: {
    errors: Object,
    data: Object,
    parentform: Object,
  },
  setup(props, context) {
    const noc = inject("noc");
    const team = inject("team");
    const user = inject("user");
    const incidents = inject("incidents");
    const customers = inject("customers");
    const townships = inject("townships");
    const packages = inject("packages");

    const parent_form = inject("parent_form");

    let incident = ref();
    let editMode = ref(false);
    let loading = ref(false);
    let selected_id = ref(null);
    defineExpose({
      doSubmit,
    });
    const form = useForm({
      id: null,
      code: null,
      priority: "normal",
      customer_id: null,
      incharge_id: team.filter((d) => d.id == user.id)[0],
      type: "default",
      topic: null,
      status: 1,
      start_date: null,
      end_date: null,
      new_address: null,
      new_township: null,
      latitude: null,
      longitude: null,
      package_id: null,
      description: null,
      date: null,
      time: null,
      close_date: null,
      close_time: null,
      resolved_date: null,
      resolved_time: null,
    });
    function clearform() {
      selected_id.value = null;
      editMode.value = false;
      form.id = "";
      form.code = "";
      form.priority = "normal";
      form.customer_id = "";
      form.incharge_id = team.filter((d) => d.id == user.id)[0];
      form.type = "default";
      form.topic = "";
      form.status = 1;
      form.start_date = "";
      form.end_date = "";
      form.new_address = "";
      form.new_township = "";
      form.latitude = "";
      form.longitude = "";
      form.package_id = "";
      form.description = "";
      form.date = "";
      form.time = "";
      form.close_date = "";
      form.close_time = "";
      form.resolved_date = "";
      form.resolved_time = "";
    }

    function doSubmit() {
      if (editMode.value != true) {
        form._method = "POST";
        router.post("/incident", form, {
          preserveState: true,
          onSuccess: (page) => {
            selected_id.value = page.props.response.id;
            let response = incidents.data.filter((d) => d.id == selected_id.value)[0];
            editMode.value = true;
            context.emit("selected_id", selected_id.value);
            context.emit("loading", false);
            if (response !== undefined) {
              console.log("response is : " + response);
              edit(response);
            }

            Toast.fire({
              icon: "success",
              title: page.props.flash.message,
            });
          },
          onError: (errors) => {
            context.emit("loading", false);
            console.log("error ..");
          },
          onStart: (pending) => {
            context.emit("loading", true);
          },
        });
      } else {
        form._method = "PUT";
        router.post("/incident/" + form.id, form, {
          onSuccess: (page) => {
            Toast.fire({
              icon: "success",
              title: page.props.flash.message,
            });
            context.emit("loading", false);
          },
          onError: (errors) => {
            context.emit("loading", false);
            console.log("error .." + errors);
          },
          onStart: (pending) => {
            context.emit("loading", true);
          },
        });
      }
    }
    function edit(data) {
      let lat_long = null;
      selected_id.value = data.id;
      editMode.value = true;
      if (data.location != null) {
        lat_long = data.location.split(",", 2);
        form.latitude = lat_long[0];
        form.longitude = lat_long[1];
      }
      form.id = data.id;
      form.code = data.code;
      form.priority = data.priority;
      form.customer_id = customers.filter((d) => d.id == data.customer_id)[0];
      form.incharge_id = noc.filter((d) => d.id == data.incharge_id)[0];
      form.type = data.type;
      form.topic = data.topic;
      form.status = data.status;
      form.start_date = data.start_date;
      form.end_date = data.end_date;
      form.new_address = data.new_address;
      form.new_township = townships.filter((d) => d.id == data.new_township)[0];
      form.package_id = packages.filter((d) => d.id == data.package_id)[0];
      form.description = data.description;
      form.date = data.date;
      form.time = data.time;
      form.close_date = data.close_date;
      form.close_time = data.close_time;
      form.resolved_date = data.resolved_date;
      form.resolved_time = data.resolved_time;
    }
    function openForm(data) {
      editMode.value = true;
      // form.id = data.id;
      // form.code = data.code;
      form.priority = data.priority;
      // form.customer_id = data.customer_id;
      // form.incharge_id = data.incharge_id;
      // form.type = data.type;
      // form.topic = data.topic;
      // form.status = data.status;
      // form.start_date = data.start_date;
      // form.end_date = data.end_date;
      // form.new_address = data.new_address;
      // form.new_township = data.new_township;
      // form.package_id = data.package_id;
      // form.description = data.description;
      // form.date = data.date;
      // form.time = data.time;
      // form.close_date = data.close_date;
      // form.close_time = data.close_time;
      // form.resolved_date = data.resolved_date;
      // form.resolved_time = data.resolved_time;
    }
    function getStatus(data) {
      let status = "Open";
      if (data == 1) {
        status = "Open";
      } else if (data == 2) {
        status = "Escalated";
      } else if (data == 3) {
        status = "Closed";
      } else if (data == 4) {
        status = "Deleted";
      } else if (data == 5) {
        status = "Resolved";
      }
      return status;
    }
    function deleteIncident(data) {
      if (!confirm("Are you sure want to remove?")) return;
      form._method = "PUT";
      form.status = 4;
      router.post("/incident/" + form.id, form, {
        onSuccess: (page) => {
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
    const getIncident = async () => {
      let url = "/getIncident/" + props.data;
      console.log(url);
      try {
        context.emit("loading", true);
        const res = await fetch(url);
        const data = await res.json();
        // console.log(data);
        return data;
      } catch (err) {
        context.emit("loading", false);
        console.error(err);
      }
    }
    const calculate = () => {
      getIncident().then((d) => {
        incident.value = d;
      });
    }
    onMounted(() => {
      console.log("Incident Detail - onMounted");


      if (props.data) {
        selected_id.value = props.data;

        getIncident().then((response) => {
          context.emit("loading", false);
          if (response !== undefined) {
            console.log('appending response');
            edit(response);
            console.log('show parent form');
            console.log(parent_form);
            if (parent_form) {

              openForm(parent_form);
            }
          } else {
            console.log('not appending response');
          }
        });
      }

      packages.map(function (x) {
        return (x.item_data = `${x.speed} Mbps - ${x.name} - ${x.contract_period} Months`);
      });
    });
    onUpdated(() => {
      console.log("Incident Detail - onUpdated");

      packages.map(function (x) {
        return (x.item_data = `${x.speed} Mbps - ${x.name} - ${x.contract_period} Months`);
      });
    });
    return { loading, getIncident, calculate, deleteIncident, doSubmit, getStatus, form, clearform, customers, team, townships, packages, openForm, editMode, edit };
  },
};
</script>
