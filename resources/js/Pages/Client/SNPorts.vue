<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-white leading-tight">DN SN Report</h2>
        </template>

        <div class="py-2">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">


                <div class="grid grid-cols-1 md:grid-cols-5 gap-2 w-full">
                    <div class="col-span-1 sm:col-span-1">
                        <label for="sh_general" class="block text-sm font-medium text-gray-700">General </label>

                        <div class="mt-1 flex rounded-md ">
                            <span
                                class="z-10 leading-snug font-normal  text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                                <i class="fas fa-user"></i>
                            </span>
                            <input type="text" v-model="searchForm.general" name="sh_general" id="sh_general"
                                class="pl-10 py-2 focus:ring-0 flex-1 block w-full rounded-md sm:text-sm border-gray-200 text-gray-600"
                                placeholder="Customer/Company Name etc." tabindex="1" />
                        </div>

                    </div>
                    <div class="col-span-1 sm:col-span-1">
                        <label for="sh_dn" class="block text-sm font-medium text-gray-700">DN </label>
                        <div class="mt-1">
                            <multiselect deselect-label="Selected already" :options="dns" track-by="name" label="name"
                                v-model="searchForm.dn" :allow-empty="true" @select="DNSelect"
                                placeholder="Please Choose DN">
                            </multiselect>
                        </div>

                    </div>
                    <div class="col-span-1 sm:col-span-1">
                        <label for="sh_sn" class="block text-sm font-medium text-gray-700">SN </label>
                        <div class="mt-1" v-if="res_sn">
                            <multiselect deselect-label="Selected already" :options="res_sn" track-by="id" label="name"
                                v-model="searchForm.sn" :allow-empty="true"></multiselect>
                        </div>
                        <div v-else class="mt-1">
                            <input type="text"
                                class="py-2 focus:ring-0 flex-1 block w-full rounded-md sm:text-sm border-gray-200 text-gray-400"
                                value="Please Choose SN" disabled />
                        </div>
                    </div>
                    <div class="col-span-1 sm:col-span-1 grid grid-cols-2 gap-2">
                        <div>
                            <label for="sh_customer" class="block text-sm font-medium text-gray-700">Min Customer
                            </label>
                            <div class="mt-1 flex rounded-md ">
                                <span
                                    class="z-10 leading-snug font-normal  text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                                    <i class="fas fa-greater-than-equal"></i>
                                </span>
                                <input type="number" v-model="searchForm.customer_min" name="sh_general" id="sh_general"
                                    class="pl-10 py-2 focus:ring-0 flex-1 block w-full rounded-md sm:text-sm border-gray-200 text-gray-400"
                                    tabindex="4" />
                            </div>
                        </div>
                        <div>
                            <label for="sh_customer" class="block text-sm font-medium text-gray-700">Max Customer
                            </label>
                            <div class="mt-1 flex rounded-md ">
                                <span
                                    class="z-10 leading-snug font-normal  text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                                    <i class="fas fa-less-than-equal"></i>
                                </span>
                                <input type="number" v-model="searchForm.customer_max" name="sh_general" id="sh_general"
                                    class="pl-10 py-2 focus:ring-0 flex-1 block w-full rounded-md sm:text-sm border-gray-200 text-gray-400"
                                    tabindex="5" />
                            </div>
                        </div>

                    </div>
                    <div class="col-span-1 sm:col-span-1 flex self-end mb-1">
                        <a @click="searchPort"
                            class="cursor-pointer inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">Search
                            <i class="ml-1 fa fa-search text-white" tabindex="6"></i></a>
                        <a @click="resetSearch"
                            class="ml-2 cursor-pointer inline-flex items-center px-4 py-2 bg-yellow-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:border-yellow-900 focus:ring focus:ring-yellow-300 disabled:opacity-25 transition">Reset
                            <i class="ml-1 fa fa-eraser text-white" tabindex="7"></i>
                        </a>
                    </div>
                </div>


                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg" v-if="overall.data">
                    <table class="min-w-full divide-y divide-gray-200 table-auto ">
                        <thead class="bg-gray-50">
                            <tr class="text-left">
                                <th scope="col" class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">No.</th>
                                <th scope="col" class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">DN/Port
                                </th>
                                <th scope="col" class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">SN Name
                                </th>
                                <th scope="col" class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">Total
                                    Customers
                                </th>
                                <th scope="col" class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">
                                    Description</th>
                                <th scope="col" class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">Location
                                </th>
                                <th scope="col" class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">Input dbm
                                </th>
                                <th scope="col" class="relative px-6 py-3"><span class="sr-only">Action</span></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 max-h-64 ">
                            <tr v-for="(row, index) in overall.data" v-bind:key="row.id">
                                <td class="px-6 py-3 font-medium">{{ overall.from + index }}</td>
                                <td class="px-6 py-3 font-medium">{{ row.dn_name }}</td>
                                <td class="px-6 py-3 font-medium">{{ row.name }}</td>
                                <td class="px-6 py-3 font-medium">{{ row.ports }}</td>
                                <td class="px-6 py-3 font-medium">{{ row.description }}</td>
                                <td class="px-6 py-3 font-medium">{{ row.location }}</td>
                                <td class="px-6 py-3 font-medium">{{ row.input_dbm }}</td>
                                <td class="px-6 py-3 text-xs font-medium text-right">
                                    <a href="#" @click="editSN(row)" class="text-indigo-600 hover:text-indigo-900">View
                                        Detail</a>

                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <span v-if="overall.total" class="w-full block mt-4">
                    <label class="text-xs text-gray-600">{{ overall.data.length }} SN Port List in Current Page. Total
                        Number of
                        SN
                        Ports : {{ overall.total }}</label>
                </span>
                <span v-if="overall.links">
                    <pagination class="mt-6" :links="overall.links" />
                </span>


            </div>
        </div>
    </app-layout>
    <jet-confirmation-modal :show="id" @close="id = null">
        <template #title> Delete Node</template>
        <template #content> Are you sure you would like to delete this API token? </template>
        <template #footer>
            <jet-secondary-button @click="id = null"> Cancel </jet-secondary-button>
            <jet-danger-button class="ml-2" @click="deleteNode"> Delete </jet-danger-button>
        </template>
    </jet-confirmation-modal>
    <jet-dialog-modal :show="showSN" @close="showSN = false">
        <template #title> Add New Port </template>
        <template #content>
            <div v-if="$page.props.errors[0]" class="text-red-500">{{ $page.props.errors[0] }}</div>
            <div>
                <div class="mt-4 text-sm">
                    <div class="mt-1 flex rounded-md shadow-sm" v-if="dns.length !== 0">
                        <multiselect deselect-label="Selected already" :options="dns" track-by="id" label="name"
                            v-model="form.dn_id" :allow-empty="true"></multiselect>
                    </div>
                    <div class="mb-4 md:col-span-1">
                        <label for="name" class="block text-gray-700 text-sm font-bold mb-2">SN Name :</label>
                        <input type="text"
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                            id="name" placeholder="Enter SN Name" v-model="form.name" />
                        <div v-if="$page.props.errors.name" class="text-red-500">{{ $page.props.errors.name }}
                        </div>
                    </div>
                    <div class="mb-4 md:col-span-1">
                        <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Description
                            :</label>
                        <textarea
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                            id="description" placeholder="Enter Description" v-model="form.description" />
                        <div v-if="$page.props.errors.description" class="text-red-500">{{
                            $page.props.errors.description }}
                        </div>
                    </div>
                    <div class="mb-4 md:col-span-1">
                        <label for="location" class="block text-gray-700 text-sm font-bold mb-2">SN Location :</label>
                        <input type="text"
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                            id="location" placeholder="Enter Location (Lat,Long)" v-model="form.location" />
                        <div v-if="$page.props.errors.location" class="text-red-500">{{ $page.props.errors.location }}
                        </div>
                    </div>
                    <div class="mb-4 md:col-span-1">
                        <label for="input_dbm" class="block text-gray-700 text-sm font-bold mb-2">SN Input dbm :</label>
                        <input type="text"
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                            id="input_dbm" placeholder="Enter Input Dbm" v-model="form.input_dbm" />
                        <div v-if="$page.props.errors.input_dbm" class="text-red-500">{{ $page.props.errors.input_dbm }}
                        </div>
                    </div>

                </div>
            </div>
        </template>
        <template #footer>
            <jet-secondary-button @click="cancelSN"> Close </jet-secondary-button>
        </template>
    </jet-dialog-modal>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import Pagination from "@/Components/Pagination";
import PrimaryButton from "@/Jetstream/PrimaryButton";
import JetDialogModal from "@/Jetstream/DialogModal";
import JetSecondaryButton from "@/Jetstream/SecondaryButton";
import JetDangerButton from "@/Jetstream/DangerButton";
import JetInput from "@/Jetstream/TextInput";
import JetInputError from "@/Jetstream/InputError";
import JetConfirmationModal from "@/Jetstream/ConfirmationModal";
import { reactive, ref, onMounted, onUpdated } from "vue";
import { router } from '@inertiajs/vue3';
import { useForm } from "@inertiajs/vue3";
import Multiselect from "@suadelabs/vue3-multiselect";
export default {
    name: "dnSnReport",
    components: {
        AppLayout,
        Pagination,
        PrimaryButton,
        JetDialogModal,
        JetSecondaryButton,
        JetDangerButton,
        JetConfirmationModal,
        JetInput,
        JetInputError,
        useForm,
        Multiselect,
    },
    props: {
        overall: Object,
        sns: Object,
        dns: Object,
        sns_all: Object,
        errors: Object,
    },

    setup(props) {
        let id = ref(null);
        let showSN = ref(false);
        let editMode = ref(false);
        let search = ref('');
        let res_sn = ref("");
        const searchForm = useForm({
            general: null,
            dn: null,
            sn: null,
            customer_min: null,
            customer_max: null,
        });
        const resetSearch = () => {
            res_sn.value = null;
            searchForm.reset();
            searchPort();
        }
        const form = useForm({
            id: null,
            sn: null,
            dn_id: null,
            name: null,
            description: null,
            location: null,
            input_dbm: null,

        });
        function confirmDelete(data) {
            id.value = data;
        }
        function resetForm() {
            form.id = null;
            form.dn_id = null;
            form.sn = null;
            form.name = null;
            form.description = null;
            form.location = null;
            form.input_dbm = null;
        }
        function editSN(data) {
            form.id = data.id;
            form.dn_id = props.dns.filter((d) => d.id == data.dn_id)[0];
            form.sn = props.sns_all.filter((d) => d.name == data.name)[0];
            form.name = data.name;
            form.description = data.description;
            form.location = data.location;
            form.input_dbm = data.input_dbm;
            showSN.value = true;
            editMode.value = true;
        }

        function cancelSN() {
            showSN.value = false;
            resetForm();
        }

        function searchPort() {
            router.post('/dnSnReport/', searchForm, { preserveState: true })
        }
        function DNSelect(dn) {
            getSN(dn.id).then((d) => {
                console.log(d);
                if (d) {
                    searchForm.sn = null;
                    res_sn.value = d;
                } else {
                    searchForm.sn = null;
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
        return { id, editSN, cancelSN, showSN, form, editMode, searchForm, resetSearch, confirmDelete, searchPort, search, DNSelect, res_sn };
    },
};
</script>

<style>
.multiselect__tags {

    padding: 5px 40px 0 5px !important;

}
</style>