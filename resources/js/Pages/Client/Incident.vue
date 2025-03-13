<template>
  <app-layout>
    <div class="py-1">
      <div class="mx-auto sm:px-6 lg:px-8">

        <div class="py-2 md:inline-flex justify-between w-full gap-4">
          <div class="flex w-full">
            <span
              class="z-10  font-normal text-gray-400 absolute bg-transparent rounded text-base items-center justify-center  w-8 pl-3 py-4">
              <i class="fas fa-search"></i>
            </span>
            <input type="text" placeholder="Ticket/Customer"
              class="relative self-center block w-full pr-12 py-2 rounded-md text-gray-400 text-sm border-gray-300 focus:ring focus:ring-indigo-500 focus:border-indigo-500 focus:ring-opacity-10 focus:outline-none pl-10"
              id="search" tabindex="1" v-model="search" v-on:keyup.enter="searchIncident" />
          </div>

          <div class="flex w-full">
            <span
              class="z-10  font-normal text-gray-400 absolute bg-transparent rounded text-base items-center justify-center  w-8 pl-3 py-4">
              <i class="fas fa-sliders-h"></i>
            </span>
            <select v-model="incidentType"
              class="relative self-center block w-full pr-12 py-2 rounded-md text-gray-400 text-sm border-gray-300 focus:ring focus:ring-indigo-500 focus:border-indigo-500 focus:ring-opacity-10 focus:outline-none pl-10"
              tabindex="3" @change="changeStatus">
              <option value="default">All Ticket</option>
              <option value="service_complaint">Service Complaint</option>
              <option value="onsite_complaint">Onsite Complaint</option>
              <option value="technical_complaint">Technical Complaint</option>
              <option value="plan_change">Plan Change</option>
              <option value="suspension">Suspension</option>
              <option value="termination">Termination</option>
            </select>
          </div>

          <div class="flex w-full">
            <span
              class="z-10  font-normal text-gray-400 absolute bg-transparent rounded text-base items-center justify-center  w-8 pl-3 py-4">
              <i class="fas fa-sliders-h"></i>
            </span>
            <select v-model="incidentBy"
              class="relative self-center block w-full pr-12 py-2 rounded-md text-gray-400 text-sm border-gray-300 focus:ring focus:ring-indigo-500 focus:border-indigo-500 focus:ring-opacity-10 focus:outline-none pl-10"
              tabindex="4" @change="changeStatus">
              <option value="default">All Member</option>
              <option v-for="row in noc" v-bind:key="row.id" :value="row.id">{{ row.name }}</option>

            </select>
          </div>
          <div class="flex w-full">
            <span
              class="z-10  font-normal text-gray-400 absolute bg-transparent rounded text-base items-center justify-center  w-8 pl-3 py-4">
              <i class="fas fa-sliders-h"></i>
            </span>
            
            <select v-model="incidentStatus"
              class="relative self-center block w-full pr-12 py-2 rounded-md text-gray-400 text-sm border-gray-300 focus:ring focus:ring-indigo-500 focus:border-indigo-500 focus:ring-opacity-10 focus:outline-none pl-10"
              tabindex="5" @change="changeStatus">
              <option value="0">All Status</option>
              <option value="1">Open</option>
              <option value="2">Escalated</option>
              <option value="5">Resolved</option>
              <option value="3">Closed</option>
              <option value="4">Deleted</option>
            </select>
          </div>
          <div class="flex w-full self-center">
             
            <VueDatePicker v-model="incidentDate" :range="{ partialRange: true }" placeholder="Ticket Date" 
              :enable-time-picker="false" model-type="yyyy-MM-dd" id="order_date"
              class="text-gray-400 text-sm focus:ring focus:ring-indigo-500 focus:border-indigo-500 focus:ring-opacity-10 focus:outline-none" />
          </div>
          <div class="flex w-full md:w-1/4">
            <button @click="changeStatus"
              class="text-center self-center w-full mt-2 md:w-auto md:mt-0 items-center px-4 py-3 bg-indigo-500 border border-transparent rounded-sm font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-400 active:bg-indigo-600 focus:border-litepie-primary-300 focus:ring focus:ring-litepie-primary-500 focus:ring-opacity-10 focus:outline-none disabled:opacity-25 transition mr-1"><i
                class="fas fa-search opacity-75 lg:ml-1 text-sm"></i></button>
          </div>
          <div class="flex w-full md:w-1/2 md:justify-end">
            <button @click="newTicket()"
              class="text-center self-center w-full mt-2 md:w-auto md:mt-0 items-center px-4 py-3 bg-indigo-500 border border-transparent rounded-sm font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-400 active:bg-indigo-600 focus:border-litepie-primary-300 focus:ring focus:ring-litepie-primary-500 focus:ring-opacity-10 focus:outline-none disabled:opacity-25 transition mr-1"
              v-if="permission[0].write_incident == 1" tabindex="7">Ticket<i
                class="fas fa-plus-circle opacity-75 lg:ml-1 text-sm"></i></button>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-6 gap-6 w-full"
          v-if="permission[0].read_incident == 1 || permission[0].write_incident == 1">
          <!--ticket list -->

          <div class="lg:col-span-4 md:col-span-6">

            <div class="bg-white overflow-auto md:overflow-hidden shadow-xl sm:rounded-lg mt-1" v-if="incidents.data">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th scope="col"
                      class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                    <th scope="col"
                      class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No.</th>
                    <th scope="col"
                      class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                      @click="sortBy('date')">Open Date <i class="fas fa-sort text-gray-400"></i></th>
                    <th scope="col"
                      class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                      @click="sortBy('code')">Ticket <i class="fas fa-sort text-gray-400"></i></th>
                    <th scope="col"
                      class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                      @click="sortBy('ftth_id')">User <i class="fas fa-sort text-gray-400"></i></th>
                    <th scope="col"
                      class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                      @click="sortBy('township_id')">TSP <i class="fas fa-sort text-gray-400"></i></th>
                    <th scope="col"
                      class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                      @click="sortBy('type')">Type <i class="fas fa-sort text-gray-400"></i></th>
                    <th scope="col"
                      class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                      @click="sortBy('incharge')">Created <i class="fas fa-sort text-gray-400"></i></th>
                    <th scope="col"
                      class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 text-sm">
                  <tr v-for="(row, index) in incidents.data" v-bind:key="row.id"
                    :class="[row.id == selected_id ? 'bg-indigo-200' : '']" @click="edit(row)" class="cursor-pointer">
                    <td class="px-2 py-3 whitespace-nowrap"><i :class="'fa fa-circle text-' + row.color"></i></td>
                    <td class="px-2 py-3 whitespace-nowrap">{{ (index += incidents.from) }}</td>
                    <td class="px-2 py-3 whitespace-nowrap">{{ row.date }} {{ row.time }}</td>
                    <td class="px-2 py-3 whitespace-nowrap">{{ row.code }}</td>
                    <td class="px-2 py-3 whitespace-nowrap" v-if="row.ftth_id">{{ row.ftth_id }}</td>
                    <td class="px-2 py-3 whitespace-nowrap" v-if="row.township_short">{{ row.township_short }}</td>
                    <td class="px-2 py-3 whitespace-nowrap capitalize">{{ row.type.replace("_", " ") }}</td>
                    <td class="px-2 py-3 whitespace-nowrap">{{ row.incharge.match(/\b\w/g).join("") }}</td>
                    <td class="px-2 py-3 whitespace-nowrap">{{ getStatus(row.status) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
            <span v-if="incidents.total" class="w-full block mt-4">
              <label class="text-xs text-gray-600">{{ incidents.data.length }} Tickets in Current Page. Total Number of
                Tickets : {{ incidents.total }}</label>
            </span>
            <span v-if="incidents.links">
              <pagination class="mt-4" :links="incidents.links" />
            </span>
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mt-1 py-4 flex flex-col items-center"
              v-if="!incidents.data">
              <no-data />
            </div>
          </div>
          <!-- end of ticket list -->
          <!--alarm panel -->
          <div class="col-span-2">
            <div class="bg-white rounded-lg w-full mx-auto mt-1 shadow-xl divide-y divide-gray-200 py-2 px-2">
              <div class="grid grid-cols-4 gap-2">
                <div class="col-span-1 ">
                  <label class="block text-sm font-normal text-center">Critical</label>
                  <button
                    class="block py-2 px-2 w-full bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-400 active:bg-red-600 focus:border-red-300 focus:ring focus:ring-red-500 focus:ring-opacity-10 focus:outline-none disabled:opacity-25 transition"
                    @click="showPriority('critical')">{{ critical }}</button>
                </div>
                <div class="col-span-1" @click="showPriority('high')">
                  <label class="block text-sm font-normal text-center">High</label>
                  <button
                    class="block py-2 px-2 w-full bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-500 active:bg-yellow-700 focus:border-yellow-300 focus:ring focus:ring-yellow-500 focus:ring-opacity-10 focus:outline-none disabled:opacity-25 transition"
                    @click="showPriority('high')">{{ high }}</button>
                </div>
                <div class="col-span-1" @click="showPriority('normal')">
                  <label class="block text-sm font-normal text-center">Normal</label>
                  <button
                    class="block py-2 px-2 w-full bg-yellow-400 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-300 active:bg-yellow-500 focus:border-yellow-300 focus:ring focus:ring-yellow-500 focus:ring-opacity-10 focus:outline-none disabled:opacity-25 transition"
                    @click="showPriority('normal')">{{ normal }}</button>
                </div>
                <div class="col-span-1">
                  <label class="block text-sm font-normal text-center">All</label>
                  <button
                    class="block py-2 px-2 w-full bg-indigo-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-400 active:bg-indigo-600 focus:border-indigo-300 focus:ring focus:ring-indigo-500 focus:ring-opacity-10 focus:outline-none disabled:opacity-25 transition"
                    @click="showPriority('all')">{{ critical + high + normal }}</button>
                </div>
              </div>
            </div>
            <incident-alert @show_edit="alert_edit" />
          </div>
          <!--end of alarm panel -->
        </div>
        <div class="flex justify-center" v-else>
          <span class="px-20 py-10 center bg-white inline-flex">
            <i class="fa fas fa-exclamation-triangle fa-2x text-yellow-600 mr-2"></i>
            <label class="font-bold mt-2 text-sm"> You Don't Have Permission To Access This Page</label>
          </span>
        </div>
      </div>
    </div>
    <div ref="isOpen" class="fixed z-10 inset-0 overflow-y-auto ease-out duration-400" v-if="isOpen">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity">
          <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        ​

        <div
          class="bg-gray-50 rounded-sm pt-1 inline-block align-bottom overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-5xl sm:w-full"
          role="dialog" aria-modal="true" aria-labelledby="modal-headline">
          <div class="">
            <!-- ticket input panel -->
            <div class="">
              <!-- panel buttons -->
              <div class="py-2 divide-y divide-yellow-500">
                <div class="mt-1 px-3 flex w-full justify-between">
                  <div class="flex">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight mt-2">Ticket Detail</h2>
                  </div>
                  <div class="flex">
                    <span
                      class="border-0 px-3 py-3 placeholder-gray-300 text-gray-600 relative bg-white bg-white rounded-l-md text-sm shadow outline-none focus:outline-none">
                      Ticket ID </span>
                    <input type="text" v-model="form.code" name="code" id="code"
                      class="mr-2 border-0 px-3 py-3 placeholder-gray-300 text-gray-600 relative bg-white bg-white rounded-r-md text-sm shadow outline-none focus:outline-none"
                      disabled />
                    <ul class="flex col-span-1">
                      <li class="px-2 lg:px-3">
                        <label class="block text-sm">Critical</label>
                        <input v-model="form.priority" name="priority" value="critical" type="radio"
                          class="form-radio h-5 w-5 text-red-600 bg-red-600" title="(1-4hrs)" />
                      </li>
                      <li class="px-2 lg:px-3">
                        <label class="block text-sm">High</label>
                        <input v-model="form.priority" name="priority" value="high" type="radio"
                          class="form-radio h-5 w-5 text-yellow-600 bg-yellow-600" title="(1-12hrs)" />
                      </li>
                      <li class="px-2 lg:px-3">
                        <label class="block text-sm">Normal</label>
                        <input v-model="form.priority" name="priority" value="normal" type="radio"
                          class="form-radio h-5 w-5 text-yellow-300 bg-yellow-300" title="(1-48hrs)" />
                      </li>

                    </ul>
                  </div>
                </div>
              </div>
              <!-- end of panel buttons -->
              <div class="bg-white rounded-t-lg w-full mx-auto mt-1 shadow-xl divide-y divide-gray-200">
                <!-- Tabs -->
                <div class="inline-flex w-full bg-gray-50 rounded-t-lg">
                  <ul id="tabs" class="flex">
                    <li class="px-2 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                      :class="[tab == 1 ? 'border-b-2 border-indigo-400 -mb-px' : 'opacity-50']"><a href="#"
                        @click="tabClick(1)" preserve-state>Genaral</a></li>
                    <li class="px-2 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                      :class="[tab == 2 ? 'border-b-2 border-indigo-400 -mb-px' : 'opacity-50']"><a href="#"
                        @click="tabClick(2)" preserve-state>Tasks</a></li>
                    <li class="px-2 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                      :class="[tab == 3 ? 'border-b-2 border-indigo-400 -mb-px' : 'opacity-50']"><a href="#"
                        @click="tabClick(3)" preserve-state>Files</a></li>
                    <li class="px-2 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                      :class="[tab == 4 ? 'border-b-2 border-indigo-400 -mb-px' : 'opacity-50']"><a href="#"
                        @click="tabClick(4)" preserve-state>Customer Info</a></li>
                    <li class="px-2 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                      :class="[tab == 5 ? 'border-b-2 border-indigo-400 -mb-px' : 'opacity-50']"><a href="#"
                        @click="tabClick(5)" preserve-state>LOGS</a></li>
                    <li class="px-2 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                      :class="[tab == 6 ? 'border-b-2 border-indigo-400 -mb-px' : 'opacity-50']"><a href="#"
                        @click="tabClick(6)" preserve-state>History</a></li>
                  </ul>
                </div>
                <!-- Tab Contents -->
                <div id="tab-contents">
                  <!-- tab1 -->
                  <div class="p-4" :class="[tab == 1 ? '' : 'hidden']">
                    <!-- <incident-detail @loading="checkLoading" @selected_id="getSelectedID" :parentform="form" :data="selected_id" v-if="!editMode" :key="page_update" ref="detailcheck" v-model="form" />
                    <incident-detail @loading="checkLoading" @selected_id="getSelectedID" :parentform="form" :data="selected_id" v-if="editMode" :key="page_update" ref="detailcheck" v-model="form"  /> -->
                    <div class="grid grid-cols-5 gap-2">
                      <!-- date -->
                      <div class="py-2 col-span-1 sm:col-span-1">
                        <div class="mt-1 flex">
                          <label for="date" class="block text-sm font-medium text-gray-700 mt-2 mr-2">Incident Detail :
                          </label>
                        </div>
                      </div>
                      <div class="py-2 col-span-2 sm:col-span-2">
                        <div class="mt-1 flex rounded-md shadow-sm">
                          <input type="date" v-model="form.date" name="date" id="date"
                            class="form-input focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" />
                        </div>
                        <p v-if="$page.props.errors.date" class="mt-2 text-sm text-red-500">{{ $page.props.errors.date
                          }}</p>
                      </div>
                      <div class="py-2 col-span-2 sm:col-span-2">
                        <div class="mt-1 flex rounded-md shadow-sm">
                          <input type="time" v-model="form.time" name="time"
                            class="form-input focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" />
                        </div>
                        <p v-if="$page.props.errors.time" class="mt-2 text-sm text-red-500">{{ $page.props.errors.time
                          }}</p>
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
                          <label for="first_name" class="block text-sm font-medium text-gray-700 mt-2 mr-2"> User ID :
                          </label>
                        </div>
                      </div>
                      <div class="py-2 col-span-4 sm:col-span-4">
                        <div class="mt-1 flex rounded-md shadow-sm" v-if="customers.length !== 0">
                          <multiselect deselect-label="Selected already" :options="customers" track-by="id"
                            label="ftth_id" v-model="form.customer_id" :allow-empty="false"></multiselect>
                        </div>
                        <p v-if="$page.props.errors.customer" class="mt-2 text-sm text-red-500">{{
                          $page.props.errors.customer }}</p>
                      </div>
                      <!-- end of user id -->
                      <!-- person incharge  -->
                      <div class="py-2 col-span-1 sm:col-span-1">
                        <div class="mt-1 flex">
                          <label for="incharge" class="block text-sm font-medium text-gray-700 mt-2 mr-2"> Person
                            Incharge : </label>
                        </div>
                      </div>
                      <div class="py-2 col-span-4 sm:col-span-4">
                        <div class="mt-1 flex rounded-md shadow-sm" v-if="team.length !== 0">
                          <multiselect deselect-label="Selected already" :options="team" track-by="id" label="name"
                            v-model="form.incharge_id" :allow-empty="false" :disabled="true"></multiselect>
                        </div>
                        <p v-if="$page.props.errors.incharge" class="mt-2 text-sm text-red-500">{{
                          $page.props.errors.incharge }}</p>
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
                            required @change="form.topic = null">
                            <option value="default">Please Choose Ticket Type</option>
                            <option value="service_complaint">Service Complaint</option>
                            <option value="onsite_complaint">Onsite Complaint</option>
                            <option value="technical_complaint">Technical Complaint</option>
                            <option value="plan_change">Plan Change</option>
                            <option value="suspension">Suspension</option>
                            <option value="termination">Termination</option>
                          </select>
                        </div>
                        <p v-if="$page.props.errors.type" class="mt-2 text-sm text-red-500">{{ $page.props.errors.type
                          }}</p>
                      </div>
                      <!-- end of type -->
                      <!-- topic -->
                      <div class="py-2 col-span-1 sm:col-span-1"
                        v-if="form.type == 'service_complaint' || form.type == 'onsite_complaint' | form.type == 'technical_complaint'">
                        <div class="mt-1 flex">
                          <label for="topic" class="block text-sm font-medium text-gray-700 mt-2 mr-2"> Topic : </label>
                        </div>
                      </div>
                      <div class="py-2 col-span-4 sm:col-span-4" v-if="form.type == 'service_complaint'">
                        <div class="mt-1 flex">
                          <select v-model="form.topic"
                            class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300">
                            <option value="los_redlight">LOS Redlight</option>
                            <option value="pon_blinking">PON Blinking</option>
                            <option value="high_loss">High Loss</option>
                            <option value="dn_down">DN Down</option>
                            <option value="sn_down">SN Down</option>
                            <option value="relocation">Relocation</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="other">Other</option>
                          </select>
                        </div>
                        <p v-if="$page.props.errors.topic" class="mt-2 text-sm text-red-500">{{ $page.props.errors.topic
                          }}</p>
                      </div>
                      <div class="py-2 col-span-4 sm:col-span-4" v-if="form.type == 'onsite_complaint'">
                        <div class="mt-1 flex">
                          <select v-model="form.topic"
                            class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300">
                            <option value="no_internet">No Internet</option>
                            <option value="slow_performance">Slow Performance</option>
                            <option value="wifi_issue">Wifi Issue</option>
                            <option value="coverage_issue">Coverage Issue</option>
                            <option value="onu_issue">ONU issue</option>
                            <option value="home_router_issue">Home Router Issue</option>
                            <option value="password_change">Password Change</option>
                            <option value="other">Other</option>
                          </select>
                        </div>
                        <p v-if="$page.props.errors.topic" class="mt-2 text-sm text-red-500">{{ $page.props.errors.topic
                          }}</p>
                      </div>
                      <div class="py-2 col-span-4 sm:col-span-4" v-if="form.type == 'technical_complaint'">
                        <div class="mt-1 flex">
                          <select v-model="form.topic"
                            class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300">
                            <option value="website_delay_issue">Website Delay Issue</option>
                            <option value="gaming_delay_issue">Gaming Delay Issue</option>
                            <option value="application_delay_issue">Application Delay Issue</option>
                            <option value="other">Other</option>
                          </select>
                        </div>
                        <p v-if="$page.props.errors.topic" class="mt-2 text-sm text-red-500">{{ $page.props.errors.topic
                          }}</p>
                      </div>
                      <!-- end of topic -->
                      <!-- status -->
                      <div class="py-2 col-span-1 sm:col-span-1">
                        <div class="mt-1 flex">
                          <label for="first_name" class="block text-sm font-medium text-gray-700 mt-2 mr-2"> Status :
                          </label>
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
                        <p v-if="$page.props.errors.status" class="mt-2 text-sm text-red-500">{{
                          $page.props.errors.status }}</p>
                      </div>
                      <!-- end of status -->
                      <!-- close date time -->

                      <div class="py-2 col-span-1 sm:col-span-1" v-if="form.status == 3">
                        <div class="mt-1 flex">
                          <label for="date" class="block text-sm font-medium text-gray-700 mt-2 mr-2"> Incident Close
                            Period : </label>
                        </div>
                      </div>
                      <div class="py-2 col-span-2 sm:col-span-2" v-if="form.status == 3">
                        <div class="mt-1 flex rounded-md shadow-sm">
                          <input type="date" v-model="form.close_date" name="close_date" id="close_date"
                            class="form-input focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" />
                        </div>
                        <p v-if="$page.props.errors.close_date" class="mt-2 text-sm text-red-500">{{
                          $page.props.errors.close_date }}</p>
                      </div>
                      <div class="py-2 col-span-2 sm:col-span-2" v-if="form.status == 3">
                        <div class="mt-1 flex rounded-md shadow-sm">
                          <input type="time" v-model="form.close_time" name="close_time"
                            class="form-input focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" />
                        </div>
                        <p v-if="$page.props.errors.close_time" class="mt-2 text-sm text-red-500">{{
                          $page.props.errors.close_time }}</p>
                      </div>

                      <!-- close date time -->
                      <!-- resolved date time -->

                      <div class="py-2 col-span-1 sm:col-span-1" v-if="form.status == 5">
                        <div class="mt-1 flex">
                          <label for="date" class="block text-sm font-medium text-gray-700 mt-2 mr-2"> Incident Resolved
                            Period : </label>
                        </div>
                      </div>
                      <div class="py-2 col-span-2 sm:col-span-2" v-if="form.status == 5">
                        <div class="mt-1 flex rounded-md shadow-sm">
                          <input type="date" v-model="form.resolved_date" name="close_date" id="close_date"
                            class="form-input focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" />
                        </div>
                        <p v-if="$page.props.errors.resolved_date" class="mt-2 text-sm text-red-500">{{
                          $page.props.errors.resolved_date }}</p>
                      </div>
                      <div class="py-2 col-span-2 sm:col-span-2" v-if="form.status == 5">
                        <div class="mt-1 flex rounded-md shadow-sm">
                          <input type="time" v-model="form.resolved_time" name="close_time"
                            class="form-input focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" />
                        </div>
                        <p v-if="$page.props.errors.resolved_time" class="mt-2 text-sm text-red-500">{{
                          $page.props.errors.resolved_time }}</p>
                      </div>

                      <!-- resolved date time -->
                      <!-- suspension -->
                      <div class="py-2 col-span-1 sm:col-span-1" v-if="form.type == 'suspension'">
                        <div class="mt-1 flex">
                          <label for="suspense" class="block text-sm font-medium text-gray-700 mt-2 mr-2"> Suspense
                            Period : </label>
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
                            <p v-if="$page.props.errors.start_date" class="mt-2 text-sm text-red-500">{{
                              $page.props.errors.start_date }}</p>
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
                            <p v-if="$page.props.errors.end_date" class="mt-2 text-sm text-red-500">{{
                              $page.props.errors.end_date }}</p>
                          </div>
                        </div>
                      </div>
                      <!-- end of suspension -->
                      <!-- resume -->
                      <div class="py-2 col-span-1 sm:col-span-1" v-if="form.type == 'resume'">
                        <div class="mt-1 flex">
                          <label for="resume" class="block text-sm font-medium text-gray-700 mt-2 mr-2"> Resume Date :
                          </label>
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
                        <p v-if="$page.props.errors.resume" class="mt-2 text-sm text-red-500">{{
                          $page.props.errors.start_date }}</p>
                      </div>
                      <!-- end of resume -->
                      <!-- termination -->
                      <div class="py-2 col-span-1 sm:col-span-1" v-if="form.type == 'termination'">
                        <div class="mt-1 flex">
                          <label for="termination" class="block text-sm font-medium text-gray-700 mt-2 mr-2">
                            Termination Date : </label>
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
                        <p v-if="$page.props.errors.start_date" class="mt-2 text-sm text-red-500">{{
                          $page.props.errors.start_date }}</p>
                      </div>
                      <!-- end of termination -->
                      <!-- relocation address -->
                      <div class="py-2 col-span-1 sm:col-span-1" v-if="form.type == 'relocation'">
                        <div class="mt-1 flex">
                          <label for="new_address" class="block text-sm font-medium text-gray-700 mt-2 mr-2"> New
                            Address : </label>
                        </div>
                      </div>
                      <div class="py-2 col-span-4 sm:col-span-4" v-if="form.type == 'relocation'">
                        <div class="mt-1 flex">
                          <textarea v-model="form.new_address" name="new_address" id="new_address"
                            class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"> </textarea>
                        </div>
                        <p v-if="$page.props.errors.new_address" class="mt-2 text-sm text-red-500">{{
                          $page.props.errors.new_address }}</p>
                      </div>
                      <!-- end of relocation address -->
                      <!-- relocation township -->
                      <div class="py-2 col-span-1 sm:col-span-1" v-if="form.type == 'relocation'">
                        <div class="mt-1 flex">
                          <label for="new_address" class="block text-sm font-medium text-gray-700 mt-2 mr-2"> New
                            Township : </label>
                        </div>
                      </div>
                      <div class="py-2 col-span-4 sm:col-span-4" v-if="form.type == 'relocation'">
                        <div class="mt-1 flex rounded-md shadow-sm" v-if="townships.length !== 0">
                          <multiselect deselect-label="Selected already" :options="townships" track-by="id" label="name"
                            v-model="form.new_township" :allow-empty="false"></multiselect>
                        </div>
                        <p v-if="$page.props.errors.new_township" class="mt-2 text-sm text-red-500">{{
                          $page.props.errors.new_township }}</p>
                      </div>
                      <!-- end of relocation township -->
                      <!-- relocation latlong -->
                      <div class="py-2 col-span-1 sm:col-span-1" v-if="form.type == 'relocation'">
                        <div class="mt-1 flex">
                          <label for="new_latlong" class="block text-sm font-medium text-gray-700 mt-2 mr-2"> New
                            Location : </label>
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
                            <p v-if="$page.props.errors.latitude" class="mt-2 text-sm text-red-500">{{
                              $page.props.errors.latitude }}</p>
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
                            <p v-if="$page.props.errors.longitude" class="mt-2 text-sm text-red-500">{{
                              $page.props.errors.longitude }}</p>
                          </div>
                        </div>
                      </div>
                      <!-- end of relocation latlong -->
                      <!-- relocation start date -->
                      <div class="py-2 col-span-1 sm:col-span-1" v-if="form.type == 'relocation'">
                        <div class="mt-1 flex">
                          <label for="new_address" class="block text-sm font-medium text-gray-700 mt-2 mr-2"> Effective
                            Date : </label>
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
                        <p v-if="$page.props.errors.start_date" class="mt-2 text-sm text-red-500">{{
                          $page.props.errors.start_date }}</p>
                      </div>
                      <!-- relocation end date -->
                      <!-- plan change -->
                      <div class="py-2 col-span-1 sm:col-span-1" v-if="form.type == 'plan_change'">
                        <div class="mt-1 flex">
                          <label for="new_package" class="block text-sm font-medium text-gray-700 mt-2 mr-2"> New
                            Package: </label>
                        </div>
                      </div>
                      <div class="py-2 col-span-4 sm:col-span-4" v-if="form.type == 'plan_change'">
                        <div class="mt-1 flex rounded-md shadow-sm" v-if="packages.length !== 0">
                          <multiselect deselect-label="Selected already" :options="packages" track-by="id"
                            label="item_data" v-model="form.package_id" :allow-empty="false"></multiselect>
                        </div>
                        <p v-if="$page.props.errors.package" class="mt-2 text-sm text-red-500">{{
                          $page.props.errors.package }}</p>
                      </div>
                      <!-- end of plan change -->
                      <!-- relocation start date -->
                      <div class="py-2 col-span-1 sm:col-span-1" v-if="form.type == 'plan_change'">
                        <div class="mt-1 flex">
                          <label for="new_address" class="block text-sm font-medium text-gray-700 mt-2 mr-2"> Effective
                            Date : </label>
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
                        <p v-if="$page.props.errors.start_date" class="mt-2 text-sm text-red-500">{{
                          $page.props.errors.start_date }}</p>
                      </div>
                      <!-- relocation end date -->
                      <!-- detail -->
                      <div class="py-2 col-span-1 sm:col-span-1">
                        <div class="mt-1 flex">
                          <label for="detail" class="block text-sm font-medium text-gray-700 mt-2 mr-2"> Detail :
                          </label>
                        </div>
                      </div>
                      <div class="py-2 col-span-4 sm:col-span-4">
                        <div class="mt-1 flex">
                          <textarea v-model="form.description" name="detail" id="detail"
                            class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"> </textarea>
                        </div>
                        <p v-if="$page.props.errors.description" class="mt-2 text-sm text-red-500">{{
                          $page.props.errors.description }}</p>
                      </div>
                      <!-- end of detail -->
                    </div>
                  </div>
                  <!-- end of tab1 -->
                  <div class="p-4" :class="[tab == 2 ? '' : 'hidden']">
                    <keep-alive>
                      <task :data="selected_id" :key="page_update" v-if="tab == 2" />
                    </keep-alive>
                  </div>
                  <div class="p-4" :class="[tab == 3 ? '' : 'hidden']">
                    <keep-alive>
                      <file :data="selected_id" :key="page_update" v-if="tab == 3" />
                    </keep-alive>
                  </div>
                  <div class="p-4" :class="[tab == 4 ? '' : 'hidden']">
                    <keep-alive><customer-detail :data="selected_id" :key="page_update" v-if="tab == 4" /></keep-alive>
                  </div>

                  <div class="p-4" :class="[tab == 5 ? '' : 'hidden']">
                    <keep-alive>
                      <log :data="selected_id" :key="page_update" v-if="tab == 5" />
                    </keep-alive>
                  </div>
                  <div class="p-4" :class="[tab == 6 ? '' : 'hidden']">
                    <keep-alive>
                      <history :data="selected_id" :key="page_update" v-if="tab == 6" />
                    </keep-alive>
                  </div>

                </div>
              </div>
            </div>
            <!-- end of ticket input panel -->
          </div>
          <div class="bg-indigo-50 px-3 py-3 sm:px-6 sm:flex sm:flex-row"
            :class="[tab == 1 ? 'justify-between' : 'justify-end']">
            <div class="flex" v-if="tab == 1 && permission[0].write_incident == 1">
              <button
                class="inline-flex items-center px-4 py-3 bg-indigo-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-400 active:bg-indigo-600 focus:outline-none focus:border-indigo-900 disabled:opacity-25 transition mr-1"
                @click="submit()"><span v-if="editMode">Update</span><span v-if="editMode == false">Save</span><i
                  class="fas fa-save opacity-75 lg:ml-1 text-sm"></i></button>
              <button @click="deleteIncident"
                class="inline-flex items-center px-4 py-3 bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-400 active:bg-red-600 focus:outline-none focus:border-gray-500 disabled:opacity-25 transition mr-1"
                v-show="selected_id">Delete<i class="lg:ml-1 fas fa-trash opacity-75 text-sm"></i></button>
            </div>
            <div class="flex">
              <button @click="closeModal" type="button"
                class="inline-flex items-center px-4 py-3 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-600 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-500 disabled:opacity-25 transition">Close
                <i class="lg:ml-1 fas fa-times-circle opacity-75 text-sm"></i></button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div v-if="loading" wire:loading
      class="fixed top-0 left-0 right-0 bottom-0 w-full h-screen z-50 overflow-hidden bg-gray-700 opacity-75 flex flex-col items-center justify-center">
      <div class="loader ease-linear rounded-full border-4 border-t-4 border-gray-200 h-12 w-12 mb-4"></div>
      <h2 class="text-center text-white text-xl font-semibold">Loading...</h2>
      <p class="w-1/3 text-center text-white">This may take a few seconds, please don't close this page.</p>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/IncidentLayout";
import IncidentAlert from "@/Components/IncidentAlert";

import IncidentDetail from "@/Components/IncidentDetail";
import Task from "@/Components/Task";
import Log from "@/Components/Log";
import History from "@/Components/History";
import File from "@/Components/File";
import CustomerDetail from "@/Components/CustomerDetail";
import NoData from "@/Components/NoData";
import Pagination from "@/Components/Pagination";
import { reactive, ref, onMounted, onUpdated, provide } from "vue";
import Multiselect from "@suadelabs/vue3-multiselect";
import { router } from '@inertiajs/vue3';
import { useForm } from "@inertiajs/vue3";
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
export default {
  name: "Incident",
  components: {
    AppLayout,
    Pagination,
    IncidentAlert,
    IncidentDetail,
    Multiselect,
    VueDatePicker,
    Task,
    File,
    Log,
    History,
    CustomerDetail,
    NoData,
  },
  props: {
    incidents: Object,
    incidents_2: Object,
    noc: Object,
    team: Object,
    townships: Object,
    customers: Object,
    packages: Object,
    critical: Object,
    high: Object,
    normal: Object,
    errors: Object,
    permission: Object,
    user: Object,
  },
  setup(props) {
    const search = ref("");
    const sort = ref("");
    const page_update = ref(1);
    let show = ref(false);
    let editMode = ref(false);
    let selected_id = ref("");
    let incidentStatus = ref(0);
    let incidentType = ref("default");
    let incidentBy = ref("default");
    let incidentDate = ref([]);
    const formatter = ref({
      date: "YYYY-MM-DD",
      month: "MMM",
    });
    let isOpen = ref(false);
    let loading = ref(false);
    provide("noc", props.noc);
    provide("team", props.team);
    provide("townships", props.townships);
    provide("user", props.user);
    provide("packages", props.packages);
    provide("permission", props.permission);
    provide("customers", props.customers);

    const form = useForm({
      id: null,
      code: null,
      priority: null,
      customer_id: null,
      incharge_id: props.team.filter((d) => d.id == props.user.id)[0],
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

    let tab = ref(true);
    let selection = ref("");
    function typeChange(type) {
      selection.value = type;

    }
    function tabClick(val) {
      if (selected_id.value != null) {
        tab.value = val;
        if (val == 1) {
          let data = props.incidents.data.filter((d) => d.id == selected_id.value)[0];
          edit(data);
        }
      }
    }


    function openModal() {
      var today = new Date();
      var date = today.getFullYear() + '-' + (today.getMonth() + 1) + '-' + today.getDate();
      var time = today.getHours() + ":" + today.getMinutes();
      if (!form.close_date && incidentStatus.value != 3)
        form.close_date = date;
      if (!form.close_time && incidentStatus.value != 3)
        form.close_time = time;
      tab.value = 1;
      isOpen.value = true;
    }
    function closeModal() {
      isOpen.value = false;
      selected_id.value = null;
    }
    function newTicket() {
      clearform();
      openModal();
    }

    function alert_edit(data) {
      let edit_data = props.incidents_2.filter((d) => d.id == data.id)[0];
      edit(edit_data);
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
      form.customer_id = props.customers.filter((d) => d.id == data.customer_id)[0];
      form.incharge_id = props.noc.filter((d) => d.id == data.incharge_id)[0];
      form.type = data.type;
      form.topic = data.topic;
      form.status = data.status;
      form.start_date = data.start_date;
      form.end_date = data.end_date;
      form.new_address = data.new_address;
      form.new_township = props.townships.filter((d) => d.id == data.new_township)[0];
      form.package_id = props.packages.filter((d) => d.id == data.package_id)[0];
      form.description = data.description;
      form.date = data.date;
      form.time = data.time;
      form.close_date = data.close_date;
      form.close_time = data.close_time;
      form.resolved_date = data.resolved_date;
      form.resolved_time = data.resolved_time;

      openModal();
    }
    function clearform() {
      selected_id.value = null;
      editMode.value = false;
      form.id = "";
      form.code = "";
      form.priority = "normal";
      form.customer_id = "";
      form.incharge_id = props.team.filter((d) => d.id == props.user.id)[0];
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
    //  function getStatus(data) {
    //   let status = "WIP";
    //   if (data == 1) {
    //     status = "WIP";
    //   } else if (data == 2) {
    //     status = "Escalated";
    //   } else if (data == 3){
    //     status = "Closed";
    //   } else if ( data == 4){
    //     status = "Deleted";
    //   }
    //   return status;
    // }
    function deleteIncident(data) {
      if (!confirm("Are you sure want to remove?")) return;
      form._method = "PUT";
      form.status = 4;
      router.post("/incident/" + form.id, form, {
        onSuccess: (page) => {
          page_update.value += 1;
          Toast.fire({
            icon: "success",
            title: page.props.flash.message,
          });
          closeModal();
        },
        onError: (errors) => {
          console.log("error ..".errors);
        },
      });
    }
    const searchIncident = () => {
      let url = "/incident/";
      if (incidentStatus.value != null) {
        url = url + "?status=" + incidentStatus.value;
      }
      router.get(url, { keyword: search.value }, { preserveState: true });
    };
    const changeStatus = () => {
      let url = "/incident/";
      if (search.value != null) {
        url = url + "?keyword=" + search.value;
      }
      if (incidentType.value != "default") {
        url = url + "&type=" + incidentType.value;
      }

      if (incidentBy.value != "default") {
        url = url + "&member=" + incidentBy.value;
      }
      if (incidentDate.value != "") {
        url = url + "&date=" + incidentDate.value;
      }

      router.get(url, { status: incidentStatus.value }, { preserveState: true });
    };
    const showPriority = (data) => {
      let url = "/incident";
      if (data != 'all') {
        router.get(url, { priority: data }, { preserveState: true });
      } else {
        router.get(url, {}, { preserveState: true });
      }

    }
    const sortBy = (d) => {
      let url = "/incident/?";

      sort.value = d;
      sort.order = sort.order !== "asc" ? "asc" : "desc";
      let suburl = getURL();
      router.get(url + suburl, { sort: sort.value, order: sort.order }, { preserveState: true });
    };
    const getURL = () => {
      let suburl = "";
      if (search.value != null) {
        suburl = "&keyword=" + search.value;
      }
      if (incidentType.value != "default") {
        suburl += "&type=" + incidentType.value;
      }

      if (incidentBy.value != "default") {
        suburl += "&member=" + incidentBy.value;
      }
      if (incidentStatus.value != null) {
        suburl += "&status=" + incidentStatus.value;
      }
      if (incidentDate.value != "") {
        suburl += "&date=" + incidentDate.value;
      }
      return suburl;
    };
    function priorityColor() {
      props.incidents.data.map(function (x) {
        if (x.priority == "high") {
          x.color = x.status != 3 && x.status != 4 ? "yellow-600" : "gray-200";
        } else if (x.priority == "critical") {
          x.color = x.status != 3 && x.status != 4 ? "red-600" : "gray-200";
        } else {
          x.color = x.status != 3 && x.status != 4 ? "yellow-300" : "gray-200";
        }
      });
    }
    function submit() {
      if (editMode.value != true) {
        form._method = "POST";
        router.post("/incident", form, {
          preserveState: true,
          onSuccess: (page) => {
            selected_id.value = page.props.response.id;
            let response = props.incidents.data.filter((d) => d.id == selected_id.value)[0];
            editMode.value = true;
            loading.value = false;
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
            loading.value = false;
            console.log("error ..");
          },
          onStart: (pending) => {
            loading.value = true;
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
            loading.value = false;
          },
          onError: (errors) => {
            loading.value = false;
            console.log("error .." + errors);
          },
          onStart: (pending) => {
            loading.value = true;
          },
        });
      }
    }
    onMounted(() => {
      console.log("Incident - On Mounted");
      props.packages.map(function (x) {
        return (x.item_data = `${x.name}`);
      });

      priorityColor();
    });

    onUpdated(() => {
      console.log("Incident - On Updated");
      props.packages.map(function (x) {
        return (x.item_data = `${x.name}`);
      });

      priorityColor();
    });
    return { loading, form, openModal, closeModal, newTicket, isOpen, deleteIncident, searchIncident, edit, sortBy, getStatus, changeStatus, sort, search, show, tabClick, tab, selection, selected_id, editMode, typeChange, showPriority, incidentStatus, page_update, alert_edit, submit, clearform, incidentType, incidentBy, incidentDate, formatter };
  },
};
</script>