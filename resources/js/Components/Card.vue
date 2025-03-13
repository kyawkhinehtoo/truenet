<template>
    <div
      class="flex justify-between items-center p-2 bg-white rounded-lg shadow-md dark:bg-gray-800 min-w-0 cursor-pointer hover:bg-gray-50"
      @click="onClick"
    >
      <div class="flex items-center">
        <div
          class="rounded-full h-10 w-10 flex items-center justify-center p-2"
          :class="`text-${bgColor}-500 bg-${bgColor}-100 dark:text-${bgColor}-100 dark:bg-${bgColor}-500`"
        >
          <i :class="`fa ${icon}`"></i>
        </div>
        <p class="text-xs m-2 font-medium text-gray-600 dark:text-gray-400">{{ label }}</p>
      </div>
      <div class="flex items-center">
        <label
          class="px-2.5 py-0.5 rounded text-xs font-medium"
          :class="`bg-${bgColor}-100 text-${bgColor}-800 dark:bg-${bgColor}-900 dark:text-${bgColor}-300`"
        >
          {{ count }}
        </label>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    name: "Card",
    props: {
      icon: {
        type: String,
        required: true,
      },
      bgColor: {
        type: String,
        default: "blue", // Default color if not provided
      },
      label: {
        type: String,
        required: true,
      },
      count: {
        type: Number,
        default: 0,
      },
    },
    emits: ["click"],
    methods: {
      onClick() {
        this.$emit("click");
      },
    },
  };
  </script>
  
  <style scoped>
  /* Add any necessary scoped styles here if needed */
  </style>