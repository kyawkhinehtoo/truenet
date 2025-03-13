<template>
  <div class="mt-2">
    <div>
      <h1 class="mb-4 text-center font-black text-gray-700">STEPS {{ step }}</h1>
      <div class="flex">
        <div class="w-1/3 text-center px-6">
          <div class="bg-gray-300 rounded-lg flex items-center justify-center border border-gray-200"
            :class="{ 'transition-colors duration-600 ease-in-out bg-yellow-300 border-yellow-200': step >= 1 }">
            <div class="w-1/3 bg-transparent h-20 flex items-center justify-center icon-step">
              <i class="fas fa-cogs fa-2x flex"></i>
            </div>

            <div class="w-2/3 bg-gray-200 h-24 flex flex-col items-center justify-center px-1 rounded-r-lg body-step"
              :class="{ 'transition-colors duration-600 ease-in-out bg-yellow-200 transform': step >= 1 }">
              <h2 class="font-bold text-sm">Billing Info</h2>
              <p class="text-xs text-gray-600">
                Pleae Enter Billing Metadata
              </p>
            </div>
          </div>
        </div>
        <div class="flex-1 flex items-center justify-center">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
            <path d="M14 2h-7.229l7.014 7h-13.785v6h13.785l-7.014 7h7.229l10-10z" />
          </svg>
        </div>
        <div class="w-1/3 text-center px-6 ">
          <div class="bg-gray-300 rounded-lg flex items-center justify-center border border-gray-200"
            :class="{ 'transition-colors duration-600 ease-in-out bg-green-300 border-green-200': step >= 2 }">
            <div class="w-1/3 bg-transparent h-20 flex items-center justify-center icon-step">
              <i class="fas fa-cogs fa-2x flex"></i>
            </div>
            <div class="w-2/3 bg-gray-200 h-24 flex flex-col items-center justify-center px-1 rounded-r-lg body-step"
              :class="{ 'transition-colors duration-600 ease-in-out bg-green-200 transform': step >= 2 }">
              <h2 class="font-bold text-sm">Preview the Data</h2>
              <p class="text-xs text-gray-600">
                Data confirmation !
              </p>
            </div>
          </div>
        </div>
        <div class="flex-1 flex items-center justify-center">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
            <path d="M14 2h-7.229l7.014 7h-13.785v6h13.785l-7.014 7h7.229l10-10z" />
          </svg>
        </div>
        <div class="w-1/3 text-center px-6">
          <div class="bg-gray-300 rounded-lg flex items-center justify-center border border-gray-200"
            :class="{ 'transition-colors duration-600 ease-in-out bg-blue-300 border-blue-200': step >= 3 }">
            <div class="w-1/3 bg-transparent h-20 flex items-center justify-center icon-step">
              <i class="fas fa-cogs fa-2x flex"></i>
            </div>
            <div class="w-2/3 bg-gray-200 h-24 flex flex-col items-center justify-center px-1 rounded-r-lg body-step"
              :class="{ 'transition-colors duration-600 ease-in-out bg-blue-200 transform': step >= 3 }">
              <h2 class="font-bold text-sm">Success</h2>
              <p class="text-xs text-gray-600">
                Bill Generation Complete !
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ["steps", "step"]
};
</script>
