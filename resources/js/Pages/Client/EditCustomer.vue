<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">Customer Details</h2>
    </template>
    <div class="py-2">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mt-5 md:mt-0 md:col-span-2">
          <form @submit.prevent="submit">
            <div class="shadow sm:rounded-md sm:overflow-hidden">
              <div class="inline-flex w-full bg-gray-50 rounded-t-lg">
                <ul id="tabs" class="flex">
                  <li class="px-2 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    :class="[tab == 1 ? 'border-b-2 border-indigo-400 -mb-px' : 'opacity-50']"><a href="#"
                      @click="tabClick(1)" preserve-state>Genaral</a></li>
                  <li class="px-2 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    :class="[tab == 2 ? 'border-b-2 border-indigo-400 -mb-px' : 'opacity-50']"><a href="#"
                      @click="tabClick(2)" preserve-state>Documents <span
                        class="bg-blue-100 text-blue-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">{{
                          total_documents }}</span></a></li>
                  <li class="px-2 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider "
                    :class="[tab == 6 ? 'border-b-2 border-indigo-400 -mb-px' : 'opacity-50']"><a href="#"
                      @click="tabClick(6)" preserve-state>Text</a></li>
                  <li class="px-2 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    :class="[tab == 3 ? 'border-b-2 border-indigo-400 -mb-px' : 'opacity-50']"><a href="#"
                      @click="tabClick(3)" preserve-state>History</a></li>
                  <li class="px-2 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    :class="[tab == 4 ? 'border-b-2 border-indigo-400 -mb-px' : 'opacity-50']"
                    v-if="role.read_only_ip || role.add_ip || role.add_ip || role.edit_ip"><a href="#"
                      @click="tabClick(4)" preserve-state>Public IP <span
                        class="bg-blue-100 text-blue-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">{{
                          total_ips }}</span></a></li>
                  <li class="px-2 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    :class="[tab == 5 ? 'border-b-2 border-indigo-400 -mb-px' : 'opacity-50']"
                    v-if="radius && role.radius_read"><a href="#" @click="tabClick(5)" preserve-state>Radius</a></li>
                </ul>
              </div>
              <!-- Tab Contents -->
              <div id="tab-contents">
                <!-- tab1 -->
                <div class="p-4" :class="[tab == 1 ? '' : 'hidden']">
                  <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                    <h6 class="md:min-w-full text-indigo-700 text-xs uppercase font-bold block pt-1 no-underline">
                      Customer
                      Basic Information</h6>
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-2">
                      <div class="col-span-1 sm:col-span-1">
                        <label for="name" class="block text-sm font-medium text-gray-700"><span
                            class="text-red-500">*</span>
                          Customer Name </label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                          <span
                            class="z-10 leading-snug font-normal text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                            <i class="fas fa-user"></i>
                          </span>
                          <input type="text" v-model="form.name" name="name" id="name"
                            class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                            placeholder="Customer Name" required :disabled="checkPerm('name')" />
                        </div>
                        <p v-show="$page.props.errors.name" class="mt-2 text-sm text-red-500">{{ $page.props.errors.name
                          }}
                        </p>
                      </div>
                      <div class="col-span-1 sm:col-span-1">
                        <label for="nrc" class="block text-sm font-medium text-gray-700"> NRC/Passport </label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                          <span
                            class="z-10 leading-snug font-normal absolute text-center text-gray-300 bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                            <i class="fas fa-id-card"></i>
                          </span>
                          <input type="text" v-model="form.nrc" name="nrc" id="nrc"
                            class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                            placeholder="e.g 12/AaBbCc(N)123456" :disabled="checkPerm('nrc')" />
                        </div>
                        <p v-show="$page.props.errors.nrc" class="mt-2 text-sm text-red-500">{{ $page.props.errors.nrc
                          }}
                        </p>
                      </div>

                      <div class="col-span-1 sm:col-span-1">
                        <label for="phone_1" class="block text-sm font-medium text-gray-700"><span
                            class="text-red-500">*</span>
                          Phone No. (e.g. 09-123/09-456)</label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                          <span
                            class="z-10 leading-snug font-normal absolute text-center text-gray-300 bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                            <i class="fas fa-phone"></i>
                          </span>
                          <input type="text" v-model="form.phone_1" name="phone_1" id="phone_1"
                            class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                            placeholder="e.g 0945000111" required :disabled="checkPerm('phone_1')" />
                        </div>
                        <p v-show="$page.props.errors.phone_1" class="mt-2 text-sm text-red-500">{{
                          $page.props.errors.phone_1 }}</p>
                      </div>
                      <div class="col-span-1 sm:col-span-1">
                        <label for="email" class="block text-sm font-medium text-gray-700"> Email
                        </label>
                        <div class="mt-1 flex rounded-md ">
                          <span
                            class="z-10 leading-snug font-normal absolute text-center text-gray-300 bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                            <i class="fas fa-envelope"></i>
                          </span>
                          <input type="email" v-model="form.email" name="email" id="email"
                            class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                            placeholder="customer@gmail.com" :disabled="checkPerm('email')" />
                        </div>
                        <p v-if="$page.props.errors.email" class="mt-2 text-sm text-red-500">{{
                          $page.props.errors.email
                        }}</p>
                      </div>

                      <div class="col-span-1 sm:col-span-1">
                        <label for="township" class="block text-sm font-medium text-gray-700"><span
                            class="text-red-500">*</span>
                          Township </label>
                        <div class="mt-1 flex rounded-md shadow-sm" v-if="townships.length !== 0">
                          <multiselect deselect-label="Selected already" :options="townships" track-by="id" label="name"
                            v-model="form.township" placeholder="Select Township" :allow-empty="false"
                            :disabled="checkPerm('township_id')" required>
                          </multiselect>
                        </div>
                        <p v-show="$page.props.errors.township_id" class="mt-2 text-sm text-red-500">{{
                          $page.props.errors.township_id }}</p>
                      </div>
                      <div class="col-span-1 sm:col-span-1">
                        <label for="project_id" class="block text-sm font-medium text-gray-700"> Project/ Partner
                        </label>
                        <div class="mt-1 flex rounded-md " v-if="projects.length !== 0">
                          <multiselect deselect-label="Selected already" :options="projects" track-by="id" label="name"
                            v-model="form.project" placeholder="Select Project/Partner" :allow-empty="false"
                            :disabled="checkPerm('project_id')" >
                          </multiselect>
                        </div>
                        <p v-if="$page.props.errors.project" class="mt-2 text-sm text-red-500">{{
                          $page.props.errors.project
                        }}</p>
                      </div>

                      <div class="col-span-1 sm:col-span-2">
                        <label for="social_account" class="block text-sm font-medium text-gray-700"> Social Account
                        </label>
                        <div class="mt-1 flex rounded-md ">
                          <span
                            class="z-10 leading-snug font-normal absolute text-center text-purple-400 bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                            <i class="mt-1 fas fa-brands fa-viber"></i>
                          </span>
                          <input type="text" v-model="form.social_account" name="social_account" id="social_account"
                            class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                            placeholder="Enter Social Account" :disabled="checkPerm('social_account')" />
                        </div>
                        <p v-if="$page.props.errors.social_account" class="mt-2 text-sm text-red-500">{{
                          $page.props.errors.social_account
                        }}</p>
                      </div>
                      <div class="col-span-1 sm:col-span-4">
                        <label for="address" class="block text-sm font-medium text-gray-700"><span
                            class="text-red-500">*</span>
                          Full Address </label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                          <span
                            class="z-10 leading-snug font-normal absolute text-center text-gray-300 bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                            <i class="fas fa-map-marker-alt"></i>
                          </span>
                          <textarea v-model="form.address" name="address" id="address"
                            class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                            required :disabled="checkPerm('address')" />
                        </div>
                        <p v-show="$page.props.errors.address" class="mt-2 text-sm text-red-500">{{
                          $page.props.errors.address }}</p>
                      </div>
                    </div>
                    <hr class="my-4 md:min-w-full" />
                    <h6 class="md:min-w-full text-indigo-700 text-xs uppercase font-bold block pt-1 no-underline">Sale
                      Information</h6>
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-2">
                      <div class="col-span-1 sm:col-span-1">
                        <label for="order_date" class="block text-sm font-medium text-gray-700"><span
                            class="text-red-500">*</span> Order Date </label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                          <input type="date" v-model="form.order_date" name="order_date" id="order_date"
                            class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                            required :disabled="checkPerm('order_date')" />
                        </div>
                        <p v-show="$page.props.errors.order_date" class="mt-2 text-sm text-red-500">{{
                          $page.props.errors.order_date }}</p>
                      </div>
                      <div class="col-span-1 sm:col-span-1">
                        <label for="status" class="block text-sm font-medium text-gray-700"><span
                            class="text-red-500">*</span>
                          Customer Status </label>
                        <div class="mt-1 flex rounded-md shadow-sm" v-if="status_list.length !== 0">
                          <multiselect deselect-label="Selected already" :options="status_list" track-by="id"
                            label="name" v-model="form.status" :allow-empty="false" :disabled="checkPerm('status_id')">
                          </multiselect>
                        </div>
                        <p v-show="$page.props.errors.status" class="mt-2 text-sm text-red-500">{{
                          $page.props.errors.status
                        }}</p>
                      </div>
                      <div class="col-span-1 sm:col-span-1">
                        <label for="sale_person" class="block text-sm font-medium text-gray-700"><span
                            class="text-red-500">*</span> Sale Person </label>
                        <div class="mt-1 flex rounded-md shadow-sm" v-if="sale_persons.length !== 0">
                          <multiselect deselect-label="Selected already" :options="sale_persons" track-by="id"
                            label="name" v-model="form.sale_person" :allow-empty="false"
                            :disabled="checkPerm('sale_person_id')">
                          </multiselect>
                        </div>
                        <p v-show="$page.props.errors.sale_person" class="mt-2 text-sm text-red-500">{{
                          $page.props.errors.sale_person }}</p>
                      </div>
                      <div class="col-span-1 sm:col-span-1">
                        <label for="sale_channel" class="block text-sm font-medium text-gray-700"><span
                            class="text-red-500">*</span> Sale Channel </label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                          <span
                            class="z-10 leading-snug font-normal text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                            <i class="fas fa-external-link-alt"></i>
                          </span>
                          <input type="text" v-model="form.sale_channel" name="sale_channel" id="sale_channel"
                            class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                            placeholder="e.g Facebook/ Person Name" required :disabled="checkPerm('sale_channel')" />
                        </div>
                        <p v-show="$page.props.errors.sale_channel" class="mt-2 text-sm text-red-500">{{
                          $page.props.errors.sale_channel }}</p>
                      </div>

                      <div class="col-span-1 sm:col-span-1">
                        <label for="package" class="block text-sm font-medium text-gray-700"><span
                            class="text-red-500">*</span>
                          Package </label>
                        <div class="mt-1 flex rounded-md shadow-sm" v-if="packages.length  !== 0 ">
                          <multiselect deselect-label="Selected already" :options="packages" track-by="id" label="name"
                            v-model="form.package" :allow-empty="false" :disabled="checkPerm('package_id')">
                          </multiselect>
                        </div>
                        <p v-show="$page.props.errors.package" class="mt-2 text-sm text-red-500">{{
                          $page.props.errors.package }}</p>
                      </div>
                      <div class="col-span-1 sm:col-span-1">
                        <label for="extra_bandwidth" class="block text-sm font-medium text-gray-700"> Extra Bandwidth
                        </label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                          <input type="number" v-model="form.extra_bandwidth" name="extra_bandwidth"
                            id="extra_bandwidth"
                            class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300"
                            placeholder="Only for bonus bandwidth" :disabled="checkPerm('extra_bandwidth')"
                            v-on:keypress="isNumber(event)" />
                          <span
                            class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                            Mbps </span>
                        </div>
                        <p v-show="$page.props.errors.extra_bandwidth" class="mt-2 text-sm text-red-500">{{
                          $page.props.errors.extra_bandwidth }}</p>
                      </div>
                      <div class="col-span-1 sm:col-span-1">
                        <label for="foc_period" class="block text-sm font-medium text-gray-700"><input type="checkbox"
                            class="rounded-sm" v-model="form.foc" id="foc" /> FOC (Free of Charge) </label>
                        <div class="mt-1 flex rounded-md">
                          <select name="foc_period" id="foc_period" v-model="form.foc_period"
                            class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            :disabled="!form.foc">
                            <option value="" selected>Please Choose Period</option>
                            <option value="1">1 Month</option>
                            <option value="2">2 Months</option>
                            <option value="3">3 Months</option>
                            <option value="4">4 Months</option>
                            <option value="5">5 Months</option>
                            <option value="7">7 Months</option>
                            <option value="8">8 Months</option>
                            <option value="9">9 Months</option>
                            <option value="10">10 Months</option>
                            <option value="11">11 Months</option>
                            <option value="12">12 Months</option>
                            <option value="0">Unlimited</option>
                          </select>
                        </div>

                      </div>
                      <div class="col-span-1 sm:col-span-1">
                        <label for="customer_type" class="block text-sm font-medium text-gray-700"> Customer Type
                        </label>
                        <div class="mt-1 flex rounded-md">
                          <select name="customer_type" id="customer_type" v-model="form.customer_type"
                            class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                          :disabled="checkPerm('customer_type')">
                            <option value="1">Normal Customer</option>
                            <option value="2">VIP Customer</option>
                            <option value="3">Partner Customer</option>
                            <option value="4">Office Staff</option>
                          </select>
                        </div>

                      </div>
                      <div class="col-span-1 sm:col-span-4">
                        <label for="sale_remark" class="block text-sm font-medium text-gray-700"> Sale Remark </label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                          <span
                            class="z-10 leading-snug font-normal text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                            <i class="fas fa-comment"></i>
                          </span>
                          <textarea name="sale_remark" v-model="form.sale_remark" id="sale_remark"
                            class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                            :disabled="checkPerm('sale_remark')" />
                        </div>
                        <p v-show="$page.props.errors.sale_remark" class="mt-2 text-sm text-red-500">{{
                          $page.props.errors.sale_remark }}</p>
                      </div>
                    </div>
                    <hr class="my-4 md:min-w-full" />
                    <h6 class="md:min-w-full text-indigo-700 text-xs uppercase font-bold block pt-1 no-underline">
                      Installation Information</h6>
                    <div class="col-span-1 sm:col-span-1 w-full sm:w-1/2">
                      <label for="ftth_id" class="block text-sm font-medium text-gray-700"><span
                          class="text-red-500">*</span>
                        Customer ID </label>
                      <div class="mt-1 flex rounded-md shadow-sm">
                        <span
                          class="z-10 leading-snug font-normal text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                          <i class="fas fa-id-badge"></i>
                        </span>
                        <input v-model="form.ftth_id" type="text" name="ftth_id" id="ftth_id"
                          class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                          required :disabled="checkPerm('ftth_id')" />
                      </div>
                      <p v-show="$page.props.errors.ftth_id" class="mt-2 text-sm text-red-500">{{
                        $page.props.errors.ftth_id }}</p>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-2">

                      <div class="col-span-1 sm:col-span-1">
                        <label for="prefer_install_date" class="block text-sm font-medium text-gray-700"><span
                            class="text-red-500">*</span> Prefer
                          Installation Date </label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                          <span
                            class="z-10 leading-snug font-normal text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                            <i class="fas fa-tools"></i>
                          </span>
                          <input v-model="form.prefer_install_date" type="date" name="prefer_install_date"
                            id="prefer_install_date"
                            class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                            :disabled="checkPerm('prefer_install_date')" />
                        </div>
                      </div>
                      <div class="col-span-1 sm:col-span-1">
                        <label for="installation_date" class="block text-sm font-medium text-gray-700"> Actual Installed
                          Date </label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                          <span
                            class="z-10 leading-snug font-normal absolute text-center text-gray-300 bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                            <i class="fas fa-tools"></i>
                          </span>
                          <input v-model="form.installation_date" type="date" name="installation_date"
                            id="installation_date"
                            class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                            :disabled="checkPerm('installation_date')" />
                        </div>
                        <p v-show="$page.props.errors.installation_date" class="mt-2 text-sm text-red-500">{{
                          $page.props.errors.installation_date }}</p>
                      </div>
                      <div class="col-span-1 sm:col-span-1">
                        <label for="service_activation_date" class="block text-sm font-medium text-gray-700"> Service
                          Activation
                          Date </label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                          <span
                            class="z-10 leading-snug font-normal absolute text-center text-gray-300 bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                            <i class="fas fa-tools"></i>
                          </span>
                          <input v-model="form.service_activation_date" type="date" name="service_activation_date"
                            id="service_activation_date"
                            class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                            :disabled="checkPerm('service_activation_date')" />
                        </div>
                        <p v-show="$page.props.errors.service_activation_date" class="mt-2 text-sm text-red-500">{{
                          $page.props.errors.service_activation_date }}</p>
                      </div>
                      <div class="col-span-1 sm:col-span-1">
                        <label for="subcom" class="block text-sm font-medium text-gray-700"> Installation Team </label>
                        <div class="mt-1 flex rounded-md shadow-sm" v-if="subcoms.length !== 0">
                          <multiselect deselect-label="Selected already" :options="subcoms" track-by="id" label="name"
                            v-model="form.subcom" :allow-empty="true" :disabled="checkPerm('subcom_id')"></multiselect>
                        </div>
                        <p v-show="$page.props.errors.subcom" class="mt-2 text-sm text-red-500">{{
                          $page.props.errors.subcom
                        }}</p>
                      </div>
                      <div class="col-span-1 sm:col-span-1">
                        <label for="pop_site" class="block text-sm font-medium text-gray-700"> Choose POP Site </label>
                        <div class="mt-1 flex rounded-md shadow-sm" v-if="pops.length !== 0">
                          <multiselect deselect-label="Selected already" :options="pops" track-by="id" label="site_name"
                            v-model="form.pop_id" :allow-empty="false" :disabled="checkPerm('sn_id')"
                       >
                          </multiselect>
                        </div>
                      </div>
                      <div class="col-span-1 sm:col-span-1">
                        <label for="pop_device_id" class="block text-sm font-medium text-gray-700"> Choose OLT</label>
                        <div class="mt-1 flex rounded-md shadow-sm" v-if="popDevices?.length  !== 0">
                          <multiselect deselect-label="Selected already" :options="popDevices" track-by="id"
                            label="device_name" v-model="form.pop_device_id" :allow-empty="false"
                            :disabled="checkPerm('pop_device_id')" >
                          </multiselect>
                        </div>
                      </div>
                      <div class="col-span-1 sm:col-span-1">
                        <label for="fiber_distance" class="block text-sm font-medium text-gray-700"> Please Choose DN
                        </label>
                        <div class="mt-1 flex rounded-md shadow-sm" v-if="dnOptions?.length">
                          <multiselect deselect-label="Selected already" :options="dnOptions" track-by="id" label="name"
                            v-model="form.dn_id" :allow-empty="false"  :disabled="checkPerm('sn_id')">
                          </multiselect>
                        </div>

                      </div>

                      <div class="col-span-1 sm:col-span-1">
                        <label for="fiber_distance" class="block text-sm font-medium text-gray-700"> Please Choose SN
                        </label>
                        <div class="mt-1 flex rounded-md shadow-sm" v-if="snOptions.length  !== 0">
                          <multiselect deselect-label="Selected already" :options="snOptions" track-by="id" label="name"
                            v-model="form.sn_id" :allow-empty="true" :disabled="checkPerm('sn_id')"></multiselect>
                        </div>
                      </div>
                      <div class="text-sm text-gray-600 mt-2 sm:col-span-4 col-span-1" v-if="dnInfo">
                        GPON INFO : {{ dnInfo }}
                        <hr class="my-4 md:min-w-full" />
                      </div>


                      <div class="col-span-1 sm:col-span-1">
                        <label for="splitter_no" class="block text-sm font-medium text-gray-700"> SN Port No. </label>
                        <div class="mt-1 flex rounded-md shadow-sm" v-if="snPortNoOptions.length  !== 0">
                          <multiselect deselect-label="Selected already" :options="snPortNoOptions" track-by="id"
                            label="name" v-model="form.splitter_no" :allow-empty="true" :disabled="checkPerm('sn_id')">
                          </multiselect>
                          <p v-show="$page.props.errors.splitter_no" class="mt-2 text-sm text-red-500">{{
                            $page.props.errors.splitter_no }}</p>
                        </div>
                      </div>


                      <div class="col-span-1 sm:col-span-1">
                        <label for="gpon_ontid" class="block text-sm font-medium text-gray-700"> GPON ONTID </label>
                        <div class="mt-1 flex rounded-md shadow-sm" v-if="gponOnuIdOptions.length !== 0">
                          <multiselect deselect-label="Selected already" :options="gponOnuIdOptions" track-by="id"
                            label="name" v-model="form.gpon_ontid" :allow-empty="true"
                            :disabled="checkPerm('gpon_ontid')">
                          </multiselect>
                          <p v-show="$page.props.errors.gpon_ontid" class="mt-2 text-sm text-red-500">{{
                            $page.props.errors.gpon_ontid }}</p>
                        </div>
                      </div>

                      <div class="col-span-1 sm:col-span-1">
                        <label for="port_balance" class="block text-sm font-medium text-gray-700"> Port Balance</label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                          <span
                            class="z-10 leading-snug font-normal text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                            <i class="fas fa-tools"></i>
                          </span>
                          <input v-model="form.port_balance" type="number" name="port_balance" id="port_balance"
                            class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                            placeholder="Enter Port Balance" :disabled="checkPerm('port_balance')" />
                          <p v-show="$page.props.errors.port_balance" class="mt-2 text-sm text-red-500">{{
                            $page.props.errors.port_balance }}</p>
                        </div>
                      </div>

                      <div class="col-span-1 sm:col-span-1">
                        <label for="fiber_distance" class="block text-sm font-medium text-gray-700"> Actual Fiber
                          Distance
                        </label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                          <input v-model="form.fiber_distance" type="number" name="fiber_distance" id="fiber_distance"
                            class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300"
                            :disabled="checkPerm('fiber_distance')" />
                          <span
                            class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                            Meter </span>
                          <p v-show="$page.props.errors.fiber_distance" class="mt-2 text-sm text-red-500">{{
                            $page.props.errors.fiber_distance }}</p>
                        </div>
                      </div>
                      <div class="col-span-1 md:col-span-1">
                        <label for="bundle" class="block text-sm font-medium text-gray-700">
                          Devices
                        </label>
                        <div class="mt-1 flex rounded-md shadow-sm" v-if="bundle_equiptments.length !== 0">
                          <multiselect deselect-label="Selected already" :options="bundle_equiptments" track-by="id"
                            label="name" v-model="form.bundles" :allow-empty="false" :disabled="checkPerm('bundle')"
                            :multiple="true" :taggable="true"></multiselect>
                        </div>
                        <p v-show="$page.props.errors.bundles" class="mt-2 text-sm text-red-500">{{
                          $page.props.errors.bundles }}</p>
                      </div>
                      <div class="col-span-1 sm:col-span-1">
                        <label for="onu_serial" class="block text-sm font-medium text-gray-700"> ONU Serial </label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                          <span
                            class="z-10 leading-snug font-normal text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                            <i class="fas fa-tools"></i>
                          </span>
                          <input v-model="form.onu_serial" type="text" name="onu_serial" id="onu_serial"
                            class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                            :disabled="checkPerm('onu_serial')" />
                        </div>
                        <p v-show="$page.props.errors.onu_serial" class="mt-2 text-sm text-red-500">{{
                          $page.props.errors.onu_serial }}</p>

                      </div>
                      <div class="col-span-1 sm:col-span-1">
                        <label for="onu_power" class="block text-sm font-medium text-gray-700"> ONU Power </label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                          <input type="text" v-model="form.onu_power" name="onu_power" id="onu_power"
                            class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300"
                            :disabled="checkPerm('onu_power')" />
                          <span
                            class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                            dBi </span>
                          <p v-show="$page.props.errors.onu_power" class="mt-2 text-sm text-red-500">{{
                            $page.props.errors.onu_power }}</p>
                        </div>
                      </div>
                      <div class="col-span-1 sm:col-span-1">
                        <label for="vlan" class="block text-sm font-medium text-gray-700"> VLAN </label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                          <span
                            class="z-10 leading-snug font-normal text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                            <i class="fas fa-circle-nodes"></i>
                          </span>
                          <input type="number" v-model="form.vlan" name="vlan" id="vlan"
                            class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                            :disabled="checkPerm('vlan')" />

                          <p v-show="$page.props.errors.vlan" class="mt-2 text-sm text-red-500">{{
                            $page.props.errors.vlan }}</p>
                        </div>
                      </div>
                      <div class="col-span-1 sm:col-span-1">
                        <label for="wlan_ssid" class="block text-sm font-medium text-gray-700"> WLAN SSID Name </label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                          <span
                            class="z-10 leading-snug font-normal text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                            <i class="fas fa-wifi"></i>
                          </span>
                          <input type="text" v-model="form.wlan_ssid" name="wlan_ssid" id="wlan_ssid"
                            class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                            :disabled="checkPerm('wlan_ssid')" />

                          <p v-show="$page.props.errors.wlan_ssid" class="mt-2 text-sm text-red-500">{{
                            $page.props.errors.wlan_ssid }}</p>
                        </div>
                      </div>
                      <div class="col-span-1 sm:col-span-1">
                        <label for="wlan_password" class="block text-sm font-medium text-gray-700"> WLAN SSID Password
                        </label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                          <span
                            class="z-10 leading-snug font-normal text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                            <i class="fas fa-lock"></i>
                          </span>
                          <input type="text" v-model="form.wlan_password" name="wlan_password" id="wlan_password"
                            class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                            :disabled="checkPerm('wlan_password')" />

                          <p v-show="$page.props.errors.wlan_password" class="mt-2 text-sm text-red-500">{{
                            $page.props.errors.wlan_password }}</p>
                        </div>
                      </div>


                      <div class="col-span-1 sm:col-span-1">
                        <label for="pppoe_account" class="block text-sm font-medium text-gray-700"> PPPoE Account <i
                            v-if="pppoe_auto" class="text-red-600 text-xs">(Auto Generated)</i> </label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                          <span :class="pppoe_auto ? 'text-gray-700' : 'text-gray-300'"
                            class="z-10 leading-snug font-normal absolute text-center bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                            <i class="fas fa-tools"></i>
                          </span>
                          <input @click="fillPppoe" v-model="form.pppoe_account" type="text" name="pppoe_account"
                            id="pppoe_account"
                            class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                            :class="pppoe_auto ? 'bg-yellow-200' : 'bg-white'" :disabled="checkPerm('pppoe_account')" />
                        </div>
                        <p v-show="$page.props.errors.pppoe_account" class="mt-2 text-sm text-red-500">{{
                          $page.props.errors.pppoe_account }}</p>
                      </div>
                      <div class="col-span-1 sm:col-span-1">
                        <label for="pppoe_password" class="block text-sm font-medium text-gray-700"> PPPoE Password <i
                            class="ml-2 fa fas fa-sync text-gray-400 hover:text-gray-600 cursor-pointer"
                            @click="generatePassword()"></i> </label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                          <span
                            class="z-10 leading-snug font-normal text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                            <i class="fas fa-tools"></i>
                          </span>
                          <input v-model="form.pppoe_password" type="text" name="pppoe_password" id="pppoe_password"
                            class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                            :disabled="checkPerm('pppoe_password')" />
                        </div>
                        <p v-show="$page.props.errors.pppoe_password" class="mt-2 text-sm text-red-500">{{
                          $page.props.errors.pppoe_password }}</p>
                      </div>

                      <div class="col-span-1 sm:col-span-1">
                        <label for="latitude" class="block text-sm font-medium text-gray-700">Latitude </label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                          <span
                            class="z-10 leading-snug font-normal text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-3">
                            <i class="fas fa-location-arrow"></i>
                          </span>
                          <input type="text" v-model="form.latitude" name="latitude" id="latitude"
                            class="pl-10 mt-1 form-input focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                            v-on:keypress="isNumber(event)" :disabled="checkPerm('location')" />
                        </div>
                        <p v-show="$page.props.errors.latitude" class="mt-2 text-sm text-red-500">{{
                          $page.props.errors.latitude }}</p>
                      </div>
                      <div class="col-span-1 sm:col-span-1">
                        <label for="longitude" class="block text-sm font-medium text-gray-700">Longitude </label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                          <span
                            class="z-10 leading-snug font-normal  text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-3">
                            <i class="fas fa-location-arrow"></i>
                          </span>
                          <input type="text" v-model="form.longitude" name="longitude" id="longitude"
                            class="pl-10 mt-1 form-input focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                            v-on:keypress="isNumber(event)" :disabled="checkPerm('location')" />
                        </div>
                        <p v-show="$page.props.errors.longitude" class="mt-2 text-sm text-red-500">{{
                          $page.props.errors.longitude }}</p>
                      </div>

                      <div class="col-span-1 sm:col-span-2">
                        <label for="installation_remark" class="block text-sm font-medium text-gray-700"> Installation
                          Remark </label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                          <span
                            class="z-10 leading-snug font-normal text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                            <i class="fas fa-comment"></i>
                          </span>
                          <textarea name="installation_remark" v-model="form.installation_remark"
                            id="installation_remark"
                            class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                            :disabled="checkPerm('installation_remark')" />
                        </div>
                        <p v-show="$page.props.errors.installation_remark" class="mt-2 text-sm text-red-500">{{
                          $page.props.errors.installation_remark }}</p>
                      </div>
                    </div>

                  </div>

                  <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                    <Link :href="route('customer.index')"
                      class="inline-flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 shadow-sm focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150">Back</Link>

                    <button @click="resetForm" type="button"
                      class="ml-2 inline-flex justify-center py-2 px-4 border border-transparent  text-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 shadow-sm focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150">Reset</button>

                    <button type="submit"
                      class="ml-2 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Save</button>
                  </div>
                </div> <!-- tab1 -->
                <div class="p-4" :class="[tab == 2 ? '' : 'hidden']">
                  <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                    <h6 class="md:min-w-full text-indigo-700 text-xs uppercase font-bold block pt-1 no-underline">
                      Customer
                      Documents</h6>
                    <hr class="my-4 md:min-w-full" />
                    <keep-alive>
                      <customer-file :data="form.id" :permission="!checkPerm('order_date')" v-if="tab == 2" />
                    </keep-alive>

                  </div>
                </div>
                <div class="p-4" :class="[tab == 3 ? '' : 'hidden']">
                  <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                    <h6 class="md:min-w-full text-indigo-700 text-xs uppercase font-bold block pt-1 no-underline">
                      Customer
                      History</h6>
                    <hr class="my-4 md:min-w-full" />
                    <keep-alive>
                      <customer-history :data="form.id" :permission="!checkPerm('order_date')" v-if="tab == 3" />
                    </keep-alive>

                  </div>
                </div>
                <div class="p-4" :class="[tab == 4 ? '' : 'hidden']">
                  <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                    <h6 class="md:min-w-full text-indigo-700 text-xs uppercase font-bold block pt-1 no-underline">Public
                      IP
                    </h6>
                    <hr class="my-4 md:min-w-full" />

                    <customer-ip :data="form.id" :permission="!checkPerm('order_date')" v-if="tab == 4" />


                  </div>
                </div>
                <div class="p-4" :class="[tab == 5 ? '' : 'hidden']" v-if="radius">
                  <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                    <h6 class="md:min-w-full text-indigo-700 text-xs uppercase font-bold block pt-1 no-underline">Radius
                    </h6>
                    <hr class="my-4 md:min-w-full" />

                    <customer-radius :data="form.id" :permission="!checkPerm('order_date')" v-if="tab == 5" />


                  </div>
                </div>
                <div class="p-4" :class="[tab == 6 ? '' : 'hidden']">
                  <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                    <div class="w-full flex justify-between">
                      <h6 class="text-indigo-700 text-xs uppercase font-bold block pt-1 no-underline">Text

                      </h6>
                      <span class="gap-2">
                        <a href="#" @click="copyText()">Copy <i
                            class="ml-2 fa text-xl fa-copy text-right text-indigo-500 hover:text-indigo-700 cursor-pointer"></i></a>
                        |
                        <a href="#" @click="shareText()">
                          Share <i
                            class="fa text-xl fa-share-nodes text-right text-indigo-500 hover:text-indigo-700 cursor-pointer"></i>
                        </a>
                      </span>


                    </div>

                    <hr class="my-4 md:min-w-full" />

                    <div id="text-code" ref="textContent">
                      Sales: {{ form.sale_person.name }}<br />
                      Sales Source – {{ form.sale_channel }}<br />
                      Customer ID – {{ form.ftth_id }}<br />
                      Service Order Date - {{ form.order_date }}<br />
                      Customer Name – {{ form.name }}<br />
                      Contact Number – {{ form.phone_1 }} {{ form.phone_2 }}<br />
                      Contact E Mail - {{ form.email }}<br />
                      Township – {{ form.township['name'] }}<br />
                      Fully Address – {{ form.address }}<br />
                      Location – {{ form.latitude }},{{ form.longitude }} <br />
                      Applied Mbps – {{ form.package.name }} ({{ form.package.speed }}
                      Mbps)<br />
                      MRC – {{ form.package.price }} <span class="uppercase">{{ form.package.currency }}</span><br />
                      Preferred installation date & time – {{ form.prefer_install_date }}<br />
                      <span v-if="form.sale_remark">, {{ form.sale_remark }}</span>
                      <hr />
                      Devices - {{ bundle }}<br />
                      GPON Info - {{ gponInfo }} <br />
                      DN - {{ form.dn_id?.name }}<br />
                      SN - {{ form.sn_id?.name }}<br />
                      SN Port - {{ snPort }}<br />
                    </div>

                  </div>
                </div>

              </div> <!-- Tab Contents -->
            </div>
          </form>
        </div>
        <!-- end of mt-5 md: -->
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import Multiselect from "@suadelabs/vue3-multiselect";
import {  ref, onMounted, provide,watch } from "vue";
import CustomerFile from "@/Components/CustomerFile";
import CustomerHistory from "@/Components/CustomerHistory";
import CustomerRadius from "@/Components/CustomerRadius";
import CustomerIp from "@/Components/CustomerIP";
import { router,useForm,Link } from "@inertiajs/vue3";
export default {
  name: "EditCustomer",
  components: {
    AppLayout,
    CustomerFile,
    Multiselect,
    CustomerRadius,
    CustomerHistory,
    CustomerIp,
    Link
  },
  props: {
    packages: Object,
    sale_persons: Object,
    townships: Object,
    projects: Object,
    errors: Object,
    customer: Object,
    status_list: Object,
    subcoms: Object,
    roles: Object,
    users: Object,
    user: Object,
    radius: Object,
    customer_history: Object,
    pops: Object,
    role: Object,
    total_documents: Object,
    total_ips: Object,
    bundle_equiptments: Object,
  },
  setup(props) {
    provide('role', props.role);
    let res_dn = ref("");
    let res_sn = ref("");
    let pop = ref("");
    let pop_devices = ref("");
    let pppoe_auto = ref(false);
    let lat_long = '';
    const bundle = ref(null);
    const dnInfo = ref(null);
    const textContent = ref(null);
    const gponInfo = ref(null);
    const dnName = ref(null);
    const snName = ref(null);
    const snPort = ref(null);
    const snPortNoOptions = ref(
      Array.from({ length: 16 }, (v, i) => ({ id: i + 1, name: `SN Port ${i + 1}` }))
    );

    const gponOnuIdOptions = ref(
      Array.from({ length: 127 }, (v, i) => ({ id: i, name: `OnuID${i}` }))
    );
    if (props.customer.location) {
      lat_long = props.customer.location.split(",", 2);
    }

    let tab = ref(1);
    let selected_id = ref("");
    const popDevices = ref([]);
    const dnOptions = ref([]);
    const snOptions = ref([]);    
    function tabClick(val) {
      if (selected_id.value != null)
        tab.value = val;
    }

    const form = useForm({
      id: props.customer.id,
      name: props.customer.name,
      nrc: props.customer.nrc,
      phone_1: props.customer.phone_1,
      phone_2: props.customer.phone_2,
      email: props.customer.email,
      address: props.customer.address,
      latitude: (lat_long[0]) ? lat_long[0] : '',
      longitude: (lat_long[1]) ? lat_long[1] : '',
      order_date: props.customer.order_date,
      sale_channel: props.customer.sale_channel,
      sale_remark: props.customer.sale_remark,
      installation_date: props.customer.installation_date,
      service_activation_date: props.customer.service_activation_date,
      ftth_id: props.customer.ftth_id,
      township: "",
      sale_person: "",
      package: "",
      project: "",
      status: "",
      subcom: "",
      prefer_install_date: props.customer.prefer_install_date,

      splitter_no: props.customer.splitter_no,
      installation_remark: props.customer.installation_remark,
      fc_used: props.customer.fc_used,
      fc_damaged: props.customer.fc_damaged,
      onu_serial: props.customer.onu_serial,
      onu_power: props.customer.onu_power,
      contract_term: props.customer.contract_term,
      foc: (props.customer.foc) ? true : false,
      foc_period: props.customer.foc_period,
      advance_payment: props.customer.advance_payment,
      advance_payment_day: props.customer.advance_payment_day,
      extra_bandwidth: props.customer.extra_bandwidth,
      fiber_distance: props.customer.fiber_distance,
      pppoe_account: props.customer.pppoe_account,
      pppoe_password: "",
      customer_type: props.customer.customer_type,

      vlan: props.customer.vlan,
      wlan_ssid: props.customer.wlan_ssid,
      wlan_password: "",
      social_account: props.customer.social_account,
      bundles: "",
      email: props.customer.email,

      gpon_ontid: props.customer.gpon_ontid,
      port_balance: props.customer.port_balance,

      pop_id: props.customer.pop,
      pop_device_id: props.customer.pop_device,
      dn_id: props.customer.dn,
      sn_id: props.customer.sn,
    });

    function submit() {
      form._method = "PUT";
      router.post("/customer/" + form.id, form, {
        onSuccess: (page) => {
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
    function checkPerm(data) {
      const my_role = props.roles.filter((d) => d.id == props.users.role)[0];
      if (my_role.read_customer) {
        return true;
      }
      let role_arr = my_role.permission.split(',');
      let disable = role_arr.includes(data);
      return !disable;
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
    




    function fillPppoe() {
      if (!form.pppoe_account) {
        if (form.ftth_id && form.township) {
          // let dn_no = getNumber(form.dn_id['name']);
          // let sn_no = getNumber(form.sn_id['name']);
          // let city_code = form.township['city_code'];
          // var data = getNumber(form.ftth_id);
          // let psw = dn_no.toString() + sn_no.toString() + ('00000' + (parseInt(data))).slice(-5);
          // let pppoe = city_code + psw + '@FIP';
          form.pppoe_account = form.ftth_id;
          pppoe_auto.value = true;
        }
      }

    }
    function getAbbreviation(name) {
      // Remove any text inside parentheses
      name = name.replace(/\(.*?\)/g, '').trim();

      // Split the name by spaces to get individual words
      const words = name.split(/\s+/);

      // Initialize an abbreviation string
      let abbreviation = '';

      // Loop through the words to construct the abbreviation
      for (let i = 0; i < words.length; i++) {
        // Only add the first letter of each word until abbreviation reaches 3 characters
        abbreviation += words[i][0].toUpperCase();
        if (abbreviation.length === 3) break;
      }

      // Get the current year
      const currentYear = new Date().getFullYear();

      // If the abbreviation is less than 3 characters, pad it (optional) and add the current year
      return abbreviation.padEnd(3, abbreviation[abbreviation.length - 1] || '') + '@' + currentYear + 'FIP';
    }
    function generatePassword() {
      // var chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
      // var passwordLength = 8;
      // var password = "";
      // for (var i = 0; i <= passwordLength; i++) {
      //   var randomNumber = Math.floor(Math.random() * chars.length);
      //   password += chars.substring(randomNumber, randomNumber + 1);
      // }
      if (!form.pppoe_password) {
        // if (form.ftth_id) {
        //   form.pppoe_password = password;
        // }
        if (form.ftth_id && form.name && form.pppoe_account) {

          form.pppoe_password = getAbbreviation(form.name);
        }
      }
    }
    function getNumber(data) {
      const string = data;
      const regex = /\d+/g;
      const matches = string.match(regex);
      const integerValue = matches ? parseInt(matches.join('')) : 0;
      return integerValue;
    }
 
   
    // Fetch OLTs by POP
    const fetchPopDevices = async () => {
      if (!form.pop_id) {
        popDevices.value = [];
        return;
      }
      try {
        const response = await fetch(`/getOLTByPOP/${form.pop_id['id']}`);
        const data = await response.json();
        popDevices.value = data || [];
      } catch (error) {
        console.error("Failed to fetch POP devices", error);
      }
    };

    // Fetch DNs by OLT
    const fetchDNs = async () => {
      if (!form.pop_device_id) {
        dnOptions.value = [];
        return;
      }
      try {
        const response = await fetch(`/getDNByOLT/${form.pop_device_id['id']}`);
        const data = await response.json();
        dnOptions.value = data || [];
      } catch (error) {
        console.error("Failed to fetch DNs", error);
      }
    };

    // Fetch SNs by DN
    const fetchSNs = async () => {
      if (!form.dn_id) {
        snOptions.value = [];
        return;
      }
      try {
        const response = await fetch(`/getDnId/${form.dn_id['id']}`);
        const data = await response.json();
        snOptions.value = data || [];
      } catch (error) {
        console.error("Failed to fetch SNs", error);
      }
    };

    watch(
      () => form.pop_id,
      ()=>{
        fetchPopDevices();
        form.sn_id = null;
        form.dn_id = null;
        form.pop_device_id = null;
        form.gpon_frame = null;
        form.gpon_slot = null;
        form.gpon_port = null;
        form.gpon_ontid = null;
        form.port_balance = null;
      }
    );
    watch(
      () => form.pop_device_id,
      () => {
        fetchDNs(); 
        form.dn_id = null;
        form.sn_id = null;
      }
    );
    watch(
      () => form.dn_id,
      () => {
        fetchSNs(); 
        dnInfo.value = `Frame${form.dn_id.gpon_frame}/Slot${form.dn_id.gpon_slot}/Port${form.dn_id.gpon_port}`;
        form.sn_id = null;
      }
    );
    function isEmptyObject(value) {
      // Check if it's an array
      if (Array.isArray(value)) {
        console.log('array');
        // If the array is empty, return true
        if (value.length === 0) {
          console.log('empty array');
          return true;
        }
        // Check if the array contains only empty objects
        return value.every(item => typeof item === 'object' && Object.keys(item).length === 0);
      }

      // Check if it's an object
      if (value && typeof value === 'object') {
        return Object.keys(value).length === 0;
      }

      // If it's neither an object nor an array, return false
      return false;
    }
    const copyText = () => {
      if (textContent.value) {
        // Get the text content from the <code> element
        let text = textContent.value.innerText;
        // Use the Clipboard API to copy the text content
        navigator.clipboard.writeText(text)
          .then(() => {
            // Optionally, you can show a success message or feedback to the user
            alert('Text copied to clipboard!');
          })
          .catch(err => {
            // Handle any errors that occur during the copy
            console.error('Failed to copy text: ', err);
          });
      } else {
        console.error('Text content ref is undefined.');
      }
    };
    const shareText = () => {
      if (textContent.value) {
        // Get the text content from the <code> element
        let text = textContent.value.innerText;
        // Use the Clipboard API to copy the text content
        if (navigator.share) {
          navigator.share({
            title: 'Customer Information',
            text: text
          }).then(() => {
            // Optionally, you can show a success message or feedback to the user
            alert('Text copied to clipboard!');
          })
            .catch(err => {
              // Handle any errors that occur during the copy
              console.error('Failed to copy text: ', err);
            });
        }


      } else {
        console.error('Text content ref is undefined.');
      }
    };
    onMounted(() => {


      form.project = props.projects.filter((d) => d.id == props.customer.project_id)[0];
      form.package = props.packages.filter((d) => d.id == props.customer.package_id)[0];
      form.township = props.townships.filter((d) => d.id == props.customer.township_id)[0];
      form.sale_person = props.sale_persons.filter((d) => d.id == props.customer.sale_person_id)[0] || null;

      if (props.customer.bundle) {
              let bundleArray = props.customer.bundle.split(",");
              let bundleLists = [];
              
              bundleArray.forEach(e => {
                // 1) Find the matching equipment object
                const foundEquip = props.bundle_equiptments.find(d => d.id == e);

                // 2) If it exists, push to array & build your bundle.value string
                if (foundEquip) {
                  bundleLists.push(foundEquip);

                  if (!bundle.value) {
                    bundle.value = foundEquip.name;
                  } else {
                    bundle.value = bundle.value + ',' + foundEquip.name;
                  }
                }
                // 3) (Optionally) handle the "not found" case
                else {
                  console.warn(`Equipment with ID ${e} was not found`);
                }
              });

              form.bundles = bundleLists;
            }
      if (props.customer.splitter_no) {
        form.splitter_no = snPortNoOptions.value.filter((d) => d.id == props.customer.splitter_no)[0];
        snPort.value = form.splitter_no.name;
      }
      if (props.customer.gpon_ontid) {
        form.gpon_ontid = gponOnuIdOptions.value.filter((d) => d.name == props.customer.gpon_ontid)[0];
      }
     
      form.pppoe_password = (!checkPerm('pppoe_password')) ? props.customer.pppoe_password : "********";
      form.wlan_password = (!checkPerm('wlan_password')) ? props.customer.wlan_password : "********";
      form.status = props.status_list.filter((d) => d.id == props.customer.status_id)[0];
      form.subcom = props.subcoms.filter((d) => d.id == props.customer.subcom_id)[0];

      //DN SN
      if (form.pop_id) {
        fetchPopDevices();
      }
      if (form.pop_device_id){
        fetchDNs();
      }
      if (form.dn_id) {
        fetchSNs();
        dnInfo.value = `Frame${form.dn_id.gpon_frame}/Slot${form.dn_id.gpon_slot}/Port${form.dn_id.gpon_port}`;
      }

      if (props.customer.pop_device_id && dnInfo.value && props.customer.sn_id) {
        gponInfo.value = `${dnInfo.value}`;
      }
      if (gponInfo.value && props.customer.gpon_ontid) {
        gponInfo.value += '/' + gponOnuIdOptions.value.filter((d) => d.name == props.customer.gpon_ontid)[0].name;
      }

    });

    return {
      form, submit, isNumber, checkPerm, tab, tabClick, fillPppoe, pppoe_auto, generatePassword, isEmptyObject, pop_devices, snPortNoOptions, gponOnuIdOptions, dnInfo, bundle, shareText, copyText, textContent,
      gponInfo,
      dnName,
      snName,
      snPort,
      popDevices,
      dnOptions,
      snOptions,
    };
  },
};
</script>
