<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">Package Setup</h2>
    </template>

    <div class="py-2">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between space-x-2 items-end mb-2 px-1 md:px-0">
          <div class="relative flex flex-wrap z-0">
            <span
              class="z-10 h-full leading-snug font-normal text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-3"><i
                class="fas fa-search"></i></span>
            <input type="text" placeholder="Search here..."
              class="border-0 px-3 py-3 placeholder-gray-300 text-gray-600 relative bg-white rounded text-sm shadow outline-none focus:outline-none focus:ring w-full pl-10"
              id="search" v-model="search" v-on:keyup.enter="searchTsp" />
          </div>

          <button @click="openModal"
            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">Create</button>
        </div>
        <div class="bg-white overflow-auto shadow-xl sm:rounded-lg" v-if="packages.data">
          <!-- <button @click="openModal" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded my-3">Create New package</button>
                 <input class="w-half rounded py-2 my-3 float-right" type="text" placeholder="Search package" v-model="search" v-on:keyup.enter="searchTsp">
                    -->

          <table class="min-w-full divide-y divide-gray-200 text-xs">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  No.
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Package Name</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  v-if="radius_services != null">Radius Package</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Service Type</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  MRC
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Contract Terms</th>

                <th scope="col" class="relative px-6 py-3"><span class="sr-only">Action</span></th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="(row, index) in packages.data" v-bind:key="row.id" :class="{ 'text-gray-400': !row.status }">
                <td class="px-6 py-3  text-left text-md font-medium whitespace-nowrap">{{ packages.from + index }}</td>
                <td class="px-6 py-3  text-left text-md font-medium whitespace-nowrap">{{ row.name }}</td>
                <!-- <td class="px-6 py-3  text-left text-sm font-medium whitespace-nowrap"><Bundle :data="row.id" :key="form.componentKey" /></td> -->
                <td class="px-6 py-3  text-left text-md font-medium whitespace-nowrap uppercase"
                  v-if="radius_services != null">{{ getRadiusPackage(row.id) }}</td>
                <td class="px-6 py-3  text-left text-md font-medium whitespace-nowrap uppercase">{{ row.type }}</td>
                <td class="px-6 py-3  text-left text-md font-medium whitespace-nowrap uppercase">{{ row.price }} <span
                    class="uppercase">{{ row.currency }}</span> </td>
                <td class="px-6 py-3  text-left text-md font-medium  whitespace-nowrap">{{ row.contract_period }} Months
                </td>

                <td class="px-6 py-3  text-md font-medium whitespace-nowrap text-right">
                  <a href="#" @click="edit(row)" class="text-indigo-600 hover:text-indigo-900">Edit</a> |
                  <a href="#" @click="deleteRow(row)" class="text-red-600 hover:text-red-900">Delete</a>
                </td>
              </tr>
            </tbody>
          </table>

          <div ref="isOpen" class="fixed z-10 inset-0 overflow-y-auto ease-out duration-400" v-if="isOpen">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
              <div class="fixed inset-0 transition-opacity">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
              </div>
              ​
              <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-5xl sm:w-full"
                role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                <form @submit.prevent="submit">
                  <div class="shadow sm:rounded-md sm:overflow-hidden">
                    <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                      <div class="grid md:grid-cols-2 gap-6">
                        <div class="col-span-1 sm:col-span-1">
                          <div class="py-2">
                            <label for="name" class="block text-md font-medium text-gray-700"> Package Name </label>
                            <div class="mt-1 flex rounded-md shadow-sm">
                              <input type="text" v-model="form.name" name="name" id="name"
                                class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                                placeholder="Package Name" required />
                            </div>
                          </div>
                          <div class="py-2">
                            <label for="currency" class="block text-md font-medium text-gray-700"> Currency Type
                            </label>
                            <div class="mt-2 flex">
                              <label class="flex-auto items-center"> <input type="radio"
                                  class="form-radio h-5 w-5 text-blue-600" checked name="currency"
                                  v-model="form.currency" value="baht" /><span
                                  class="ml-2 text-gray-700 text-sm">TBH</span></label>

                              <label class="flex-auto items-center"> <input type="radio"
                                  class="form-radio h-5 w-5 text-green-600" name="currency" v-model="form.currency"
                                  value="usd" /><span class="ml-2 text-gray-700 text-sm">USD</span> </label>

                              <label class="flex-auto items-center"> <input type="radio"
                                  class="form-radio h-5 w-5 text-red-600" name="currency" v-model="form.currency"
                                  value="mmk" /><span class="ml-2 text-gray-700 text-sm">MMK</span> </label>
                            </div>
                          </div>
                          <div class="py-2 grid md:grid-cols-2 gap-4">
                            <div class="col-span-1">
                              <label for="price" class="block text-md font-medium text-gray-700"> Package Price </label>
                              <div class="mt-1 flex rounded-md shadow-sm">
                                <input type="text" v-model="form.price" name="price" id="price"
                                  class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300"
                                  placeholder="Package Price" required />
                                <span
                                  class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm uppercase">
                                  {{ form.currency }} </span>
                              </div>
                            </div>
                            <div class="col-span-1">
                              <label for="speed" class="block text-md font-medium text-gray-700"> Bandwidth </label>
                              <div class="mt-1 flex rounded-md shadow-sm">
                                <input type="text" name="speed" v-model="form.speed" id="speed"
                                  class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300"
                                  placeholder="Bandwidth in Mbps" v-on:keypress="isNumber(event)" required />
                                <span
                                  class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                  Mbps </span>
                              </div>
                            </div>

                          </div>

                          <div class="py-2" v-if="radius_services">
                            <label for="speed" class="block text-md font-medium text-gray-700"> Choose Radius Package
                            </label>
                            <div class="mt-1 flex rounded-md shadow-sm" v-if="radius_services.length !== 0">
                              <multiselect deselect-label="Selected already" :options="radius_services" track-by="srvid"
                                label="srvname" v-model="form.radius_srvid" :allow-empty="true" :multiple="false">
                              </multiselect>
                              <div v-if="$page.props.errors.package" class="text-red-500">{{
                                $page.props.errors.radius_srvid
                              }}</div>
                            </div>
                          </div>

                          <div class="py-2">
                            <label for="type" class="block text-md font-medium text-gray-700"> Service Type </label>
                            <div class="mt-1 flex">
                              <label class="flex-auto items-center mt-3"> <input type="radio"
                                  class="form-radio h-5 w-5 text-blue-600" checked name="type" v-model="form.type"
                                  value="ftth" /><span class="ml-2 text-gray-700">FTTH</span> </label>
                              <label class="flex-auto items-center mt-3"> <input type="radio"
                                  class="form-radio h-5 w-5 text-green-600" name="type" v-model="form.type"
                                  value="b2b" /><span class="ml-2 text-gray-700">B2B</span> </label>
                              <label class="flex-auto items-center mt-3"> <input type="radio"
                                  class="form-radio h-5 w-5 text-red-600" name="type" v-model="form.type"
                                  value="dia" /><span class="ml-2 text-gray-700">DIA</span> </label>
                              <label class="flex-auto items-center mt-3"> <input type="radio"
                                  class="form-radio h-5 w-5 text-yellow-600" name="type" v-model="form.type"
                                  value="mpls" /><span class="ml-2 text-gray-700">MPLS</span> </label>
                            </div>
                          </div>
                          <div class="py-2">
                            <label for="contract_period" class="block text-md font-medium text-gray-700"> Maximum
                              SLA</label>
                            <div class="mt-1 flex rounded-md shadow-sm">
                              <select id="sla_id" v-model="form.sla_id" name="sla_id"
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option v-for="row in slas" v-bind:key="row.id" :value="row.id">{{ row.percentage }}
                                </option>
                              </select>
                            </div>
                          </div>
                          <div class="py-2">
                            <label for="contract_period" class="block text-md font-medium text-gray-700"> Contract Term
                            </label>
                            <div class="mt-1 flex rounded-md shadow-sm">
                              <select id="contract_period" v-model="form.contract_period" name="contract_period"
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="1">1 Months</option>
                                <option value="3">3 Months</option>
                                <option value="6">6 Months</option>
                                <option value="12">12 Months</option>
                                <option value="24">24 Months</option>
                              </select>
                            </div>
                          </div>

                          <div class="py-2 grid grid-cols-5 gap-2">
                            <div class="col-span-3 sm:col-span-3">
                              <label for="bundle_equiptment" class="block text-md font-medium text-gray-700"> Bundle
                                Equiptment </label>
                              <div class="mt-1 flex rounded-md shadow-sm">
                                <select id="bundle_equiptment" v-model="form.bundle_equiptment" name="bundle_equiptment"
                                  autocomplete="bundle_equiptment"
                                  class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                  <option>No Bundle</option>
                                  <option v-for="row in bundle_equiptments" v-bind:key="row.id" :value="row.id">{{
                                    row.name
                                  }}</option>
                                </select>
                              </div>
                            </div>
                            <div class="col-span-1 sm:col-span-1">
                              <label for="qty" class="block text-md font-medium text-gray-700"> QTY </label>
                              <div class="mt-1 flex rounded-md shadow-sm">
                                <input type="number" v-model="form.qty" id="qty"
                                  class="mt-1 form-input focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                                  min="1" max="999" />
                              </div>
                            </div>
                            <div class="col-span-1 sm:col-span-1">
                              <label class="block text-md font-medium text-gray-700"> &nbsp; </label>
                              <div class="mt-1 flex rounded-md shadow-sm">
                                <button @click="addBundle" type="button"
                                  class="flex justify-center  px-4 mt-1 py-2 md:mt-0 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 focus:outline-none active:bg-indigo-700 transition ease-in-out duration-150">
                                  <span class="md:mt-1 hidden md:block flex-1"> Add</span>
                                  <i class="fas fa-plus-circle mt-1 px-1 cursor-pointer text-base flex-1"></i>
                                </button>
                              </div>
                            </div>

                          </div>
                          <div class="py-2">
                            <label for="type" class="block text-md font-medium text-gray-700"> Status </label>
                            <div class="mt-1 flex">
                              <input type="checkbox"
                                class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded mt-1"
                                name="status" v-model="form.status" value="true" />
                              <label class="flex-auto items-center ml-1" v-if="form.status == true">Enabled</label>
                              <label class="flex-auto items-center ml-1" v-if="form.status == false">Disabled</label>

                            </div>
                          </div>
                        </div>
                        <div class="col-span-1 sm:col-span-1">
                          <div class="py-2">
                            <label for="company_website" class="block text-md font-medium text-gray-700"> Bundle
                              Equiptment
                            </label>
                            <div v-if="form.bundleList.length === 0">
                              <label class="text-sm text-gray-700 flex-1">No Equiptment</label>
                            </div>
                            <div
                              class="mt-1 flex rounded-md shadow-sm max-h-80 overflow-y-auto scrollbar-thin scrollbar-thumb-rounded scrollbar-w-1 scrollbar-thumb-gray-400 scrollbar-track-gray-100 scrolling-touch"
                              v-if="form.bundleList.length !== 0">
                              <ul class="px-0 w-full">
                                <li class="border list-none rounded-sm px-3 py-3 flex" style="border-bottom-width: 0"
                                  v-for="row in form.bundleList" v-bind:key="row.id">
                                  <div class="text-sm text-gray-700 flex-1">{{ row[0].name }}</div>
                                  <div class="text-sm text-gray-700 flex-1">{{ row[1].qty }}</div>
                                  <div class="text-red-400 justify-end"><i
                                      class="fas fa-times-circle mr-2 text-red-700 hover:text-red-600 cursor-pointer text-base"
                                      @click="removeBundle(`${row[0].id}`)"></i></div>
                                </li>
                              </ul>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                      <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                          <button wire:click.prevent="submit" type="submit"
                            class="inline-flex justify-center w-full px-4 py-2 bg-gray-800 border border-gray-300 rounded-md font-medium text-base leading-6 sm:text-sm text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150"
                            v-show="!editMode">Save</button>
                        </span>
                        <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                          <button wire:click.prevent="submit" type="submit"
                            class="inline-flex justify-center w-full px-4 py-2 bg-gray-800 border border-gray-300 rounded-md font-medium text-base leading-6 sm:text-sm text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150"
                            v-show="editMode">Update</button>
                        </span>
                        <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
                          <button @click="closeModal" type="button"
                            class="inline-flex justify-center w-full rounded-md border border-gray-300 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5">Cancel</button>
                        </span>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
        <span v-if="packages.links">
          <pagination class="mt-6" :links="packages.links" />
        </span>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import Pagination from "@/Components/Pagination";
import Bundle from "@/Components/Bundle";
import { reactive, ref } from "vue";
import { router } from '@inertiajs/vue3';
import Multiselect from "@suadelabs/vue3-multiselect";
export default {
  name: "package",
  components: {
    AppLayout,
    Pagination,
    Bundle,
    Multiselect,
  },
  //props: ['packages', 'errors'],
  props: {
    packages: Object,
    bundle_equiptments: Object,
    slas: Object,
    errors: Object,
    bundles: String,
    radius_services: Object
  },
  setup(props) {
    const form = reactive({
      id: null,
      name: null,
      price: null,
      speed: null,
      type: "ftth",
      sla_id: 1,
      status: true,
      currency: "mmk",
      contract_period: 1,
      package_id: null,
      bundle_equiptment_id: null,
      bundleList: [],
      bundle_equiptment: "No Bundle",
      qty: 1,
      radius_srvid: null,
      componentKey: 0,
    });
    const search = ref("");
    let editMode = ref(false);
    let isOpen = ref(false);
    let test = ref("");
    // let bundleList = ref([]);
    let id = ref(null);

    function resetForm() {
      form.name = null;
      form.price = null;
      form.speed = null;
      form.currency = "mmk";
      form.contract_period = 1;
      form.package_id = null;
      form.status = true;
      form.sla_id = 1;
      form.type = "ftth";
      form.qty = 1;
      form.radius_srvid = null;
      form.bundle_equiptment = "No Bundle";
      form.bundleList = [];
    }
    function submit() {
      if (!editMode.value) {
        form._method = "POST";
        router.post("/package", form, {
          preserveState: true,
          onSuccess: (page) => {
            closeModal();
            resetForm();
            Toast.fire({
              icon: "success",
              title: page.props.flash.message,
            });
          },
          onError: (errors) => {
            closeModal();
            console.log("error ..".errors);
          },
        });
      } else {
        form._method = "PUT";
        router.post("/package/" + form.id, form, {
          onSuccess: (page) => {
            form.componentKey++;
            closeModal();
            resetForm();
            Toast.fire({
              icon: "success",
              title: page.props.flash.message,
            });
          },

          onError: (errors) => {

            console.log("error ..".errors);
          },
        });
      }
    }
    function edit(data) {
      form.id = data.id;
      form.name = data.name;
      form.price = data.price;
      form.speed = data.speed;
      form.currency = data.currency;
      form.contract_period = data.contract_period;
      form.package_id = data.package_id;
      form.sla_id = data.sla_id;
      form.radius_srvid = (props.radius_services) ? props.radius_services.filter((rs) => rs.srvid == data.radius_package)[0] : null;
      form.qty = 1;
      if (data.status == 1) {
        form.status = true;
      } else {
        form.status = false;
      }

      (form.type = data.type), (form.bundle_equiptment = "No Bundle");
      form.id = data.id;
      getBundleList(data).then((d) => {
        form.bundleList = d.map((x) => {
          return { 0: { id: x.id, name: x.bundle_name }, 1: { qty: x.qty } };
        });
      });

      editMode.value = true;
      openModal();
    }
    function isNumber(evt) {
      evt = evt ? evt : window.event;
      var charCode = evt.which ? evt.which : evt.keyCode;
      if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode !== 46) {
        evt.preventDefault();
      } else {
        return true;
      }
    }
    const getBundleList = async (d) => {
      let url = "/getpackage/" + d.id;
      console.log(url);
      try {
        const res = await fetch(url);
        const data = await res.json();
        console.log(data);
        return data;
      } catch (err) {
        console.error(err);
      }
    };
    function addBundle() {
      if (form.bundle_equiptment != "No Bundle" && form.qty != null) {
        //let dup = form.bundle_equiptment in form.bundleList;
        let dup = form.bundleList.filter((d) => d[0].id == form.bundle_equiptment);
        if (dup.length === 0) {
          let filtered = props.bundle_equiptments.filter((bdl) => bdl.id == form.bundle_equiptment);
          let oQty = { qty: form.qty };
          filtered = [filtered, oQty].flat();
          form.bundleList.push(filtered);
        }
        form.qty = 1;
      }

    }
    function getRadiusPackage(row) {
      let data = props.packages.data.filter((p) => p.id == row)[0];
      if (props.radius_services != null) {

        let r_data = props.radius_services.filter((rs) => rs.srvid == data.radius_package)[0];
        return (r_data) ? r_data.srvname : null;
      }
    }
    function removeBundle(el) {
      if (el) {
        form.bundleList = form.bundleList.filter((bdl) => bdl[0].id != el);
      }
    }
    function deleteRow(data) {
      if (!confirm("Are you sure want to remove?")) return;
      data._method = "DELETE";
      router.post("/package/" + data.id, data, {
        preserveState: true,
        onSuccess: (page) => {
          closeModal();
          resetForm();
          Toast.fire({
            icon: "success",
            title: page.props.flash.message,
          });
        },
        onError: (errors) => {
          closeModal();
          console.log("error ..".errors);
        },
      });
      closeModal();
      resetForm();
    }
    function openModal() {
      isOpen.value = true;
    }
    const closeModal = () => {
      isOpen.value = false;
      resetForm();
      editMode.value = false;
    };
    const searchTsp = () => {
      console.log("search value is" + search.value);
      router.get("/package/", { package: search.value }, { preserveState: true });
    };
    // const getBundles = async($id) => {
    //       let url = '/getpackage/'+$id;
    //       try{
    //         const res = await fetch(url);
    //         const data = await res.json();
    //         return data;
    //       }catch(err){
    //         console.error(err)
    //       }
    //  }
    //  const showBundle =($id)=> {
    //    console.log('id is ' + $id)
    //   const data = getBundles($id).then((data) => {
    //     data.map(x=> x.bundle_name + ' ' )
    //    })
    //    this.props.packages.filter(o => o.id === id).forEach(o => o.bundles = data );
    // }
    // const showBundle = computed(($id)=>{
    //   getBundles($id).then((data) => {
    //   const list = data.map(x=> x.bundle_name + ' ' )
    //   console.log(list)
    //   });
    // })
    //   onMounted(() => {

    //     this.props.packages.forEach(o => {
    //       console.log('id is ' + o.id)
    //       this.showBundle(o.id)
    //     } );
    //  })

    return { form, submit, editMode, isOpen, test, openModal, closeModal, edit, deleteRow, searchTsp, addBundle, isNumber, removeBundle, search, getRadiusPackage };
  },
};
</script>
<style scoped>
input[type="number"]::-webkit-inner-spin-button {
  opacity: 1;
}

.noborder {
  border: none;
}

/* CHECKBOX TOGGLE SWITCH */
/* @apply rules for documentation, these do not work as inline style */

.toggle-checkbox:checked {
  @apply: right-0 border-green-400;
  right: 0;
  border-color: #68d391;
}

.toggle-checkbox:checked+.toggle-label {
  @apply: bg-green-400;
  background-color: #68d391;
}

.toggle-checkbox:active,
.toggle-checkbox:focus {
  outline: 0px;
  outline-offset: 0px;
  --tw-ring-inset: var(--tw-empty,
      /*!*/
      /*!*/
    );
  --tw-ring-offset-width: 0px;
  --tw-ring-offset-color: #fff;
  --tw-ring-color: rgb(0 0 0 / 6%);
  --tw-ring-offset-shadow: var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);
  --tw-ring-shadow: var(--tw-ring-inset) 0 0 0 calc(2px + var(--tw-ring-offset-width)) var(--tw-ring-color);
  box-shadow: var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow, 0 0 #0000);
}
</style>
