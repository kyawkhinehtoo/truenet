<template>
  <form @submit.prevent="submit">

    <div class="w-full mx-auto bg-white rounded-lg overflow-hidden">
      <div class="md:flex">
        <div class="w-full">

          <div class="p-3">
            <div class="mb-2 text-left"><span>Name</span>
              <input type="text" v-model="form.name"
                class="h-12 px-3 w-full border-gray-200 border rounded focus:outline-none focus:border-gray-300" />
              <p v-if="$page.props.errors.name" class="mt-2 text-sm text-red-500">{{ $page.props.errors.name }}</p>
            </div>
            <div class="mb-2 text-left">
              <span>Attachments</span>
              <i v-if="form.file" class="fas fa-times-circle text-red-600 mt-1 px-1" @click="form.file = null"></i>
              <div
                class="relative h-40 rounded-lg border-dashed border-2 border-gray-200 bg-white flex justify-center items-center hover:cursor-pointer">
                <div class="absolute">

                  <div v-if="!form.file" class="flex flex-col items-center">

                    <i class="fa fa-cloud-upload fa-3x text-gray-200"></i> <span
                      class="block text-gray-400 font-normal">Attach you files here</span> <span
                      class="block text-gray-400 font-normal">or</span> <span
                      class="block text-blue-400 font-normal">Browse files</span>
                  </div>
                  <div class="flex flex-col items-center" v-if="form.file">

                    <i :class="'fas fa-' + fa" class="text-blue-600 mt-1 px-1 fa-3x"></i>

                    <span class="block text-blue-400 font-normal">{{ form.file.name }}</span>
                  </div>

                </div>
                <input type="file" @input="form.file = $event.target.files[0]" class="h-full w-full opacity-0" />


              </div>

              <div class="flex justify-between items-center text-gray-400">
                <p v-if="$page.props.errors.file" class="mt-2 text-sm text-red-500">{{ $page.props.errors.file }}</p>
                <span>Accepted file type:.pdf, .jpg and .doc only</span> <span class="flex items-center"><i
                    class="fa fa-lock mr-1"></i> secure</span>
              </div>
            </div>
            <progress v-if="form.progress" :value="form.progress.percentage" max="100" class="w-full">{{
              form.progress.percentage }}%</progress>
            <div class="mt-3 text-center pb-3"><button type="submit"
                class="w-full h-12 text-lg  bg-blue-600 rounded text-white hover:bg-blue-700">Upload</button></div>
          </div>
        </div>
      </div>
    </div>

  </form>
</template>

<script>
import { ref, onMounted, reactive, inject, onUpdated } from "vue";
import { useForm } from "@inertiajs/vue3";
import { router } from '@inertiajs/vue3';
export default {
  name: "Upload",
  props: ["data"],
  emits: ['status'],
  setup(props, context) {
    const form = useForm({
      name: null,
      file: null,
      incident_id: (typeof props.data.incident_id !== 'undefined') ? props.data.incident_id : null,
      customer_id: (typeof props.data.customer_id !== 'undefined') ? props.data.customer_id : null,
    });
    const fa = ref("");
    const test = ref(0);
    function clearForm() {
      form.name = '';
      form.file = null;
    }
    function submit() {
      form.post("/uploadData", {
        onSuccess: (page) => {
          clearForm();
          context.emit('status', true);

        },
        onError: (errors) => {
          context.emit('status', false);
          console.log(errors)
        }
      });
    }
    onUpdated(() => {
      if (typeof form.file !== 'undefined' && form.file != null) {
        console.log("hello");
        let file_type = form.file.type;
        if (file_type.includes('pdf')) {
          fa.value = "file-pdf";
        }
        if (file_type.includes('spreadsheet')) {
          fa.value = "file-excel";
        }
        if (file_type.includes('wordprocessing')) {
          fa.value = "file-word";
        }
        if (file_type.includes('image')) {
          fa.value = "file-image";
        }
      }

    });
    return { form, submit, fa };
  },
};
</script>
