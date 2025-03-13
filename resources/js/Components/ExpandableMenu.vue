<template>
  <div class="relative group">
    <button @click="toggleSubmenu" 
      class="w-full flex items-center justify-between p-2 transition-colors text-gray-600 hover:gray-blue-500 text-xs uppercase py-3 font-bold  rounded-md bg-blueGray-100 shadow-sm cursor-pointer focus:border-none "
      :class="[isCollapsed ? 'px-3':'px-4']">
      <div class="flex items-center min-w-max">
        <i class="w-5" :class="icon" ></i>
        <span :class="[isCollapsed ? 'opacity-0 group-hover:opacity-100' : 'opacity-100', 'ml-3']">
          {{ !isCollapsed?label:'' }} 
        </span>
      </div>
      <i v-if="!isCollapsed" :class="['fas transition-transform', isOpen ? 'fa-chevron-down' : 'fa-chevron-right']"></i>
    </button>

    <!-- Submenu for collapsed state -->
    <div v-if="isCollapsed" 
      class="fixed left-20 top-5 min-w-[200px] bg-indigo-50 shadow-lg"
      :class="{ 'hidden': !showSubmenu || activeSubmenu !== name }">
      <div>
        <h6 class="px-2 py-2 text-xs font-bold text-gray-600 uppercase bg-indigo-50">
          {{ label }} 
        </h6>
        <slot></slot>
      </div>
    </div>

    <!-- Regular submenu when not collapsed -->
    <div v-else v-show="isOpen" 
      class="mt-1 space-y-1">
      <slot></slot>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    label: String,
    icon: String,
    isOpen: Boolean,
    isCollapsed: Boolean,
    name: String
  },
  data() {
    return {
      showSubmenu: false
    }
  },
  inject: ['activeSubmenu', 'setActiveSubmenu'],
  methods: {
    toggleSubmenu() {
      if (this.isCollapsed) {
        if (this.showSubmenu && this.activeSubmenu === this.name) {
          this.showSubmenu = false;
          this.setActiveSubmenu(null);
        } else {
          this.showSubmenu = true;
          this.setActiveSubmenu(this.name);
        }
      } else {
        this.$emit('toggle');
      }
    }
  },
  watch: {
    activeSubmenu(newVal) {
      if (newVal !== this.name) {
        this.showSubmenu = false;
      }
    }
  },
  emits: ['toggle']
};
</script>

<style scoped>
h6 {
  transition: background-color 0.3s, color 0.3s;
}
</style>