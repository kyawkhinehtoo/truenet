<template>
    <div id="toast-simple"
        class="fixed top-5 right-5 flex items-center w-full max-w-xs p-4 space-x-4 rtl:space-x-reverse text-gray-500 bg-white divide-x rtl:divide-x-reverse divide-gray-100 rounded-lg shadow dark:text-gray-400 dark:divide-gray-700 space-x dark:bg-gray-800 z-20"
        role="alert">

        <i class="fa fas fa-3x fa-location-arrow text-lightBlue-400 " aria-hidden="true"></i>

        <div class="relative w-full p-2">

            <h4 class="text-xs font-extrabold">OSS BSS System</h4>
            <label class="text-xs" v-if="progress != 100">Sending SMS in {{ progress }}% </label>
            <label class="text-xs" v-else>Sending SMS Completed ! </label>
            <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-lightBlue-200 ">
                <!-- :class="[progress < 90 ? 'bg-lightBlue-200' : 'bg-green-500']" -->
                <div :style="`width: ${progress}%`"
                    class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-lightBlue-500  ">
                </div>

            </div>
            <button type="button"
                class="absolute top-0 right-3 ms-auto -mx-1.5 -my-1.5 bg-white justify-center items-center flex-shrink-0 text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex h-8 w-8 dark:text-gray-500 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700"
                data-dismiss-target="#toast-message-cta" aria-label="Close" @click="dismiss()">
                <span class="sr-only">Close</span>
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                </svg>
            </button>

        </div>
    </div>
</template>
  
<script>
import { ref, watch } from "vue";
export default {
    name: 'SmsGenerationProgress',
    emits: ["dismiss"],
    props: {
        progress: Number,
    },
    setup(props, context) {

        function dismiss() {
            context.emit("dismiss", true);
        }
        return { dismiss };
    }
};
</script>