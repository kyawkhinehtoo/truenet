<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">System Setting</h2>
    </template>

    <div class="py-2">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <form @submit.prevent="submit">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
              <div class="">

                <!-- Application Name -->
                <div class="mb-4">
                  <label for="application_name" class="block text-gray-700 text-sm font-bold mb-2">
                    Application Name:
                  </label>
                  <input type="text" id="application_name" placeholder="Enter Application Name" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full 
                           shadow-sm sm:text-sm border-gray-300 rounded-md" v-model="form.application_name" />
                  <div v-if="$page.props.errors.application_name" class="text-red-500">
                    {{ $page.props.errors.application_name }}
                  </div>
                </div>
                <!-- Theme Color -->
                <div class="mb-4 grid md:grid-cols-2 md:gap-2">
                 
                  <div>
                    <label for="theme_color" class="block text-gray-700 text-sm font-bold mb-2">
                      Custom Theme Color:
                    </label>
                    <span
                    class="z-10 leading-snug font-normal text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                    <ColorPicker format="hex" shape="circle" v-model:pureColor="form.theme_color" />
                  </span>
                    <input type="text" id="theme_color" placeholder="Choose Theme Color" class="pl-10 mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full 
                             shadow-sm sm:text-sm border-gray-300 rounded-md" v-model="form.theme_color" />
                    <div v-if="$page.props.errors.theme_color" class="text-red-500">
                      {{ $page.props.errors.theme_color }}
                    </div>
                  </div>
                  <div>
                    <label for="accent_color" class="block text-gray-700 text-sm font-bold mb-2">
                     Custom Accent Color:
                    </label>
                    <span
                    class="z-10 leading-snug font-normal text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                    <ColorPicker format="hex" shape="circle" v-model:pureColor="form.accent_color" />
                  </span>
                    <input type="text" id="accent_color" placeholder="Choose Accent Color" class="pl-10 mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full 
                             shadow-sm sm:text-sm border-gray-300 rounded-md" v-model="form.accent_color" />
                    <div v-if="$page.props.errors.accent_color" class="text-red-500">
                      {{ $page.props.errors.accent_color }}
                    </div>
                  </div>
                </div>

                <!-- Logos -->
                <div class="mb-4 grid md:grid-cols-2 gap-2">

                  <!-- Small Logo -->
                  <div>
                    <label for="logo_small" class="block text-gray-700 text-sm font-bold mb-2">
                      Square Logo: (Main Logo)
                    </label>

                    <div class="mb-2 text-left">
                      <span>Attachments</span>
                      <i v-if="form.logo_small && typeof form.logo_small !== 'string'"
                        class="fas fa-times-circle text-red-600 mt-1 px-1 hover:cursor-pointer"
                        @click="removeSmallLogo"></i>
                      <div class="relative h-40 rounded-lg border-dashed border-2 border-gray-200 
                               bg-white flex justify-center items-center hover:cursor-pointer">
                        <div class="absolute">
                          <!-- 1) Show newly selected file preview if we have one -->
                          <div v-if="previewLogoSmall">
                            <img :src="previewLogoSmall" alt="Small Logo Preview" class="object-contain h-36 w-auto" />
                          </div>

                          <!-- 2) Else if there's a stored string path from DB and no new file preview -->
                          <div v-else-if="form.logo_small && typeof form.logo_small === 'string'">
                            <img :src="`/storage/${form.logo_small}`" alt="Small Logo from DB"
                              class="object-contain h-36 w-auto" />
                          </div>

                          <!-- 3) Otherwise, show placeholder -->
                          <div v-else class="flex flex-col items-center">
                            <i class="fa fa-cloud-upload fa-3x text-gray-200"></i>
                            <span class="block text-gray-400 font-normal">Attach your logo here</span>
                            <span class="block text-gray-400 font-normal">or</span>
                            <span class="block text-blue-400 font-normal">Browse files</span>
                          </div>
                        </div>
                        <input id="logo_small" type="file" class="h-full w-full opacity-0" accept=".png"
                          @change="handleSmallLogoChange" />
                      </div>

                      <div class="flex justify-between items-center text-gray-400">
                        <p v-if="$page.props.errors.logo_small" class="mt-2 text-sm text-red-500">
                          {{ $page.props.errors.logo_small }}
                        </p>
                        <span>Accepted file type: .png only</span>
                        <span class="flex items-center">
                          <i class="fa fa-lock mr-1"></i> secure
                        </span>
                      </div>
                    </div>
                  </div>

                  <!-- Large Logo -->
                  <div>
                    <label for="logo_large" class="block text-gray-700 text-sm font-bold mb-2">
                      Long Logo:
                    </label>

                    <div class="mb-2 text-left">
                      <span>Attachments</span>
                      <i v-if="form.logo_large && typeof form.logo_large !== 'string'"
                        class="fas fa-times-circle text-red-600 mt-1 px-1 hover:cursor-pointer"
                        @click="removeLargeLogo"></i>
                      <div class="relative h-40 rounded-lg border-dashed border-2 border-gray-200 
                               bg-white flex justify-center items-center hover:cursor-pointer">
                        <div class="absolute">
                          <!-- 1) Show newly selected file preview if we have one -->
                          <div v-if="previewLogoLarge">
                            <img :src="previewLogoLarge" alt="Large Logo Preview" class="object-contain h-36 w-auto" />
                          </div>

                          <!-- 2) Else if there's a stored string path from DB and no new file preview -->
                          <div v-else-if="form.logo_large && typeof form.logo_large === 'string'">
                            <img :src="`/storage/${form.logo_large}`" alt="Large Logo from DB"
                              class="object-contain h-36 w-auto" />
                          </div>

                          <!-- 3) Otherwise, show placeholder -->
                          <div v-else class="flex flex-col items-center">
                            <i class="fa fa-cloud-upload fa-3x text-gray-200"></i>
                            <span class="block text-gray-400 font-normal">Attach your logo here</span>
                            <span class="block text-gray-400 font-normal">or</span>
                            <span class="block text-blue-400 font-normal">Browse files</span>
                          </div>
                        </div>
                        <input id="logo_large" type="file" class="h-full w-full opacity-0" accept=".png"
                          @change="handleLargeLogoChange" />
                      </div>

                      <div class="flex justify-between items-center text-gray-400">
                        <p v-if="$page.props.errors.logo_large" class="mt-2 text-sm text-red-500">
                          {{ $page.props.errors.logo_large }}
                        </p>
                        <span>Accepted file type: .png only</span>
                        <span class="flex items-center">
                          <i class="fa fa-lock mr-1"></i> secure
                        </span>
                      </div>
                    </div>
                  </div>
                </div>

              </div>
            </div>

            <!-- Buttons -->
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
              <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                <button wire:click.prevent="submit" type="submit" v-show="!editMode" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent
                         rounded-md font-semibold text-xs text-white uppercase tracking-widest
                         hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900
                         focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                  Save
                </button>
              </span>

              <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                <button wire:click.prevent="submit" type="submit" v-show="editMode" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent
                         rounded-md font-semibold text-xs text-white uppercase tracking-widest
                         hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900
                         focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                  Update
                </button>
              </span>

              <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
                <button @click="closeModal" type="button" class="inline-flex justify-center w-full rounded-md border border-gray-300
                         px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm
                         hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue
                         transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                  Cancel
                </button>
              </span>
            </div>
          </form>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";

import { reactive, ref, onMounted } from "vue";
import { router } from '@inertiajs/vue3';
import { ColorPicker } from "vue3-colorpicker";
import "vue3-colorpicker/style.css";
export default {
  name: "SystemSetting",
  components: {
    AppLayout,
    ColorPicker,
  },
  props: {
    // "setting" is an array or object you pass from the server containing saved data
    setting: {
      type: Array,
      default: () => [],
    },
    errors: {
      type: Object,
      default: () => ({}),
    },
  },
  setup(props) {
    const editMode = ref(false);

    // Our form object
    const form = reactive({
      id: null,
      application_name: null,
      theme_color: null,
      accent_color: null,
      // These can be either:
      // 1) A string from DB (e.g. 'logos/small.png')
      // 2) A File object (when user chooses a new file)
      logo_small: null,
      logo_large: null,
      _method: "POST",
    });

    // Local preview URLs for newly selected files
    const previewLogoSmall = ref(null);
    const previewLogoLarge = ref(null);

    /**
     * Handle file selection for small logo
     */
    function handleSmallLogoChange(e) {
      const file = e.target.files[0] || null;
      if (file) {
        form.logo_small = file;
        previewLogoSmall.value = URL.createObjectURL(file);
      } else {
        form.logo_small = null;
        previewLogoSmall.value = null;
      }
    }

    /**
     * Handle file selection for large logo
     */
    function handleLargeLogoChange(e) {
      const file = e.target.files[0] || null;
      if (file) {
        form.logo_large = file;
        previewLogoLarge.value = URL.createObjectURL(file);
      } else {
        form.logo_large = null;
        previewLogoLarge.value = null;
      }
    }

    /**
     * Remove the currently selected file (small logo)
     */
    function removeSmallLogo() {
      form.logo_small = null;
      previewLogoSmall.value = null;
    }

    /**
     * Remove the currently selected file (large logo)
     */
    function removeLargeLogo() {
      form.logo_large = null;
      previewLogoLarge.value = null;
    }

    /**
     * Submit form
     */
    function submit() {
      if (!editMode.value) {
        // Creating a new record
        form._method = "POST";
        router.post("/setting", form, {
          preserveState: true,
          onSuccess: (page) => {
            Toast.fire({
              icon: "success",
              title: page.props.flash.message,
            });

          },
          onError: (errors) => {
            console.error("Create error:", errors);
          },
        });
      } else {
        // Updating an existing record
        form._method = "PUT";
        router.post("/setting/" + form.id, form, {
          onSuccess: (page) => {
            Toast.fire({
              icon: "success",
              title: page.props.flash.message,
            });
          },
          onError: (errors) => {
            console.error("Update error:", errors);
          },
        });
      }
    }

    /**
     * Populate form for editing
     */
    function edit(data) {
      editMode.value = true;
      form.id = data.id;
      form.application_name = data.application_name;
      form.theme_color = data.theme_color;
      form.accent_color = data.accent_color;
      form.logo_small = data.logo_small; // e.g. "logos/small.png" from DB
      form.logo_large = data.logo_large; // e.g. "logos/large.png"

      // If these are strings (paths), no local preview is needed initially
      previewLogoSmall.value = null;
      previewLogoLarge.value = null;
    }

    /**
     * Called on mount
     */
    onMounted(() => {
      if (props.setting.length > 0) {
        // Example: if your "setting" is an array with 1 record
        // You can load it into form automatically
        edit(props.setting[0]);
      }
    });

    /**
     * Optionally close the modal or navigate away
     */
    function closeModal() {
      // Example: Reset everything or navigate away
      // For now, just clear the form
      editMode.value = false;
      form.id = null;
      form.application_name = null;
      form.logo_small = null;
      form.logo_large = null;
      previewLogoSmall.value = null;
      previewLogoLarge.value = null;
    }

    return {
      form,
      editMode,
      previewLogoSmall,
      previewLogoLarge,

      handleSmallLogoChange,
      handleLargeLogoChange,
      removeSmallLogo,
      removeLargeLogo,

      submit,
      closeModal,
    };
  },
};
</script>