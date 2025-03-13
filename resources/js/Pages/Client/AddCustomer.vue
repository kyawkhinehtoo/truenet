<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">Customer Details</h2>
    </template>
    <div class="py-2">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mt-5 md:mt-0">
          <form @submit.prevent="submit">
            <div class="shadow sm:rounded-md sm:overflow-hidden">
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
                        :disabled="checkPerm('township_id')" :onchange="goID" @select="goID" @close="goID" required>
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
                        :disabled="checkPerm('project_id')" :onchange="goID" @select="goID" @close="goID">
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
                      <multiselect deselect-label="Selected already" :options="status_list" track-by="id" label="name"
                        v-model="form.status" :allow-empty="false" :disabled="checkPerm('status_id')">
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
                      <multiselect deselect-label="Selected already" :options="sale_persons" track-by="id" label="name"
                        v-model="form.sale_person" :allow-empty="false" :disabled="checkPerm('sale_person_id')">
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
                    <div class="mt-1 flex rounded-md shadow-sm" v-if="packages">
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
                      <input type="number" v-model="form.extra_bandwidth" name="extra_bandwidth" id="extra_bandwidth"
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
                        @change="goID" :disabled="checkPerm('customer_type')">
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
                    <label for="pop_site" class="block text-sm font-medium text-gray-700">Choose POP Site </label>
                    <div class="mt-1 flex rounded-md shadow-sm" v-if="pops.length !== 0">
                      <multiselect deselect-label="Selected already" :options="pops" track-by="id" label="site_name"
                        v-model="form.pop_id" :allow-empty="false" :disabled="checkPerm('sn_id')"
                   >
                      </multiselect>
                    </div>
                  </div>
                  <div class="col-span-1 sm:col-span-1">
                    <label for="pop_device_id" class="block text-sm font-medium text-gray-700"> Choose OLT</label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                      <multiselect deselect-label="Selected already" :options="popDevices" track-by="id"
                      label="device_name" v-model="form.pop_device_id" :allow-empty="false"
                      :disabled="checkPerm('pop_device_id')" >
                      </multiselect>
                    </div>
                  </div>
                  <div class="col-span-1 sm:col-span-1">
                    <label for="fiber_distance" class="block text-sm font-medium text-gray-700"> Please Choose DN
                    </label>
                    <div class="mt-1 flex rounded-md shadow-sm" v-if="dnOptions">
                      <multiselect deselect-label="Selected already" :options="dnOptions" track-by="id" label="name"
                      v-model="form.dn_id" :allow-empty="false"  :disabled="checkPerm('sn_id')">
                    </multiselect>
                    </div>
                  </div>

                  <div class="col-span-1 sm:col-span-1">
                    <label for="fiber_distance" class="block text-sm font-medium text-gray-700"> Please Choose SN
                    </label>
                    <div class="mt-1 flex rounded-md shadow-sm" v-if="snPortNoOptions">
                      <multiselect deselect-label="Selected already" :options="snPortNoOptions" track-by="id"
                      label="name" v-model="form.sn_id" :allow-empty="true" :disabled="checkPerm('sn_id')">
                    </multiselect>
                    </div>
                  </div>
                  <div class="text-sm text-gray-600 mt-2  col-span-4" v-if="dnInfo">
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
                    <div class="mt-1 flex rounded-md shadow-sm">
                      <multiselect deselect-label="Selected already" :options="gponOnuIdOptions" track-by="id"
                        label="name" v-model="form.gpon_ontid" :allow-empty="true" :disabled="checkPerm('gpon_ontid')">
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
                      <textarea name="installation_remark" v-model="form.installation_remark" id="installation_remark"
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
                  class="inline-flex justify-center py-2 px-4 border border-transparent  text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 shadow-sm focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150">Back</Link>

                <button @click="resetForm" type="button"
                  class="ml-2 inline-flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 shadow-sm focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150">Reset</button>

                <button wire:click.prevent="submit" type="submit"
                  class="ml-2 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Save</button>
              </div>
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
import { reactive, ref, onMounted, watch } from "vue";
import { router,Link,useForm } from '@inertiajs/vue3';
export default {
  name: "AddCustomer",
  components: {
    AppLayout,
    Multiselect,
    Link
  },
  props: {
    packages: Object,
    sale_persons: Object,
    townships: Object,
    projects: Object,
    status_list: Object,
    subcoms: Object,
    roles: Object,
    users: Object,
    dn: Object,
    pops: Object,
    max_id: Array,
    errors: Object,
    role: Object,
    bundle_equiptments: Object,
  },
  setup(props) {

    let pop_devices = ref("");
    let res_packages = ref("");
    let pppoe_auto = ref(false);
    const dnInfo = ref(null);

    const popDevices = ref([]);
    const dnOptions = ref([]);
    const snOptions = ref([]);    

    const form = useForm({
      id: null,
      name: "",
      nrc: "",
      phone_1: "",
      phone_2: "",
      address: "",
      latitude: "",
      longitude: "",
      order_date: "",
      sale_channel: "",
      sale_remark: "",
      installation_date: "",
      ftth_id: "",
      sale_person: "",
      package: "",
      status: "",
      subcom: "",
      township: "",
      prefer_install_date: "",
      pop_id: "",
      dn_id: "",
      sn_id: "",
      splitter_no: "",
      installation_remark: "",
      fc_used: "",
      fc_damaged: "",
      onu_serial: "",
      onu_power: "",
      contract_term: "",
      foc: "",
      foc_period: "",
      advance_payment: "",
      advance_payment_day: "",
      extra_bandwidth: "",
      fiber_distance: "",
      pppoe_account: "",
      pppoe_password: "",
      customer_type: 1,

      vlan: "",
      wlan_ssid: "",
      wlan_password: "",
      social_account: "",
      bundles: "",
      email: "",
      pop_device_id: "",
      gpon_ontid: "",
      port_balance: "",
    });

    function resetForm() {
      form.reset();
    }

    // SN Port Number
    const snPortNoOptions = ref(
      Array.from({ length: 16 }, (v, i) => ({ id: i + 1, name: `SN Port ${i + 1}` }))
    );

    const gponOnuIdOptions = ref(
      Array.from({ length: 127 }, (v, i) => ({ id: i, name: `OnuID${i}` }))
    );


    function submit() {
      form._method = "POST";
      router.post("/customer", form, {
        preserveState: true,
        onSuccess: (page) => {
          console.log("success.. ");
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
    function isNumber(evt) {
      evt = evt ? evt : window.event;
      var charCode = evt.which ? evt.which : evt.keyCode;
      if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode !== 46) {
        evt.preventDefault();
      } else {
        return true;
      }
    }
    function checkPerm(data) {
      const my_role = props.roles.filter((d) => d.id == props.role.id)[0];
      let role_arr = my_role.permission.split(",");
      let disable = role_arr.includes(data);
      return !disable;
    }

    function goID() {
      let city_code = form.township['city_code'];
      let city_id = form.township['city_id'];
      var data = props.max_id.filter((id) => id.id == city_id)[0];
      form.ftth_id = city_code + ('000000' + (parseInt(data.value) + 1)).slice(-6) + 'FX';
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
    onMounted(() => {
      form.township = props.townships.filter((d) => d.id == 1)[0];
      form.sale_person = props.sale_persons[0];

      form.status = props.status_list[0];
      goID();
    });
    return { form, submit, resetForm, isNumber, checkPerm, goID, fillPppoe, pppoe_auto, generatePassword,  res_packages, isEmptyObject, pop_devices, snPortNoOptions, gponOnuIdOptions, dnInfo,popDevices, dnOptions,snOptions };
  },
};
</script>
<style>
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

/* Firefox */
input[type="number"] {
  -moz-appearance: textfield;
}

/* .multiselect__input, .multiselect__single{
    outline:none !important;
    font-size: 0.875rem !important;
    line-height: 1.25rem !important;
    margin-bottom: 2px !important;
    margin-top: 2px !important;
}
.multiselect__input select{
    font-size: 0.875rem !important;
}
.multiselect__input:focus{
    outline-offset: 0;
    padding-top:0px;
} */
</style>
