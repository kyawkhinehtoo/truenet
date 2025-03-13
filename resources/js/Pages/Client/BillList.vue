<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">Bill List</h2>
    </template>

    <div class="py-2">
      <PdfGenerationProgress :progress="pdfprogress" @dismiss="dismiss" v-if="showPdfProgress" />
      <SmsGenerationProgress :progress="smsprogress" @dismiss="dismiss" v-if="showSmsProgress" />
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex">
        <label for="name" class="block text-sm font-bold text-gray-700 w-24 mt-3">Billing List :</label>
        <select id="id" v-model="form.bill_id" name="bill_id"
          class="ml-2 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          tabindex="1" required>
          <option value="0">-Choose Package-</option>
          <option v-for="row in lists" v-bind:key="row.id" :value="row.id">{{ row.name }}</option>
        </select>

        <a @click="view"
          class="ml-2 cursor-pointer inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">View
          <i class="ml-1 fa fa-search text-white" tabindex="2"></i></a>
      </div>

      <div class="max-w-full mx-auto sm:px-6 lg:px-8 mt-4 flex justify-between"
        v-if="form.bill_id && form.bill_id != 0">
        <div class="flex">
          <a href="#" class="w-full text-right font-semibold text-xs underline mr-2" v-on:click="toggleAdv">Advance
            Search</a>
          <i class="fas fa-chevron-right text-gray-400" v-show="!show_search"></i>
          <i class="fas fa-chevron-down text-gray-400" v-show="show_search"></i>
        </div>
        <div class="flex">
          <a href="#" class="w-full text-right font-semibold text-xs underline mr-2" v-on:click="toggleCmd">Command
            Bar</a>
          <i class="fas fa-chevron-right text-gray-400" v-show="!show_command"></i>
          <i class="fas fa-chevron-down text-gray-400" v-show="show_command"></i>
        </div>

      </div>
      <div v-show="show_search" class="max-w-full mx-auto sm:px-6 lg:px-8 mt-4">
        <billing-search @search_parameter="goSearch" />
      </div>
      <div v-if="form.bill_id && form.bill_id != 0" v-show="show_command"
        class="max-w-full mx-auto sm:px-6 lg:px-8 mt-4 ">
        <div class="flex gap-2 bg-white shadow sm:rounded-lg space-x-2 items-center py-2 px-2 md:px-2"
          :class="[smsgateway?.status == '1' ? 'justify-between' : 'justify-end']">
          <div class="flex gap-2" v-if="smsgateway?.status == '1'">
            <a @click="sendAllSMS"
              class="cursor-pointer inline-flex items-center px-2 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:border-red-700 focus:ring focus:ring-red-300 disabled:opacity-25 transition">Broadcast
              Invoice <i class="ml-1 fa fa-sms text-white"></i></a>
            <a @click="sendBillReminder"
              class="cursor-pointer inline-flex items-center px-2 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-500 active:bg-red-700 focus:outline-none focus:border-yellow-700 focus:ring focus:ring-red-300 disabled:opacity-25 transition">Send
              Bill Reminder <i class="ml-1 fa fa-sms text-white"></i></a>
          </div>
          <div class="flex gap-2">
            <a @click="generateAllPDF"
              class="cursor-pointer inline-flex items-center px-2 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">Generate
              All PDF <i class="ml-1 fa fa-file-pdf text-white"></i></a>

            <a @click="createPrepaid"
              class="cursor-pointer inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:border-indigo-700 focus:ring focus:ring-indigo-300 disabled:opacity-25 transition">Add
              New Invoice <i class="ml-1 fa fa-plus-square text-white"></i></a>
            <a @click="doExcel"
              class="cursor-pointer inline-flex items-center px-2 py-2 bg-green-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition">Export
              <i class="ml-1 fa fa-download text-white"></i></a>
          </div>





        </div>
      </div>
      <div v-if="billings" class="max-w-full mx-auto sm:px-6 lg:px-8 mt-4">
        <div class="px-3 py-1 items-center bg-white rounded-md mb-2 shadow-sm flex justify-between w-full ">
          <div class="flex pt-1 w-full">
            <div class="relative w-full">
              <label class="text-xs">{{ paid_percent }}% Percentage of {{ (paid) ? new Intl.NumberFormat('en-US', {
                maximumSignificantDigits: 8
              }).format(paid) : 0
                }} Kyats in {{ new Intl.NumberFormat('en-US', {
                  maximumSignificantDigits: 8
                }).format(receivable)
                }} Kyats</label>
              <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-lightBlue-200 z-10">
                <div :style="`width: ${paid_percent}%`"
                  class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-lightBlue-500 z-10">
                </div>

              </div>
            </div>
          </div>

        </div>
        <div class="bg-white overflow-auto shadow-xl sm:rounded-lg" v-if="billings.data">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col"
                  class="pl-3 px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  No.</th>
                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Bill No.</th>
                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  C-ID</th>
                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Package</th>
                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Usage</th>
                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Total</th>

                <!--
                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Deliver SMS</th>
                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  SMS Sent Date</th>
                  -->
                <!-- <th scope="col" class="relative px-6 py-3"><span class="sr-only">Action</span></th> -->
                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  <i class="fa fa-print"></i> Invoice
                </th>
                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Invoice Delivery</th>
                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Receipt</th>
                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Receipt Status</th>
                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  <i class="fa fa-print"></i> Receipt
                </th>
                <th scope="col" class="relative px-6 py-3"><span class="sr-only" v-if="invoiceEdit">Action</span></th>


              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="(row, index) in billings.data " v-bind:key="row.id">
                <td class="pl-4 px-2 py-3 text-xs whitespace-nowrap">{{ (index += billings.from) }}</td>
                <td class="px-2 py-3 text-xs whitespace-nowrap">{{ row.bill_number }}</td>
                <td class="px-2 py-3 text-xs whitespace-nowrap">{{ row.ftth_id }}</td>
                <td class="px-2 py-3 text-xs whitespace-nowrap">{{ (row.service_description !== row.qty) ?
                  row.service_description + `(${row.qty})` : row.service_description }}</td>
                <td class="px-2 py-3 text-xs whitespace-nowrap">{{ row.usage_days }}</td>
                <td class="px-2 py-3 text-xs whitespace-nowrap">{{ row.total_payable }}</td>
                <td class="px-2 py-3 text-xs whitespace-nowrap">
                  <span v-if="row.total_payable > 0">
                    <span v-if="row.invoice_file"><a :href="'/s/' + row.invoice_url">Download</a></span><span
                      v-else><button type="button" @click="generatePDF(row.id)"
                        class="h-8 text-md w-24 bg-blue-600 rounded text-white hover:bg-blue-700">Make
                        PDF</button></span>
                  </span>
                </td>


                <td class="px-2 py-3 text-xs whitespace-nowrap">
                  <span v-if="row.total_payable > 0">
                    <span v-if="row.sms_sent_status">{{ row.sent_date }}</span><span v-else>
                      <span v-if="smsgateway?.status == '1' && row.invoice_file">
                        <button type="button" @click="sendSMS(row.id)"
                          class="h-8 text-md w-20 bg-red-600 rounded text-white hover:bg-red-700">Send</button>
                      </span>
                    </span>
                  </span>
                </td>
                <!--
                <td class="px-2 py-3 text-xs whitespace-nowrap">{{ row.sent_date ? row.sent_date : "None" }}</td>
                  -->
                <!-- <td class="px-2 py-3 text-xs whitespace-nowrap"><a :href="`/pdfpreview2/${row.id}`" target="_blank"><i class="fa fas fa-eye text-gray-400"></i></a></td> -->
                <!-- <td class="px-2 py-3 text-xs whitespace-nowrap"><a :href="`/showInvoice/${row.id}`" target="_blank"><i class="fa fas fa-eye text-gray-400"></i></a></td>  -->
                <td class="px-2 py-3 text-xs whitespace-nowrap">
                  <button type="button" @click="openReceipt(row)"
                    class="h-8 text-md w-24 bg-green-600 rounded text-white hover:bg-green-700"
                    v-if="row.receipt_status">View Receipt</button>
                  <button type="button" @click="openReceipt(row)"
                    class="h-8 text-md w-24 bg-green-400 rounded text-white hover:bg-green-500" v-else>Make
                    Receipt</button>
                </td>
                <td class="px-2 py-3 text-xs whitespace-nowrap capitalize">{{
                  (row.receipt_status) ? row.receipt_status.replace('_', ' ') : ''
                }}</td>
                <td class="px-2 py-3 text-xs whitespace-nowrap">
                  <span v-if="row.receipt_status">
                    <span v-if="row.receipt_file"><a :href="'/s/' + row.receipt_url">Download</a></span><span
                      v-else><button type="button" @click="generateReceiptPDF(row.receipt_id)"
                        class="h-8 text-md w-24 bg-blue-600 rounded text-white hover:bg-blue-700">Make
                        PDF</button></span>
                  </span>
                </td>
                <td class="px-6 py-3 text-xs whitespace-nowrap text-right font-medium" v-if="invoiceEdit">
                  <a href="#" @click="edit_invoice(row)" class="text-yellow-600 hover:text-yellow-900"><i
                      class="fas fa-pen"></i></a>
                  <span v-if="user.delete_invoice"> |
                    <a href="#" @click="deleteRow(row)" class="text-red-600 hover:text-red-900"><i
                        class="fas fa-trash"></i></a>
                  </span>

                </td>

              </tr>
            </tbody>
          </table>
        </div>
        <span v-if="billings.total" class="w-full block mt-4">
          <label class="text-xs text-gray-600">{{ billings.data.length }} Invoices in Current Page. Total Number of
            Invoices : {{ billings.total }}</label>
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
      </div>
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
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex justify-between w-full">
            <h1 class="flex text-indigo-700 text-md uppercase font-bold block pt-1 no-underline">Receipt Form</h1>
            <i class="flex fa fa-2x fas fa-times-circle text-red-500 hover:text-red-800 cursor-pointer"
              @click="closeModal"></i>
          </div>
          <form @submit.prevent="submit">
            <div class="shadow overflow-hidden border-b border-gray-200 p-4">
              <p v-show="$page.props.errors.receipt_date" class="mt-2 text-sm text-red-500 block">{{
                $page.props.errors.receipt_date
              }}</p>
              <p v-show="$page.props.errors.collected_amount" class="mt-2 text-sm text-red-500 block">{{
                $page.props.errors.collected_amount
              }}</p>
              <div class="grid grid-cols-1 md:grid-cols-4 w-full">

                <div class="col-span-2 sm:col-span-2 border-2 border-marga bg-marga">
                  <h1 class="text-gray-200 text-lg font-semibold mt-1 px-2">CASH RECEIPT</h1>
                </div>
                <div class="col-span-2 sm:col-span-2 border-b-2 border-marga justify-end flex">

                  <span class="inline-flex text-sm p-2">Payment Date: </span><input type="date"
                    class="py-2 focus:ring-indigo-500 focus:border-indigo-500 inline-flex sm:text-sm border-2 border-gray-300"
                    v-model="form.receipt_date" />

                </div>


              </div>
              <div class="grid grid-cols-1 md:grid-cols-4 gap-6 w-full py-4">
                <div class="col-span-3 sm:col-span-3 border-2 border-marga p-4">
                  <p>Payer Name : {{ form.bill_to }}</p>
                  <p>Payer Address : {{ form.attn }}</p>
                  <p>Payment Description : {{ form.service_description }}</p>
                  <p>Period Covered : {{ form.period_covered_2 }}</p>
                </div>
                <div class="col-span-1 sm:col-span-1 flex flex-col justify-between">
                  <div class="border-2 border-marga p-2 text-center flex flex-col">
                    <span class="font-semibold text-md">Reference :</span> <span class="text-sm"> {{ receipt_number
                      }}</span>
                  </div>
                  <div class="border-2 border-marga p-2 text-center flex flex-col mt-2">
                    <span class="font-semibold text-md">Bill Number:</span> <span class="text-sm"> {{ form.bill_number
                      }}</span>
                  </div>
                  <div class="border-2 border-marga p-2 text-center flex flex-col mt-2">
                    <span class="font-semibold text-md">Customer ID:</span> <span class="text-sm"> {{ form.ftth_id
                      }}</span>
                  </div>
                </div>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-4 gap-6 w-full">
                <div class="py-4 col-span-1 sm:col-span-1 border-2 border-marga text-center flex flex-col">
                  <span class="font-semibold text-md">Amount (MMK):</span> <span class="text-sm"> {{ form.total_payable
                    }}</span>
                </div>
                <div class="py-4 col-span-3 sm:col-span-3 border-2 border-marga text-center flex flex-col">
                  <span class="font-semibold text-md">Amount In Word:</span> <span class="text-sm"> {{
                    form.amount_in_word
                  }}</span>
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-6 w-full py-2 gap-6">
                <div class="col-span-1 sm:col-span-1">
                  <label class="block mt-2">Received Amount</label>
                </div>
                <div class="col-span-1 sm:col-span-1 border-b-2 border-marga"><input type="text"
                    class="py-2 px-0 inline-flex sm:text-sm border-0 focus:ring-0 w-full"
                    v-model="form.collected_amount" @change="calc" /></div>

                <div class="col-span-1 sm:col-span-1">
                  <label class="block mt-2">Payment Channel</label>
                </div>
                <div class="col-span-3 sm:col-span-3 border-b-2 border-marga">
                  <div class="flex">
                    <label class="flex-auto items-center mt-1"> <input type="checkbox"
                        class="h-5 w-5 rounded shadow hover:shadow-md text-yellow-400" checked name="wave_pay"
                        v-model="form.type" value="wave_pay" />
                      <span class="ml-2 text-gray-700 text-xs font-semibold">Wave Pay</span>
                    </label>
                    <label class="flex-auto items-center mt-1"> <input type="checkbox"
                        class="h-5 w-5 rounded shadow hover:shadow-md text-blue-600" name="kbz_pay" v-model="form.type"
                        value="kbz_pay" /><span class="ml-2 text-gray-700 text-xs font-semibold">KPay</span> </label>
                    <label class="flex-auto items-center mt-1"> <input type="checkbox"
                        class="h-5 w-5 rounded shadow hover:shadow-md text-red-400" name="quick_pay" v-model="form.type"
                        value="quick_pay" /><span class="ml-2 text-gray-700 text-xs font-semibold">KBZ Quickpay</span>
                    </label>
                    <label class="flex-auto items-center mt-1"> <input type="checkbox"
                        class="h-5 w-5 rounded shadow hover:shadow-md text-red-600" name="aya_pay" v-model="form.type"
                        value="aya_pay" /><span class="ml-2 text-gray-700 text-xs font-semibold">AYA Pay</span> </label>
                    <label class="flex-auto items-center mt-1"> <input type="checkbox"
                        class="h-5 w-5 rounded shadow hover:shadow-md text-green-600" name="citizen_pay"
                        v-model="form.type" value="citizen_pay" /><span
                        class="ml-2 text-gray-700 text-xs font-semibold">Citizen Pay</span>
                    </label>
                    <label class="flex-auto items-center mt-1"> <input type="checkbox"
                        class="h-5 w-5 rounded shadow hover:shadow-md text-indigo-600" name="cash" v-model="form.type"
                        value="cash" /><span class="ml-2 text-gray-700 text-xs font-semibold">Cash</span> </label>
                    <label class="flex-auto items-center mt-1"> <input type="checkbox"
                        class="h-5 w-5 rounded shadow hover:shadow-md text-indigo-400" name="offset" v-model="form.type"
                        value="offset" /><span class="ml-2 text-gray-700 text-xs font-semibold">Offset</span> </label>
                  </div>
                </div>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-6 w-full py-2 gap-6">
                <div class="col-span-1 sm:col-span-1">
                  <label class="block mt-2" v-if="outstanding">Outstanding Amount:</label>
                  <label class="block mt-2" v-else>Surplus Amount:</label>
                </div>
                <div class="col-span-1 sm:col-span-1 border-b-2 border-marga"><input type="text"
                    class="py-2 px-0 inline-flex sm:text-sm border-0 focus:ring-0 w-full" v-model="form.extra_amount" />
                </div>
                <div class="col-span-1 sm:col-span-1">
                  <label class="block mt-2">Transition ID:</label>
                </div>
                <div class="col-span-3 sm:col-span-3 border-b-2 border-marga">
                  <input type="text" class="py-2 px-0 inline-flex sm:text-sm border-0 focus:ring-0 w-full"
                    v-model="form.transition" />
                </div>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-6 w-full py-2 gap-6">
                <div class="col-span-1 sm:col-span-1">
                  <label class="block mt-2">Received By:</label>
                </div>
                <div class="col-span-1 sm:col-span-1 border-b-2 border-marga">
                  <multiselect :class="border - 0" deselect-label="Selected already" :options="users" track-by="id"
                    label="name" v-model="form.user" :allow-empty="false"></multiselect>

                </div>

                <div class="col-span-1 sm:col-span-1">
                  <label class="block mt-2">Remark:</label>
                </div>
                <div class="col-span-3 sm:col-span-3 border-b-2 border-marga"><textarea
                    class="py-2 px-0 inline-flex sm:text-sm border-0 focus:ring-0 w-full"
                    v-model="form.remark"></textarea>
                </div>
              </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
              <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                <button @click="saveReceipt" type="button"
                  class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"
                  v-if="!form.receipt_status">GO !</button>
              </span>
              <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
                <button @click="closeModal" type="button"
                  class="inline-flex justify-center w-full rounded-md border border-gray-300 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5">Cancel</button>
              </span>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div ref="editInvoice" class="fixed z-10 inset-0 overflow-y-auto ease-out duration-400" v-if="editInvoice">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity">
          <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <!-- This element is to trick the browser into centering the modal contents. -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>​
        <div
          class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-7xl sm:w-full"
          role="dialog" aria-modal="true" aria-labelledby="modal-headline">

          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 shadow sm:rounded-lg">
            <h6 class="md:min-w-full text-indigo-700 text-sm uppercase font-bold block pt-1 no-underline"
              v-if="editMode">
              Billing Detail Information</h6>
            <h6 class="md:min-w-full text-indigo-700 text-sm uppercase font-bold block pt-1 no-underline" v-else>Create
              Invoice</h6>

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
                  id="ftth_id" placeholder="Enter Customer ID" v-model="form_2.ftth_id" disabled v-if="editMode" />
                <multiselect deselect-label="Selected already" :options="prepaid_customers" track-by="id"
                  label="ftth_id" v-model="form_2.ftth_id" :allow-empty="true" v-else @select="updateData" />
                <div v-if="$page.props.errors.ftth_id" class="text-red-500">{{ $page.props.errors.ftth_id }}</div>

                <label for="bill_to" class="mt-4 block text-gray-700 text-sm font-bold mb-2">Customer Name
                  :</label>
                <input type="text"
                  class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                  id="bill_to" placeholder="Enter Bill To" v-model="form_2.bill_to" />


                <label for="attn" class="mt-4 block text-gray-700 text-sm font-bold mb-2">Customer Address
                  :</label>
                <textarea
                  class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                  id="attn" placeholder="Enter Attention" v-model="form_2.attn"></textarea>
                <div v-if="$page.props.errors.attn" class="text-red-500">{{ $page.props.errors.attn[0] }}</div>

              </div>


              <div class="mb-4 md:col-span-1">
                <div v-if="editMode">
                  <label for="bill_number" class="block text-gray-700 text-sm font-bold mb-2">Bill Number :</label>
                  <input type="text"
                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                    id="bill_number" placeholder="Enter Bill Number" v-model="form_2.bill_number" />
                  <div v-if="$page.props.errors.bill_number" class="text-red-500">{{ $page.props.errors.bill_number }}
                  </div>
                </div>
                <div v-else>
                  <label for="customer_status" class="block text-gray-700 text-sm font-bold mb-2">Customer Status
                    :</label>
                  <input type="text"
                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                    id="customer_status" v-model="form_2.customer_status" />
                  <div v-if="$page.props.errors.customer_status" class="text-red-500">{{
                    $page.props.errors.customer_status
                  }}</div>
                </div>

                <label for="date_issued" class="mt-4 block text-gray-700 text-sm font-bold mb-2">Bill Issue Date
                  :</label>
                <input type="date"
                  class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                  id="date_issued" placeholder="Enter Issue Date" v-model="form_2.date_issued" />


                <label for="payment_duedate" class="mt-4 block text-gray-700 text-sm font-bold mb-2">Bill Due
                  Date
                  :</label>
                <input type="date"
                  class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                  id="payment_duedate" placeholder="Enter Payment Due Date" v-model="form_2.payment_duedate" />
                <div v-if="$page.props.errors.payment_duedate" class="text-red-500">{{
                  $page.props.errors.payment_duedate[0] }}</div>

              </div>
              <div class="mb-4 md:col-span-1">
                <label for="start_date" class="block text-gray-700 text-sm font-bold mb-2">Bill Start Date
                  :</label>
                <input type="date"
                  class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                  id="start_date" placeholder="Enter Bill Start Date" v-model="form_2.start_date" />

                <label for="end_date" class="mt-4 block text-gray-700 text-sm font-bold mb-2">Bill End Date
                  :</label>
                <input type="date"
                  class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                  id="end_date" placeholder="Enter Bill End Date" v-model="form_2.end_date" />
                <label for="bonus_day" class="mt-4 block text-gray-700 text-sm font-bold mb-2">Bonus Day/Month
                  (If
                  Any)
                  :</label>
                <div class="flex justify-between ">

                  <input type="number"
                    class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300"
                    id="bonus_day" v-model="form_2.bonus_day" />
                  <span
                    class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                    Days </span>
                  <input type="number"
                    class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300"
                    id="bonus_month" v-model="form_2.bonus_month" />
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
                <label for="service_description" class="block text-gray-700 text-sm font-bold mb-2">Service Description
                  :</label>
                <multiselect deselect-label="Selected already" :options="packages" track-by="id" label="item_data"
                  v-model="form_2.package" :allow-empty="false" @select="updatePackage" />

                <label for="normal_cost" class="mt-4 block text-gray-700 text-sm font-bold mb-2">Original Package Price
                  :</label>



                <input type="number"
                  class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                  id="normal_cost" v-model="form_2.normal_cost" disabled />


                <div v-if="$page.props.errors.normal_cost" class="text-red-500">{{ $page.props.errors.normal_cost }}
                </div>

                <label for="qty" class="mt-4 block text-gray-700 text-sm font-bold mb-2">QTY :</label>
                <input type="text"
                  class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                  id="qty" placeholder="Enter QTY" v-model="form_2.qty" disabled />
                <div v-if="$page.props.errors.qty" class="text-red-500">{{ $page.props.errors.qty }}</div>



                <label for="type" class="mt-4 block text-gray-700 text-sm font-bold mb-2">Type :</label>
                <input type="text"
                  class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                  id="type" placeholder="Enter Type (MRC/Prepaid)" v-model="form_2.type" />
                <div v-if="$page.props.errors.type" class="text-red-500">{{ $page.props.errors.type }}</div>


                <label for="usage_days" class="mt-4 block text-gray-700 text-sm font-bold mb-2" v-else>Usage (Days -
                  Month) :</label>
                <div class="flex justify-between ">
                  <input type="number"
                    class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300"
                    id="usage_day" v-model="form_2.usage_day" @input="updateUsage" />
                  <span
                    class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                    Days </span>
                  <input type="number"
                    class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300"
                    id="usage_month" v-model="form_2.usage_month" @input="updateUsage" />
                  <span
                    class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                    Months </span>
                </div>



                <label for="email" class="mt-4 block text-gray-700 text-sm font-bold mb-2">Email :</label>
                <input type="text"
                  class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                  id="email" placeholder="Enter Email" v-model="form_2.email" />
                <div v-if="$page.props.errors.email" class="text-red-500">{{ $page.props.errors.email }}</div>

                <label for="phone" class="mt-4 block text-gray-700 text-sm font-bold mb-2">Phone :</label>
                <input type="text"
                  class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                  id="phone" placeholder="Enter Phone Number" v-model="form_2.phone" />
                <div v-if="$page.props.errors.phone" class="text-red-500">{{ $page.props.errors.phone }}</div>
                <!-- <fieldset class="mt-4 border border-solid border-gray-300 p-3 rounded-md" v-if="editMode">
                  <legend class="text-gray-700 text-sm font-bold">Meta Data </legend>

                  <div class="max-w-sm text-sm flex">
                    <label class="inline-flex ml-2">
                      <input
                        class="text-indigo-500 w-6 h-6 mr-2 focus:ring-indigo-400 focus:ring-opacity-25 border border-gray-300 rounded"
                        type="checkbox" v-model="form_2.reset_pdf" />
                      Reset PDF
                    </label>
                    <label class="inline-flex ml-2">
                      <input
                        class="text-green-500 w-6 h-6 mr-2 focus:ring-green-400 focus:ring-opacity-25 border border-gray-300 rounded"
                        type="checkbox" v-model="form_2.reset_sms" />
                      Reset SMS
                    </label>
                  
                  </div>
                </fieldset> -->
              </div>

              <div class="mb-4 md:col-span-2">
                <label for="current_charge" class="block text-gray-700 text-sm font-bold mb-2">Current Charge
                  :</label>
                <input type="number"
                  class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                  id="current_charge" placeholder="Enter Current Charge" v-model="form_2.current_charge"
                  @change="form2_calc" />
                <div v-if="$page.props.errors.current_charge" class="text-red-500">{{ $page.props.errors.current_charge
                  }}</div>
                <label for="otc" class="mt-4 block text-gray-700 text-sm font-bold mb-2">OTC :</label>
                <input type="number"
                  class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                  id="otc" placeholder="Enter OTC" v-model="form_2.otc" @change="form2_calc" />
                <div v-if="$page.props.errors.otc" class="text-red-500">{{ $page.props.errors.otc }}</div>
                <label for="public_ip" class="mt-4 block text-gray-700 text-sm font-bold mb-2">Public IP :</label>
                <input type="number"
                  class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                  id="otc" placeholder="Enter Public IP Charges" v-model="form_2.public_ip" @change="form2_calc" />
                <div v-if="$page.props.errors.otc" class="text-red-500">{{ $page.props.errors.public_ip }}</div>

                <label for="compensation" class="mt-4 block text-gray-700 text-sm font-bold mb-2">Compensation :</label>
                <input type="number"
                  class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                  id="compensation" placeholder="Enter Compensation" v-model="form_2.compensation"
                  @change="form2_calc" />
                <div v-if="$page.props.errors.compensation" class="text-red-500">{{ $page.props.errors.compensation }}
                </div>
                <label for="sub_total" class="mt-4 block text-gray-700 text-sm font-bold mb-2">Sub Total :</label>
                <input type="number"
                  class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                  id="total_mmk" placeholder="Enter Sub Total" v-model="form_2.sub_total" @change="form2_calc" />
                <div v-if="$page.props.errors.sub_total" class="text-red-500">{{ $page.props.errors.sub_total }}</div>

                <label for="tax" class="mt-4 block text-gray-700 text-sm font-bold mb-2">Commercial Tax (5%) : <button
                    type="button"
                    class="inline-flex justify-center rounded-md border border-gray-300 px-4 py-2 bg-blue-500 text-base leading-6 font-medium text-white shadow-sm hover:text-white focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5"
                    @click="calTax">Calculate </button> </label>
                <input type="number"
                  class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                  id="tax" v-model="form_2.tax" @change="form2_calc" />
                <div v-if="$page.props.errors.tax" class="text-red-500">{{ $page.props.errors.tax }}</div>

                <label for="discount" class="mt-4 block text-gray-700 text-sm font-bold mb-2">Discount :</label>
                <input type="number"
                  class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                  id="discount" placeholder="Enter Discount" v-model="form_2.discount" @change="form2_calc" />
                <div v-if="$page.props.errors.discount" class="text-red-500">{{ $page.props.errors.discount }}</div>



                <label for="total_payable" class="mt-4 block text-gray-700 text-sm font-bold mb-2">Total Payable
                  :</label>
                <input type="number"
                  class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                  id="total_payable" placeholder="Enter Total Payable" v-model="form_2.total_payable" />
                <div v-if="$page.props.errors.total_payable" class="text-red-500">{{ $page.props.errors.total_payable }}
                </div>

              </div>
            </div>
          </div>

          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
              <button @click="createInvoice" type="button"
                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"
                v-show="!editMode" :disabled="disable_submit">Save</button>
            </span>
            <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
              <button @click="updateInvoice" type="button"
                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"
                v-show="editMode" :disabled="disable_submit">Update</button>
            </span>
            <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
              <button @click="closeEdit" type="button"
                class="inline-flex justify-center w-full rounded-md border border-gray-300 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5">Close</button>
            </span>
          </div>

        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import { useForm } from "@inertiajs/vue3";
import { reactive, ref, provide, onMounted, onUpdated } from "vue";
import { router } from '@inertiajs/vue3';
import Multiselect from "@suadelabs/vue3-multiselect";
import Pagination from "@/Components/Pagination";
import BillingSearch from "@/Components/BillingSearch";
import PdfGenerationProgress from "@/Components/PdfGenerationProgress";
import SmsGenerationProgress from "@/Components/SmsGenerationProgress";
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';

export default {
  name: "BillList",
  components: {
    AppLayout,
    Pagination,
    BillingSearch,
    Multiselect,
    SmsGenerationProgress,
    PdfGenerationProgress,
    VueDatePicker,
  },
  props: {
    lists: Object,
    billings: Object,
    packages: Object,
    projects: Object,
    townships: Object,
    status: Object,
    errors: Object,
    user: Object,
    users: Object,
    roles: Object,
    max_receipt: Object,
    prepaid_customers: Object,
    receivable: String,
    paid: Number,
    current_bill: Object,
    last_invoices: Object,

    package_type: Object,
    smsgateway: Object,
  },
  setup(props) {
    provide("packages", props.packages);
    provide("projects", props.projects);
    provide("townships", props.townships);
    provide("status", props.status);

    provide("package_type", props.package_type);
    //Pusher.logToConsole = true;
    const pdfprogress = ref(0);
    const smsprogress = ref(0);

    const formatter = ref({
      date: "YYYY-MM-DD",
      month: "MMM",
    });
    let show_search = ref(false);
    let show_command = ref(false);
    let loading = ref(false);
    let parameter = ref("");
    let isOpen = ref(false);
    let editMode = ref(false);
    let editInvoice = ref(false);
    let outstanding = ref(false);
    let invoiceEdit = ref(false);
    let receipt_number = ref(0);
    const paid_percent = ref(0);
    let disable_submit = ref(false);

    const showPdfProgress = ref(false);
    const showSmsProgress = ref(false);
    const form = reactive({
      id: null,
      bill_id: (props.current_bill) ? props.current_bill['id'] : null,
      customer_id: null,
      period_covered: null,
      period_covered_2: null,
      bill_number: null,
      ftth_id: null,
      date_issued: null,
      receipt_date: null,
      bill_to: null,
      attn: null,
      service_description: null,
      total_payable: null,
      bill_year: null,
      bill_month: null,
      amount_in_word: null,
      user: null,
      type: [],
      currency: "mmk",
      transition: null,
      collected_amount: 0,
      extra_amount: 0,
      remark: null,
      receipt_status: null,
    });


    function resetForm() {
      form.id = null;
      form.bill_id = (props.current_bill) ? props.current_bill['id'] : null;
      form.customer_id = null;
      form.period_covered = null;
      form.period_covered_2 = null;
      form.bill_number = null;
      form.ftth_id = null;
      form.date_issued = null;
      form.receipt_date = null;
      form.bill_to = null;
      form.attn = null;
      form.service_description = null;
      form.total_payable = null;
      form.bill_year = null;
      form.bill_month = null;
      form.amount_in_word = null;
      form.user = null;
      form.type = [];
      form.transition = null;
      form.currency = "mmk";
      form.collected_amount = 0;
      form.extra_amount = 0;
      form.remark = null;
      form.receipt_status = null;
      receipt_number.value = 0;
    }

    const form_2 = useForm({
      id: null,
      bill_id: (props.current_bill) ? props.current_bill['id'] : null,
      customer_id: null,
      start_date: null,
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
      tax: null,
      email: null,
      phone: null,
      reset_pdf: null,
      reset_email: null,
      reset_sms: null,
      reset_receipt: null,
      receipt_id: null,
      customer_status: null,
      package: null,
      end_date: null,
      public_ip: 0,
    });
    function edit_invoice(data) {

      form_2.id = data.id;
      form_2.bill_id = data.bill_id;
      form_2.customer_id = data.customer_id;
      form_2.start_date = data.start_date;
      form_2.end_date = data.end_date;
      form_2.bill_number = data.bill_number;
      form_2.ftth_id = data.ftth_id;
      form_2.date_issued = data.date_issued;
      form_2.bill_to = data.bill_to;
      form_2.attn = data.attn;
      form_2.previous_balance = data.previous_balance;
      form_2.current_charge = data.current_charge;
      form_2.compensation = data.compensation;
      form_2.otc = data.otc;
      form_2.sub_total = data.sub_total;
      form_2.payment_duedate = data.payment_duedate;
      form_2.service_description = data.service_description;
      form_2.qty = data.qty;
      form_2.usage_day = data.usage_day;
      form_2.usage_month = data.usage_month;
      form_2.bonus_day = data.bonus_day;
      form_2.bonus_month = data.bonus_month;
      form_2.public_ip = (data.public_ip) ? data.public_ip : 0;
      form_2.tax = data.tax;
      form_2.normal_cost = data.normal_cost;
      form_2.type = (data.type) ? data.type : 'Prepaid';
      form_2.total_payable = data.total_payable;
      form_2.discount = data.discount;
      form_2.email = data.email;
      form_2.phone = data.phone;
      form_2.reset_receipt = data.reset_receipt;
      form_2.receipt_id = data.receipt_id;
      form_2.package = props.packages.filter((d) => (d.name == data.service_description))[0];
      editMode.value = true;
      openEdit();
    }
    function createPrepaid() {
      form_2.reset();
      editMode.value = false;
      openEdit();
    }
    function updateData(option, id) {

      form_2.customer_id = option.id;
      form_2.bill_id = (props.current_bill) ? props.current_bill['id'] : null;
      form_2.start_date = null;
      form_2.end_date = null;
      form_2.customer_status = option.customer_status;
      form_2.date_issued = new Date('Y-m-d');
      form_2.bill_to = option.name;
      form_2.attn = option.address;
      form_2.previous_balance = 0;
      form_2.current_charge = option.package_price;
      form_2.compensation = 0;
      form_2.otc = 0;
      form_2.public_ip = (option.public_ip) ? option.public_ip : 0;
      form_2.package = props.packages.filter((d) => d.name == option.package_name)[0];
      //form_2.sub_total = data.sub_total;
      //form_2.payment_duedate = data.payment_duedate;
      form_2.service_description = option.package_name;
      form_2.qty = option.package_speed + ' Mbps';
      form_2.usage_month = 1;
      form_2.usage_day = 0;
      //form_2.tax = data.tax;
      form_2.normal_cost = option.package_price;
      form_2.type = "Prepaid";
      form_2.discount = 0;
      form_2.email = option.email;
      form_2.phone = (option.phone_1) ? option.phone_1 : '';
      form2_calc();
    }
    function updatePackage(option, id) {
      console.log('print out the normal cost');
      form_2.service_description = option.name;
      form_2.qty = option.speed + ' Mbps';
      form_2.normal_cost = option.price;
      updateUsage();
    }
    function updateUsage() {
      var dt = new Date();
      var month = dt.getMonth();
      var year = dt.getFullYear();
      var daysInMonth = new Date(year, month, 0).getDate();

      let daycount = form_2.usage_day ? (form_2.normal_cost / daysInMonth) * form_2.usage_day : 0;
      let monthcount = form_2.usage_month ? form_2.usage_month * form_2.normal_cost : 0;
      form_2.current_charge = Math.round(daycount + monthcount);
      form2_calc();
    }
    function resetEdit() {
      form_2.reset();
    }
    function openEdit() {
      editInvoice.value = true;
    }
    function closeEdit() {
      editInvoice.value = false;
      resetEdit();
      editMode.value = false;
    }
    function createInvoice() {
      disable_submit.value = true;
      form_2.post("/createInvoice", {
        onSuccess: (page) => {
          closeEdit();
          disable_submit.value = false;
          Toast.fire({
            icon: "success",
            title: page.props.flash.message,
          });
        },
        onError: (errors) => {
          disable_submit.value = false;
          console.log(errors);
        },
      });
    }
    function updateInvoice() {
      disable_submit.value = true;
      if (form_2.receipt_id) {
        Toast.fire({
          icon: "warning",
          title: "Are you sure you want to do ?",
          text: "Updating the data will be reset the receipt !",
          showCancelButton: true,
          closeOnConfirm: false,
          closeOnCancel: false,
          confirmButtonColor: '#DD6B55',
          confirmButtonText: 'Yes, sure!',
          cancelButtonText: "No, cancel it!",
          timer: false,
          timerProgressBar: false,
        }).then(function (result) {
          if (result['isConfirmed']) {
            form_2.post("/updateInvoice", {
              onSuccess: (page) => {
                disable_submit.value = false;
                Toast.fire({
                  icon: "success",
                  title: page.props.flash.message,
                });
              },
              onError: (errors) => {
                disable_submit.value = false;
                console.log(errors);
              },
            });
          } else {
            disable_submit.value = false;
            Toast.fire("Cancelled", "Your data is safe", "error");
          }


        });
      } else {
        form_2.post("/updateInvoice", {
          onSuccess: (page) => {
            disable_submit.value = false;
            Toast.fire({
              icon: "success",
              title: page.props.flash.message,
            });
          },
          onError: (errors) => {
            disable_submit.value = false;
            console.log(errors);
          },
        });
      }


    }
    const goSearch = (parm) => {
      let url = "/showbill";
      if (form.bill_id != null) {
        parm.bill_id = form.bill_id;
      }
      parameter.value = parm;
      router.get(url, parm, { preserveState: true });
    };
    const toggleAdv = () => {
      show_search.value = !show_search.value;
      //console.log(props.route);
    };
    const toggleCmd = () => {
      show_command.value = !show_command.value;

    };

    function view() {
      //form._method = "POST";
      let parm = Object.create({});
      parm.bill_id = form.bill_id;
      parameter.value = parm;
      form._method = "GET";
      router.get("/showbill", form, {
        preserveState: true,
        resetOnSuccess: true,
        onSuccess: (page) => { },
        onError: (errors) => {
          console.log("error ..".errors);
        },
      });
    }
    function getFile($data) {
      if ($data) {
        return "<a href=" + $data + ">Download</a>";
      } else {
        return "No File";
      }
    }
    function generateReceiptPDF(data) {
      router.post("/getReceiptPDF/" + data, data, {
        preserveState: true,
        onSuccess: (page) => {
          loading.value = false;
          Toast.fire({
            icon: "success",
            title: page.props.flash.message,
          });

        },
        onError: (errors) => {

          console.log("error ..".errors);
        },
        onStart: (pending) => {

          loading.value = true;
        },
      });
    }
    function generatePDF(data) {
      router.post("/getSinglePDF/" + data, data, {
        preserveState: true,
        onSuccess: (page) => {
          loading.value = false;
          Toast.fire({
            icon: "success",
            title: page.props.flash.message,
          });
        },
        onError: (errors) => {
          closeModal();
          console.log("error ..".errors);
        },
        onStart: (pending) => {

          loading.value = true;
        },
      });
    }
    function generateAllPDF() {
      router.post("/getAllPDF", parameter.value, {
        preserveState: true,
        onSuccess: (page) => {
          loading.value = false;
          Toast.fire({
            icon: "success",
            title: page.props.flash.message,
          });
        },
        onError: (errors) => {
          closeModal();
          console.log("error ..".errors);
        },
        onStart: (pending) => {

          loading.value = true;
        },
      });
    }
    function sendSMS(data) {

      router.post("/sendSingleSMS/" + data, data, {
        preserveState: true,
        onSuccess: (page) => {
          loading.value = false;
          Toast.fire({
            icon: "success",
            title: page.props.flash.message,
          });
        },
        onError: (errors) => {
          closeModal();
          console.log("error ..".errors);
        },
        onStart: (pending) => {

        },
      });
    }
    function sendAllSMS() {
      let data = parameter.value;
      router.post("/sendAllSMS", data, {
        preserveState: true,
        onSuccess: (page) => {
          loading.value = false;
          Toast.fire({
            icon: "success",
            title: page.props.flash.message,
          });
        },
        onError: (errors) => {
          closeModal();
          console.log("error ..".errors);
        },
        onStart: (pending) => {

          loading.value = true;
        },
      });
    }
    function sendBillReminder() {
      let data = parameter.value;
      router.post("/sendBillReminder", data, {
        preserveState: true,
        onSuccess: (page) => {
          loading.value = false;
          Toast.fire({
            icon: "success",
            title: page.props.flash.message,
          });
        },
        onError: (errors) => {
          closeModal();
          console.log("error ..".errors);
        },
        onStart: (pending) => {

          loading.value = true;
        },
      });
    }

    function doExcel() {

      axios.post("/exportBillingExcel", parameter.value, { responseType: "blob" }).then((response) => {
        console.log(response);
        var a = document.createElement("a");
        document.body.appendChild(a);
        a.style = "display: none";
        var blob = new Blob([response.data], {
          type: response.headers["content-type"],
        });
        const link = document.createElement("a");
        link.href = window.URL.createObjectURL(blob);
        link.download = `billings_${new Date().getTime()}.xlsx`;
        link.click();
      });
    }
    function openModal() {

      isOpen.value = true;
    }
    function closeModal() {
      isOpen.value = false;
      resetForm();
      editMode.value = false;
    }
    function openReceipt(data) {
      form.id = data.id;
      form.bill_id = data.bill_id;
      form.customer_id = data.customer_id;
      if (data.start_date && data.end_date) {

        var date_options = { year: 'numeric', month: 'short', day: 'numeric' };
        // date_options.timeZone = 'Asia/Rangoon';
        // date_options.timeZoneName = 'long';


        var from_date = new Intl.DateTimeFormat('en-SG', date_options).format(new Date(data.start_date));
        var to_date = new Intl.DateTimeFormat('en-SG', date_options).format(new Date(data.end_date));
        form.period_covered_2 = from_date + ' to ' + to_date;

      }
      form.period_covered = data.period_covered;
      form.bill_number = data.bill_number;
      form.ftth_id = data.ftth_id;
      form.date_issued = data.date_issued;
      form.bill_to = data.bill_to;
      form.attn = data.attn;
      form.service_description = data.service_description;
      form.total_payable = data.total_payable;
      form.bill_year = data.bill_year;
      form.bill_month = data.bill_month;
      form.amount_in_word = data.amount_in_word;
      form.receipt_date = data.receipt_date;
      form.user = (data.collected_person) ? props.users.filter((d) => d.id == data.collected_person)[0] : null;
      form.collected_amount = data.collected_amount;
      form.type = (data.payment_channel) ? parseQuotedStringToArray(data.payment_channel) : [];
      form.transition = data.transition;
      form.remark = data.remark;
      form.currency = (data.currency) ? data.currency : 'mmk';
      form.receipt_status = data.receipt_status;
      if (data.receipt_number)
        receipt_number.value = 'R' + data.bill_number.substring(0, 4) + '-' + data.ftth_id + '-' + ('00000' + data.receipt_number).slice(-5);
      else
        receipt_number.value = 'R' + data.bill_number.substring(0, 4) + '-' + data.ftth_id + '-' + ('00000' + (props.max_receipt.max_receipt_number + 1)).slice(-5);
      calc();
      openModal();
    }
    function parseQuotedStringToArray(quotedString) {
      return quotedString
        .split(',') // Split by commas
        .map(item => item.trim().replace(/^"|"$/g, '')); // Trim spaces and remove quotes
    }
    function saveReceipt() {
      form._method = "POST";
      router.post("/saveReceipt", form, {
        preserveState: true,
        onSuccess: (page) => {
          loading.value = false;
          Toast.fire({
            icon: "success",
            title: page.props.flash.message,
          });
          closeModal();
        },
        onError: (errors) => {

          loading.value = false;
          console.log("error ..".errors);
        },
        onStart: (pending) => {
          console.log("Loading .." + pending);
          loading.value = true;
        },
      });
    }
    function calc() {
      if (parseInt(form.collected_amount) < parseInt(form.total_payable)) {
        outstanding.value = true;
        form.extra_amount = parseInt(form.total_payable) - parseInt(form.collected_amount);
      } else {
        outstanding.value = false;
        form.extra_amount = parseInt(form.collected_amount) - parseInt(form.total_payable);
      }

      if (isNaN(form.extra_amount)) {
        form.extra_amount = 0;
      }

    }
    function calTax() {
      let sub_total = parseInt(form_2.current_charge) + parseInt(form_2.otc) + parseInt(form_2.public_ip) - parseInt(form_2.compensation);
      form_2.sub_total = sub_total.toFixed(0);
      form_2.tax = (parseInt(form_2.sub_total) / 100) * 5;
      form_2.tax = form_2.tax.toFixed(0);
      form2_calc();
    }
    function form2_calc() {
      let sub_total = parseInt(form_2.current_charge) + parseInt(form_2.otc) + parseInt(form_2.public_ip) - parseInt(form_2.compensation);
      form_2.sub_total = sub_total.toFixed(0);
      let total_payable = parseInt(form_2.sub_total) - parseInt(form_2.discount);
      if (form_2.tax) {
        total_payable = parseInt(total_payable) + parseInt(form_2.tax);
      }
      form_2.total_payable = total_payable.toFixed(0);
    }
    function checkEdit() {
      const my_role = props.roles.filter((d) => d.id == props.user.role)[0];
      if (my_role.edit_invoice) {
        return true;
      }
    }
    // function getMonth(m) {
    //   return Intl.DateTimeFormat("en", { month: "long" }).format(new Date(m));
    // }
    function cal_percent() {
      if (props.paid != 0) {

        let temp = (props.paid / props.receivable) * 100;
        paid_percent.value = temp.toFixed(2);
      } else {
        paid_percent.value = 0;
      }
    }
    const dismiss = (e) => {

      if (e) {

        showPdfProgress.value = !e;
        showSmsProgress.value = !e;
      }
    };

    function updateDateMonth(str) {

      let matches = str.match(/(\d+)\s*Months?\s*(?:and\s*(\d+)\s*Days?)?/i);
      let matches_2 = str.match(/(?:(\d+)\s*Months?\s*and\s*?)?(\d+)\s*Day?/i);

      if (matches) {
        console.log(matches);
        const [, months, days] = matches.map(match => (match ? parseInt(match, 10) : 0));

        if (months > 0) {
          form_2.usage_mo = months;
        }

        if (days > 0) {
          form_2.usage_d = days;
        }
      } else if (matches_2) {
        const [days] = matches_2.map(match => (match ? parseInt(match, 10) : 0));
        console.log(matches_2);
        if (days > 0) {
          form_2.usage_d = days;
        }
      } else {
        console.error("No numeric values followed by 'Months' or 'Days' found in the string.");
      }

    }
    onMounted(() => {
      console.log("Hey I am mount3ed");
      //  console.log(props.packages);
      props.packages.map(function (x) {
        return (x.item_data = `${x.price} MMK - ${x.name}`);
      });

      
      // window.Echo.channel("pdf-generation").listen("PdfGenerationProgress", (event) => {
      //   console.log("PdfGenerationProgress : " + event.progress);
      //   progress.value = event.progress;

      // });
      // window.Echo.channel('pdfprogressbar')
      //   .listen('.pdfgen', (e) => {

      //     pdfprogress.value = e.progress;
      //     if (typeof props.billings == 'object') {
      //       props.billings.data.map(function (x) {
      //         if (x.id == e.invoice_id) {
      //           x.invoice_file = e.invoice_file;
      //           x.invoice_url = e.invoice_url;
      //         }
      //       });
      //     }
      //     if (pdfprogress.value > 0) {
      //       showPdfProgress.value = true;
      //     }
      //   });

      // window.Echo.channel('smsprogressbar')
      //   .listen('.billsms', (e) => {
      //     smsprogress.value = e.progress;
      //     if (typeof props.billings == 'object') {
      //       props.billings.data.map(function (x) {
      //         if (x.id == e.invoice_id) {
      //           const date = new Date();
      //           const options = { day: 'numeric', month: 'short', year: 'numeric' };
      //           const formattedDate = date.toLocaleDateString('en-UK', options);
      //           x.sms_sent_status = 'sent';
      //           return x.sent_date = formattedDate;
      //         }
      //       });
      //     }

      //     if (smsprogress.value > 0) {
      //       showSmsProgress.value = true;
      //     }
      //   });

     // console.log("Now it is listening");

      //   if (typeof   props.billings == 'object' ) {
      //   props.billings.data.map(function (x) {
      //     let end_date = null;
      //     let last_invoice;
      //     last_invoice = props.last_invoices.filter((d) => d.customer_id == x.customer_id)[0];
      //    // if (last_invoice.period_covered) {
      //     if (typeof last_invoice == 'object' ) {
      //       console.log(last_invoice);
      //       if (last_invoice.period_covered.indexOf(' to ') !== false) {
      //         let t_date = last_invoice.period_covered.split(" to ");
      //         end_date = t_date[1];
      //       }
      //     }
      //     return x.end_date = end_date;
      //   });
      // }
      //if (typeof   props.prepaid_customers == 'object' ) {
      // props.prepaid_customers.map(function (x) {
      //   let end_date = null;
      //   let last_invoice;
      //    last_invoice = props.last_invoices.filter((d) => d.customer_id == x.customer_id)[0];
      //    if (typeof last_invoice == 'object' ) {
      //     console.log(last_invoice);
      //     if (last_invoice.period_covered.indexOf(' to ') !== false) {
      //       let t_date = last_invoice.period_covered.split(" to ");
      //       end_date = t_date[1];
      //     }
      //   }
      //   return x.end_date = end_date;
      // });

      //  }
      invoiceEdit.value = checkEdit();
      cal_percent();
      let bill_id = (props.current_bill) ? props.current_bill['id'] : null;
      if (bill_id) {
        let parm = Object.create({});
        parm.bill_id = bill_id;
        parameter.value = parm;
      }
    });
    const deleteRow = (data) => {
      if (!confirm("Are you sure want to Delete?")) return;
      data._method = "DELETE";
      router.post("/deleteInvoice/" + data.id, data);
    }
    onUpdated(() => {
      props.packages.map(function (x) {
        return (x.item_data = `${x.price} Baht - ${x.name} ${x.site_name}`);
      });



      //  if (typeof   props.billings == 'object' ) {
      //       props.billings.data.map(function (x) {
      //         let end_date = null;
      //         let last_invoice;
      //         last_invoice = props.last_invoices.filter((d) => d.customer_id == x.customer_id)[0];
      //        // if (last_invoice.period_covered) {
      //         if (typeof last_invoice == 'object' ) {
      //           console.log(last_invoice);
      //           if (last_invoice.period_covered.indexOf(' to ') !== false) {
      //             let t_date = last_invoice.period_covered.split(" to ");
      //             end_date = t_date[1];
      //           }
      //         }
      //         return x.end_date = end_date;
      //       });
      //     }
      // if (typeof   props.prepaid_customers == 'object' ) {
      //   props.prepaid_customers.map(function (x) {
      //     let end_date = null;
      //     let last_invoice;
      //      last_invoice = props.last_invoices.filter((d) => d.customer_id == x.customer_id)[0];
      //      if (typeof last_invoice == 'object' ) {
      //       console.log(last_invoice);
      //       if (last_invoice.period_covered.indexOf(' to ') !== false) {
      //         let t_date = last_invoice.period_covered.split(" to ");
      //         end_date = t_date[1];
      //       }
      //     }
      //     return x.end_date = end_date;
      //   });
      // }
      invoiceEdit.value = checkEdit();
      cal_percent();
      form_2.bill_id = (props.current_bill) ? props.current_bill['id'] : null;
      form.bill_id = (props.current_bill) ? props.current_bill['id'] : null;
      let bill_id = (props.current_bill) ? props.current_bill['id'] : null;
      if (bill_id) {
        let parm = Object.create({});
        parm.bill_id = bill_id;
        parameter.value = parm;
      }
    });
    return { form, form_2, formatter, view, show_search, show_command, toggleAdv, toggleCmd, goSearch, getFile, generatePDF, loading, generateAllPDF, sendSMS, parameter, sendAllSMS, doExcel, openReceipt, closeModal, calc, form2_calc, calTax, isOpen, outstanding, saveReceipt, updateInvoice, generateReceiptPDF, receipt_number, editInvoice, edit_invoice, openEdit, closeEdit, createPrepaid, invoiceEdit, updateData, editMode, paid_percent, createInvoice, updateUsage, disable_submit, updatePackage, pdfprogress, smsprogress, dismiss, showPdfProgress, showSmsProgress, sendBillReminder, deleteRow };
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

.bg-marga {
  background: #1e3a8a;
  color: #ffffff;
}

.border-marga {
  border-color: #255978;
}
</style>