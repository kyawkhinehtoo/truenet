<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">Bill Generator</h2>
    </template>

    <div class="py-2">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <steps :steps="3" :step="step" />

        <TransitionRoot :show="step == 1" enter="transition ease-out duration-300 transform" enter-from="translate-x-4"
          enter-to="translate-x-0">
          <div class="bg-white shadow sm:rounded-lg mt-4">
            <div class="px-4 py-2">
              <label for="month" class="block text-gray-700 text-sm font-bold "><span class="text-red-500">*</span>
                Billing
                Month:</label>

              <div class=" flex rounded-md shadow-sm">
                <input type="month" v-model="form.month" name="month" id="month"
                  class=" form-input focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                  @change="selectMonth" />

              </div>

              <p v-if="errors.month" class="mt-2 text-sm text-red-500">{{ errors.month }}</p>
            </div>

            <div class="px-4 py-2">
              <label for="bill_number" class="block text-gray-700 text-sm font-bold "><span
                  class="text-red-500">*</span>
                Bill Number:</label>

              <div class=" flex rounded-md shadow-sm">
                <input type="text" v-model="form.bill_number" name="bill_number" id="bill_number"
                  class=" form-input focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" />
              </div>

              <p v-if="errors.bill_number" class="mt-2 text-sm text-red-500">{{ errors.bill_number }}</p>
            </div>
            <div class="px-4 py-2">
              <label for="issue_date" class="block text-gray-700 text-sm font-bold "><span class="text-red-500">*</span>
                Issue Date:</label>

              <div class=" flex rounded-md shadow-sm">
                <input type="date" v-model="form.issue_date" name="issue_date" id="issue_date"
                  class=" form-input focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" />
              </div>

              <p v-if="errors.issue_date" class="mt-2 text-sm text-red-500">{{ errors.issue_date }}</p>
            </div>
            <div class="px-4 py-2">
              <label for="due_date" class="block text-gray-700 text-sm font-bold "><span class="text-red-500">*</span>
                Due
                Date:</label>

              <div class=" flex rounded-md shadow-sm">
                <input type="date" v-model="form.due_date" name="due_date" id="due_date"
                  class=" form-input focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" />
              </div>

              <p v-if="errors.due_date" class="mt-2 text-sm text-red-500">{{ errors.due_date }}</p>
            </div>
            <!-- <div class="px-4 py-2">
              <label for="bill" class="block text-sm font-medium text-gray-700">Additional to Existing Bill ? </label>
              <div class="flex rounded-md shadow-sm">
                <multiselect deselect-label="Selected already" :options="bill" track-by="id" label="name" v-model="form.bill_id" :allow-empty="true"></multiselect>
              </div>
              <p v-if="$page.props.errors.bill_id" class="mt-2 text-sm text-red-500">{{ $page.props.errors.bill_id }}</p>
            </div> -->
          </div>

          <!-- ... -->
        </TransitionRoot>

        <TransitionRoot :show="step == 2" enter="transition ease-out duration-300 transform" enter-from="translate-x-4"
          enter-to="translate-x-0">
          <div class="bg-white shadow sm:rounded-lg mt-4">
            <div class="px-4 py-5 sm:px-6">
              <h3 class="text-lg leading-6 font-medium text-gray-900">Billing Information</h3>
              <p class="mt-1 max-w-2xl text-sm text-gray-500">Preview the information.</p>
            </div>
            <div class="border-t border-gray-200">
              <dl>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500">Billing Month</dt>
                  <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ form.month_name }}</dd>
                </div>

                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500">Bill Number</dt>
                  <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ form.bill_number }}</dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500">Issue Date</dt>
                  <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ form.issue_date }}</dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500">Due Date</dt>
                  <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ form.due_date }}</dd>
                </div>

              </dl>
            </div>
          </div>
          <!-- ... -->
        </TransitionRoot>
        <button class="mt-2 mr-2 bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded "
          @click="back()" v-if="step > 1">Back</button>
        <button class="mt-2 bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded " v-if="step < 2"
          @click="next()">Next</button>
        <button class="mt-2 bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded " v-if="step == 2"
          @click="submit()">Generate</button>



      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import Multiselect from "@suadelabs/vue3-multiselect";
import Steps from "@/Components/Steps";
import { reactive, ref, onMounted } from "vue";
import { router } from '@inertiajs/vue3';
import { useForm } from "@inertiajs/vue3";
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import { TransitionRoot, TransitionChild } from "@headlessui/vue";
export default {
  name: "BillGenerator",
  components: {
    AppLayout,
    Steps,
    Multiselect,
    TransitionRoot,
    TransitionChild,
    VueDatePicker
  },
  props: {
    packages: Object,
    townships: Object,
    bill: Object,
    projects: Object,
    errors: Object,
  },
  setup(props) {
    const step = ref(1);
    const formatter = ref({
      date: "YYYY-MM-DD",
      month: "MMM",
    });

    const form = useForm({
      month: null,
      bill_id: null,
      period_covered_name: null,
      bill_number: null,
      issue_date: null,
      due_date: null,
      month_name: null,
      bill_month: null,
      bill_year: null,
    });

    function resetForm() {
      form.month = null;
      form.bill_id = null;
      form.period_covered = null;
      form.period_covered_name = null;
      form.bill_number = null;
      form.usd_exchange_rate = null;
      form.baht_exchange_rate = null;
      form.issue_date = null;
      form.due_date = null;
      form.expire_date = null;
      form.package = null;
      form.township = null;
      form.month_name = null;
      form.bill_month = null;
      form.bill_year = null;
      form.is_mmk = null;
    }
    function selectMonth() {

      let form_data = form.month;
      let form_date = new Date(form_data);

      form.bill_month = form_date.getMonth();
      form.bill_month = form.bill_month + 1; //fix javascript month start from zero
      form.bill_year = form_date.getFullYear();
      let bill_month = form.bill_month;
      let string_year = form.bill_year.toString();
      var options = { bill_month: 'long' };

      var formattedYear = string_year.substring(2, 4);
      var formattedMonth = ("0" + bill_month).slice(-2);
      form.month_name = new Intl.DateTimeFormat('en-US', options).format(form_date);

      form.bill_number = formattedYear + formattedMonth;

    }
    function next() {

      if (form.month != null) {
        props.errors.month = null
      } else {
        props.errors.month = "Please Enter Bill Month";
      }

      if (form.bill_number != null) {
        props.errors.bill_number = null
      } else {
        props.errors.bill_number = "Please Enter Bill Number";
      }

      if (form.issue_date != null) {
        props.errors.issue_date = null
      } else {
        props.errors.issue_date = "Please Enter Bill Issue Date";
      }

      if (form.due_date != null) {
        props.errors.due_date = null
      } else {
        props.errors.due_date = "Please Enter Bill Due Date";
      }

      //check error clear or not
      if (Object.values(props.errors).every(x => x === null || x === '')) {

        if (step.value <= 3)
          step.value++
      }
    }
    function back() {
      if (step.value >= 1)
        step.value--
    }
    function submit() {

      form.post("/doGenerate", {
        onSuccess: (page) => {
          Toast.fire({
            icon: "success",
            title: page.props.flash.message,
          });
          resetForm();

        },
        onError: (errors) => {
          console.log(errors)
        }
      });
    }

    onMounted(() => {
      props.packages.map(function (x) {
        return (x.item_data = `${x.speed} Mbps ${x.name} - ${x.contract_period} Months`);
      });
    });
    return { form, step, formatter, selectMonth, next, back, submit };
  },
};
</script>
