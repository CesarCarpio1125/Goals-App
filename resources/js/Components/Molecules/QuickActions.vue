<template>
  <BaseCard class="border-2 border-dashed border-gray-200">
    <div class="text-center">
      <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <button
          @click="$emit('view-goals')"
          class="flex items-center justify-center px-4 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors duration-200 group"
        >
          <div class="w-5 h-5 mr-2" v-html="icons.list" />
          <span class="font-medium">View Goals</span>
        </button>
        
        <button
          @click="$emit('view-progress')"
          class="flex items-center justify-center px-4 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200"
        >
          <div class="w-5 h-5 mr-2" v-html="icons.chart" />
          <span class="font-medium">View Progress</span>
        </button>
        
        <button
          @click="$emit('view-calendar')"
          class="flex items-center justify-center px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200"
        >
          <div class="w-5 h-5 mr-2" v-html="icons.calendar" />
          <span class="font-medium">Calendar</span>
        </button>
        
        <button
          @click="$emit('export-data')"
          class="flex items-center justify-center px-4 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200"
        >
          <div class="w-5 h-5 mr-2" v-html="icons.download" />
          <span class="font-medium">Export</span>
        </button>
      </div>
    </div>
  </BaseCard>
</template>

<script setup>
import { computed } from 'vue'
import BaseCard from '../Atoms/BaseCard.vue'

const icons = computed(() => ({
  plus: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>',
  chart: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>',
  calendar: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>',
  download: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/></svg>',
  list: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>',
}))

const emit = defineEmits(['create-goal', 'view-goals', 'view-progress', 'view-calendar', 'export-data'])
</script>
