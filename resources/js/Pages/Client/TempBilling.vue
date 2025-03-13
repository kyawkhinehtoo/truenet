<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">Temporary Billing View</h2>
    </template>

    <div class="py-6">
      <div class="max-w-full mx-auto sm:px-6 lg:px-8">
        <!--  Search -->
        <div class="flex justify-between space-x-2 items-end mb-2 px-1 md:px-0">
          <div class="relative flex flex-wrap">
            <span
              class="z-10 h-full leading-snug font-normal  text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-3"><i
                class="fas fa-search"></i></span>
            <input type="text" placeholder="Search here..."
              class="border-0 px-3 py-3 placeholder-gray-300 text-gray-600 relative bg-white bg-white rounded text-sm shadow outline-none focus:outline-none focus:ring w-full pl-10 py-2.5"
              id="search" v-model="search" v-on:keyup.enter="searchTsp" />
          </div>

        </div>
      </div>
      <div class="max-w-full mx-auto sm:px-6 lg:px-8 mt-4 flex justify-between">
        <div class="flex">
          <a href="#" class="w-full text-right font-semibold text-xs underline mr-2" v-on:click="toggleAdv">Advance
            Search</a>
          <i class="fas fa-chevron-right text-gray-400" v-show="!show_search"></i>
          <i class="fas fa-chevron-down text-gray-400" v-show="show_search"></i>
        </div>
        <div class="flex">
          <a @click="doExcel"
            class="cursor-pointer inline-flex items-center px-4 py-2 bg-green-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition">Export
            Excel <i class="ml-1 fa fa-download text-white" tabindex="12"></i></a>
        </div>
      </div>
      <div v-show="show_search" class="max-w-full mx-auto sm:px-6 lg:px-8 mt-4">
        <billing-search @search_parameter="goSearch" />
      </div>
      <div class="max-w-full mx-auto sm:px-6 lg:px-8">
        <div class="mt-4 p-3 inline-flex bg-white rounded-md mb-2 shadow-sm flex justify-between w-full">
          <button type="button" @click="clearData"
            class="h-10 text-md px-4 bg-yellow-600 rounded text-white hover:bg-yellow-700">Clear All Data</button>
          <button type="button" @click="goFinal"
            class="h-10 text-md px-4 bg-indigo-600 rounded text-white hover:bg-indigo-700">Save as Final</button>
        </div>
        <div class="bg-white overflow-auto shadow-xl sm:rounded-lg" v-if="billings.data">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col"
                  class="pl-3 px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  No.</th>
                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Bill
                  No.</th>
                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Customer ID</th>
                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Package</th>
                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Speed
                </th>
                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Usage
                </th>
                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Total
                  Payable</th>
                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Preview</th>

                <th scope="col" class="relative px-2 py-3"><span class="sr-only">Action</span></th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="(row, index) in billings.data" v-bind:key="row.id">
                <td class="pl-4 px-2 py-3 text-xs whitespace-nowrap">{{ (index += billings.from) }}</td>
                <td class="px-2 py-3 text-xs whitespace-nowrap">{{ row.bill_number }}</td>
                <td class="px-2 py-3 text-xs whitespace-nowrap">{{ row.ftth_id }}</td>
                <td class="px-2 py-3 text-xs whitespace-nowrap">{{ row.service_description }}</td>
                <td class="px-2 py-3 text-xs whitespace-nowrap">{{ row.qty }}</td>
                <td class="px-2 py-3 text-xs whitespace-nowrap">{{ row.usage_month ? row.usage_month + " Month" : "" }}
                  {{ row.usage_day ?
                    row.usage_day + " Day" : "" }}
                </td>
                <td class="px-2 py-3 text-xs whitespace-nowrap">{{ row.total_payable }}</td>
                <td class="px-2 py-3 text-xs whitespace-nowrap">
                  <a :href="`/pdfpreview1/${row.id}`" target="_blank"><i class="fa fas fa-eye text-gray-400"></i></a>
                </td>
                <td class="px-2 py-3 text-xs whitespace-nowrap text-right font-medium">
                  <a href="#" @click="edit(row)" class="text-yellow-600 hover:text-yellow-900"><i
                      class="fas fa-pen"></i></a> |
                  <a href="#" @click="deleteRow(row)" class="text-red-600 hover:text-red-900"><i
                      class="fas fa-trash"></i></a>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <span v-if="billings.total" class="w-full block mt-4">
          <label class="text-xs text-gray-600">{{ billings.data.length }} Invoices in Current Page. Total Number of
            Invoices
            : {{ billings.total }}</label>
        </span>
        <span v-if="billings.links">
          <pagination class="mt-6" :links="billings.links" />
        </span>
        <div v-if="loading" wire:loading
          class="fixed top-0 left-0 right-0 bottom-0 w-full h-screen z-50 overflow-hidden bg-gray-700 opacity-75 flex flex-col items-center justify-center">
          <div class="loader ease-linear rounded-full border-4 border-t-4 border-gray-200 h-12 w-12 mb-4"></div>
          <h2 class="text-center text-white text-xl font-semibold">Loading...</h2>
          <p class="w-1/3 text-center text-white">This may take a few seconds, please don't close this page.</p>
        </div>

        <div ref="isOpen" class="fixed z-10 inset-0 overflow-y-auto ease-out duration-400" v-if="isOpen">
          <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity">
              <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <!-- This element is to trick the browser into centering the modal contents. -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>​
            <div
              class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-7xl sm:w-full"
              role="dialog" aria-modal="true" aria-labelledby="modal-headline">
              <form @submit.prevent="submit">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 shadow sm:rounded-lg">
                  <h6 class="md:min-w-full text-indigo-700 text-sm uppercase font-bold block pt-1 no-underline">Billing
                    Detail Information</h6>
                  <div class="hidden sm:block" aria-hidden="true">
                    <div class="py-5">
                      <div class="border-t border-gray-200"></div>
                    </div>
                  </div>
                  <div class="md:grid md:grid-cols-3 md:gap-6">


                    <div class="mb-4 md:col-span-1">
                      <label for="ftth_id" class="block text-gray-700 text-sm font-bold mb-2">Customer ID :</label>
                      <input type="text"
                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                        id="ftth_id" placeholder="Enter Customer ID" v-model="form.ftth_id" disabled />
                      <label for="bill_to" class="mt-4 block text-gray-700 text-sm font-bold mb-2">Customer Name
                        :</label>
                      <input type="text"
                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                        id="bill_to" placeholder="Enter Bill To" v-model="form.bill_to" />


                      <label for="attn" class="mt-4 block text-gray-700 text-sm font-bold mb-2">Customer Address
                        :</label>
                      <textarea
                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                        id="attn" placeholder="Enter Attention" v-model="form.attn"></textarea>
                      <div v-if="$page.props.errors.attn" class="text-red-500">{{ $page.props.errors.attn[0] }}</div>

                    </div>


                    <div class="mb-4 md:col-span-1">
                      <label for="bill_number" class="block text-gray-700 text-sm font-bold mb-2">Bill Number :</label>
                      <input type="text"
                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                        id="bill_number" placeholder="Enter Bill Number" v-model="form.bill_number" />

                      <label for="date_issued" class="mt-4 block text-gray-700 text-sm font-bold mb-2">Bill Issue Date
                        :</label>
                      <input type="date"
                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                        id="date_issued" placeholder="Enter Issue Date" v-model="form.date_issued" />


                      <label for="payment_duedate" class="mt-4 block text-gray-700 text-sm font-bold mb-2">Bill Due
                        Date
                        :</label>
                      <input type="date"
                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                        id="payment_duedate" placeholder="Enter Payment Due Date" v-model="form.payment_duedate" />
                      <div v-if="$page.props.errors.payment_duedate" class="text-red-500">{{
                        $page.props.errors.payment_duedate[0] }}</div>

                    </div>
                    <div class="mb-4 md:col-span-1">
                      <label for="start_date" class="block text-gray-700 text-sm font-bold mb-2">Bill Start Date
                        :</label>
                      <input type="date"
                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                        id="start_date" placeholder="Enter Bill Start Date" v-model="form.start_date" />

                      <label for="end_date" class="mt-4 block text-gray-700 text-sm font-bold mb-2">Bill End Date
                        :</label>
                      <input type="date"
                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                        id="end_date" placeholder="Enter Bill End Date" v-model="form.end_date" />
                      <label for="bonus_day" class="mt-4 block text-gray-700 text-sm font-bold mb-2">Bonus Day/Month
                        (If
                        Any)
                        :</label>
                      <div class="flex justify-between ">

                        <input type="number"
                          class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300"
                          id="bonus_day" v-model="form.bonus_day" />
                        <span
                          class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                          Days </span>
                        <input type="number"
                          class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300"
                          id="bonus_month" v-model="form.bonus_month" />
                        <span
                          class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                          Months </span>
                      </div>
                    </div>

                  </div>
                  <div class="hidden sm:block" aria-hidden="true">
                    <div class="py-5">
                      <div class="border-t border-gray-200"></div>
                    </div>
                  </div>
                  <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="mb-4 md:col-span-1">
                      <label for="service_description" class="block text-gray-700 text-sm font-bold mb-2">Service
                        Description :</label>
                      <input type="text"
                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                        id="service_description" placeholder="Enter Service Description"
                        v-model="form.service_description" />
                      <div v-if="$page.props.errors.service_description" class="text-red-500">{{
                        $page.props.errors.service_description[0] }}</div>

                      <label for="qty" class="mt-4 block text-gray-700 text-sm font-bold mb-2">QTY :</label>
                      <input type="text"
                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                        id="qty" placeholder="Enter QTY" v-model="form.qty" />
                      <div v-if="$page.props.errors.qty" class="text-red-500">{{ $page.props.errors.qty[0] }}</div>

                      <label for="normal_cost" class="mt-4 block text-gray-700 text-sm font-bold mb-2">Original Package
                        Price :</label>
                      <input type="text"
                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                        id="normal_cost" v-model="form.normal_cost" disabled />
                      <div v-if="$page.props.errors.normal_cost" class="text-red-500">{{
                        $page.props.errors.normal_cost[0]
                      }}</div>

                      <label for="type" class="mt-4 block text-gray-700 text-sm font-bold mb-2">Type :</label>
                      <input type="text"
                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                        id="type" placeholder="Enter Type" v-model="form.type" />
                      <div v-if="$page.props.errors.type" class="text-red-500">{{ $page.props.errors.type[0] }}</div>

                      <label for="usage_day" class="mt-4 block text-gray-700 text-sm font-bold mb-2">Actual Usage
                        (Days/Month) :</label>
                      <div class="flex justify-between ">

                        <input type="number"
                          class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300"
                          id="usage_day" v-model="form.usage_day" @change="updateUsage" />
                        <span
                          class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                          Days </span>
                        <input type="number"
                          class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300"
                          id="usage_month" v-model="form.usage_month" @change="updateUsage" />
                        <span
                          class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                          Months </span>
                      </div>

                      <label for="email" class="mt-4 block text-gray-700 text-sm font-bold mb-2">Email :</label>
                      <input type="text"
                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                        id="email" placeholder="Enter Email" v-model="form.email" />
                      <div v-if="$page.props.errors.email" class="text-red-500">{{ $page.props.errors.email[0] }}</div>

                      <label for="phone" class="mt-4 block text-gray-700 text-sm font-bold mb-2">Phone :</label>
                      <input type="text"
                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                        id="phone" placeholder="Enter Phone Number" v-model="form.phone" />
                      <div v-if="$page.props.errors.phone" class="text-red-500">{{ $page.props.errors.phone[0] }}</div>
                    </div>

                    <div class="mb-4 md:col-span-2">
                      <label for="current_charge" class="block text-gray-700 text-sm font-bold mb-2">Current Charge
                        :</label>
                      <input type="number"
                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                        id="current_charge" placeholder="Enter Current Charge" v-model="form.current_charge"
                        @change="calc" />
                      <div v-if="$page.props.errors.current_charge" class="text-red-500">{{
                        $page.props.errors.current_charge[0] }}</div>



                      <label for="otc" class="mt-4 block text-gray-700 text-sm font-bold mb-2">OTC :</label>
                      <input type="number"
                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                        id="otc" placeholder="Enter OTC" v-model="form.otc" @change="calc" />
                      <div v-if="$page.props.errors.otc" class="text-red-500">{{ $page.props.errors.otc }}</div>
                      <label for="public_ip" class="mt-4 block text-gray-700 text-sm font-bold mb-2">Public IP :</label>
                      <input type="number"
                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                        id="otc" placeholder="Enter Public IP Charges" v-model="form.public_ip" @change="calc" />
                      <div v-if="$page.props.errors.otc" class="text-red-500">{{ $page.props.errors.public_ip }}</div>

                      <label for="compensation" class="mt-4 block text-gray-700 text-sm font-bold mb-2">Compensation
                        :</label>
                      <input type="number"
                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                        id="compensation" placeholder="Enter Compensation" v-model="form.compensation" @change="calc" />
                      <div v-if="$page.props.errors.compensation" class="text-red-500">{{
                        $page.props.errors.compensation }}
                      </div>
                      <label for="sub_total" class="mt-4 block text-gray-700 text-sm font-bold mb-2">Sub Total :</label>
                      <input type="number"
                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                        id="total_mmk" placeholder="Enter Sub Total" v-model="form.sub_total" @change="calc" />
                      <div v-if="$page.props.errors.sub_total" class="text-red-500">{{ $page.props.errors.sub_total[0]
                        }}
                      </div>


                      <label for="tax" class="mt-4 block text-gray-700 text-sm font-bold mb-2">Commercial Tax (5%) :
                        <button type="button"
                          class="inline-flex justify-center rounded-md border border-gray-300 px-4 py-2 bg-blue-500 text-base leading-6 font-medium text-white shadow-sm hover:text-white focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5"
                          @click="calTax">Calculate </button> </label>
                      <input type="number"
                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                        id="tax" v-model="form.tax" @change="calc" />
                      <div v-if="$page.props.errors.tax" class="text-red-500">{{ $page.props.errors.tax }}</div>
                      <label for="discount" class="mt-4 block text-gray-700 text-sm font-bold mb-2">Discount :</label>
                      <input type="number"
                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                        id="discount" placeholder="Enter Discount" v-model="form.discount" @change="calc" />
                      <div v-if="$page.props.errors.discount" class="text-red-500">{{ $page.props.errors.discount }}
                      </div>
                      <label for="total_payable" class="mt-4 block text-gray-700 text-sm font-bold mb-2">Total Payable
                        :</label>
                      <input type="number"
                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                        id="total_payable" placeholder="Enter Total Payable" v-model="form.total_payable" />
                      <div v-if="$page.props.errors.total_payable" class="text-red-500">{{
                        $page.props.errors.total_payable
                      }}
                      </div>
                    </div>
                  </div>
                </div>

                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                  <!-- <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                    <button wire:click.prevent="submit" type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition" v-show="!editMode">Save</button>
                  </span> -->
                  <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                    <button wire:click.prevent="submit" type="submit"
                      class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">Update</button>
                  </span>
                  <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
                    <button @click="closeModal" type="button"
                      class="inline-flex justify-center w-full rounded-md border border-gray-300 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5">Close</button>
                  </span>
                </div>
              </form>
            </div>
          </div>
        </div>
        <div ref="openBillName" class="fixed z-10 inset-0 overflow-y-auto ease-out duration-400" v-if="openBillName">
          <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity">
              <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <!-- This element is to trick the browser into centering the modal contents. -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>​
            <div
              class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
              role="dialog" aria-modal="true" aria-labelledby="modal-headline">
              <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="">
                  <label for="additonal" class="text-sm text-gray-700">Additional to Existing Bill ?</label>
                  <input type="checkbox"
                    class="ml-2 focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded" name="additonal"
                    v-model="form_1.additonal" value="true" />
                  <div class="my-4" v-if="form_1.additonal">
                    <label for="bill" class="block text-gray-700 text-sm font-bold mb-2">Please Select Bill
                    </label>
                    <div class="flex rounded-md shadow-sm">
                      <multiselect deselect-label="Selected already" :options="bill" track-by="id" label="name"
                        v-model="form_1.bill_id" :allow-empty="true"></multiselect>
                    </div>
                    <p v-if="$page.props.errors.bill_id" class="mt-2 text-sm text-red-500">{{ $page.props.errors.bill_id
                      }}
                    </p>
                  </div>
                  <div class="my-4" v-else>
                    <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Enter Bill Name to
                      Create:</label>
                    <input type="text"
                      class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                      id="name" placeholder="Enter Bill Name" v-model="form_1.bill_name" />
                    <div v-if="$page.props.errors.bill_name" class="text-red-500">{{ $page.props.errors.bill_name[0] }}
                    </div>
                  </div>
                </div>
              </div>
              <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                  <button @click="saveFinal" type="button"
                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">GO
                    !</button>
                </span>
                <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
                  <button @click="closeFinal" type="button"
                    class="inline-flex justify-center w-full rounded-md border border-gray-300 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5">Cancel</button>
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import Pagination from "@/Components/Pagination";
import { useForm } from "@inertiajs/vue3";
import { onMounted, onUpdated, provide, ref } from "vue";
import { router } from '@inertiajs/vue3';
import BillingSearch from "@/Components/BillingSearch";
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import Multiselect from "@suadelabs/vue3-multiselect";
export default {
  components: {
    AppLayout,
    Pagination,
    BillingSearch,
    VueDatePicker,
    Multiselect,
  },
  name: "TempBilling",
  props: {
    billings: Object,
    bill: Object,
    packages: Object,
    townships: Object,
    status: Object,
    errors: Object,
  },
  setup(props) {
    const formatter = ref({
      date: "YYYY-MM-DD",
      month: "MMM",
    });
    const chartRef = ref(null);
    let proj_name = ref([]);
    let showExcel = ref(false);
    let loading = ref(false);
    let isOpen = ref(false);
    let editMode = ref(false);
    let openBillName = ref(false);
    let parameter = ref("");
    provide("packages", props.packages);
    provide("townships", props.townships);
    provide("status", props.status);
    const search = ref("");
    const sort = ref("");
    let show_search = ref(false);
    const searchTsp = () => {
      router.get("/tempBilling", { keyword: search.value }, { preserveState: true });
    };
    const goSearch = (parm) => {
      parameter.value = parm;
      let url = "/tempBilling";
      router.get(url, parm, { preserveState: true });
    };
    const toggleAdv = () => {
      show_search.value = !show_search.value;
    };
    const form_1 = useForm({
      bill_name: null,
      additonal: false,
      bill_id: null,
    });

    const form = useForm({
      id: null,
      customer_id: null,
      start_date: null,
      end_date: null,
      period_covered: null,
      bill_number: null,
      ftth_id: null,
      date_issued: null,
      bill_to: null,
      attn: null,
      previous_balance: null,
      current_charge: null,
      compensation: null,
      otc: null,
      sub_total: null,
      payment_duedate: null,
      service_description: null,
      qty: null,
      usage_day: null,
      usage_month: null,
      bonus_day: null,
      bonus_month: null,
      normal_cost: null,
      type: null,
      total_payable: null,
      discount: null,
      email: null,
      phone: null,
      tax: 0,
      public_ip: 0,
    });
    function edit(data) {
      form.id = data.id;
      form.customer_id = data.customer_id;
      form.start_date = data.start_date;
      form.end_date = data.end_date;
      form.bill_number = data.bill_number;
      form.ftth_id = data.ftth_id;
      form.date_issued = data.date_issued;
      form.bill_to = data.bill_to;
      form.attn = data.attn;
      form.previous_balance = data.previous_balance;
      form.current_charge = data.current_charge;
      form.compensation = data.compensation ?? 0;
      form.otc = data.otc;
      form.sub_total = data.sub_total;
      form.payment_duedate = data.payment_duedate;
      form.service_description = data.service_description;
      form.qty = data.qty;
      form.usage_day = data.usage_day;
      form.usage_month = data.usage_month;
      form.bonus_day = data.bonus_day;
      form.bonus_month = data.bonus_month;
      form.customer_status = data.customer_status;
      form.normal_cost = data.normal_cost;
      form.type = data.type;
      form.total_payable = data.total_payable;
      form.discount = data.discount ?? 0;
      form.email = data.email;
      form.phone = data.phone;
      form.tax = data.tax ?? 0;
      form.public_ip = data.public_ip ?? 0;
      editMode.value = true;
      openModal();
    }
    function saveFinal() {
      form_1.post("/saveFinal", {
        onSuccess: (page) => {
          Toast.fire({
            icon: "success",
            title: page.props.flash.message,
          });
          closeFinal();
        },
        onError: (errors) => {
          console.log(errors);
        },
      });
    }
    function goFinal() {
      openBillName.value = true;
    }
    function closeFinal() {
      resetBillForm();
      openBillName.value = false;
    }
    function resetForm() {
      form.reset();
    }
    function resetBillForm() {
      form_1.reset();
    }
    function openModal() {
      isOpen.value = true;
    }
    function closeModal() {
      isOpen.value = false;
      resetForm();
      editMode.value = false;
    }
    function submit() {
      form.post("/updateTemp", {
        onSuccess: (page) => {
          Toast.fire({
            icon: "success",
            title: page.props.flash.message,
          });
        },
        onError: (errors) => {
          console.log(errors);
        },
      });
    }
    function clearData() {
      var conf = confirm("Are you sure want to remove all imported Data ?");
      if (conf) {
        router.post("/truncateBilling");
      }
    }

    function deleteRow(data) {
      if (!confirm("Are you sure want to remove?")) return;
      data._method = "DELETE";
      router.post("/billing/" + data.id, data);
      closeModal();
      resetForm();
    }
    function doExcel() {
      axios.post("/exportTempBillingExcel", parameter.value).then((response) => {
        console.log(response);
        var a = document.createElement("a");
        document.body.appendChild(a);
        a.style = "display: none";
        let blob = new Blob([response.data], { type: "text/csv" }),
          url = window.URL.createObjectURL(blob);
        a.href = url;
        a.download = "temp_billings.csv";
        a.click();
        window.URL.revokeObjectURL(url);
      });
    }
    function calc() {
      let sub_total = parseInt(form.current_charge) + parseInt(form.otc) + parseInt(form.public_ip) - parseInt(form.compensation);
      form.sub_total = sub_total.toFixed(0);
      let total_payable = parseInt(form.sub_total) - parseInt(form.discount);
      if (form.tax) {
        total_payable = parseInt(total_payable) + parseInt(form.tax);
      }
      form.total_payable = total_payable.toFixed(0);
    }
    function calTax() {
      let sub_total = parseInt(form.current_charge) + parseInt(form.otc) + parseInt(form.public_ip) - parseInt(form.compensation);
      form.sub_total = sub_total.toFixed(0);
      form.tax = (parseInt(form.sub_total) / 100) * 5;
      form.tax = form.tax.toFixed(0);
      calc();
    }
    function updateUsage() {
      var dt = new Date();
      var month = dt.getMonth();
      var year = dt.getFullYear();
      var daysInMonth = new Date(year, month, 0).getDate();
      let daycount = form.usage_day ? (form.normal_cost / daysInMonth) * form.usage_day : 0;
      let monthcount = form.usage_month ? form.usage_month * form.normal_cost : 0;
      form.current_charge = Math.round(daycount + monthcount);
      calc();
    }
    onMounted(() => {
      props.packages.map(function (x) {
        return (x.item_data = `${x.speed}Mbps - ${x.name} - ${x.contract_period} Months`);
      });
    });
    return { formatter, form, form_1, clearData, submit, deleteRow, isOpen, edit, closeModal, searchTsp, goSearch, toggleAdv, sort, search, show_search, calc, openBillName, saveFinal, goFinal, closeFinal, doExcel, calTax, updateUsage };
  },
};
</script>
<style scoped>
.loader {
  border-top-color: #3498db;
  -webkit-animation: spinner 1.5s linear infinite;
  animation: spinner 1.5s linear infinite;
}

@-webkit-keyframes spinner {
  0% {
    -webkit-transform: rotate(0deg);
  }

  100% {
    -webkit-transform: rotate(360deg);
  }
}

@keyframes spinner {
  0% {
    transform: rotate(0deg);
  }

  100% {
    transform: rotate(360deg);
  }
}
</style>