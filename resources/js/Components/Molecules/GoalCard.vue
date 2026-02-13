<template>
  <BaseCard :hover="true" class="group cursor-pointer relative overflow-hidden" @click="$emit('select', goal)">
    <!-- Status Badge -->
    <div class="absolute top-3 right-3 z-10">
      <span
        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium shadow-sm transition-all duration-200"
        :class="statusBadgeClasses"
      >
        <div class="w-2 h-2 mr-1" v-html="statusDotIcon" />
        {{ statusText }}
      </span>
    </div>

    <!-- Content -->
    <div class="p-4">
      <div class="flex items-start justify-between mb-3">
        <div class="flex-1 min-w-0">
          <h3 class="text-lg font-semibold text-gray-900 mb-1 truncate group-hover:text-blue-600 transition-colors">
            {{ goal.title }}
          </h3>
          <p v-if="goal.description" class="text-sm text-gray-600 line-clamp-2 leading-relaxed">
            {{ goal.description }}
          </p>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex space-x-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
          <button
            @click.stop="$emit('edit', goal)"
            class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
            title="Edit goal"
          >
            <div class="w-4 h-4" v-html="icons.edit" />
          </button>
          <button
            @click.stop="$emit('delete', goal)"
            class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
            title="Delete goal"
          >
            <div class="w-4 h-4" v-html="icons.delete" />
          </button>
        </div>
      </div>

      <!-- Progress Section -->
      <div class="space-y-3">
        <!-- Progress Bar -->
        <div class="relative">
          <ProgressBar
            :percentage="progressPercentage"
            :current="completedTasks"
            :target="totalTasks"
            :show-details="true"
            :show-label="false"
            unit="tasks"
            size="small"
          />
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-3 gap-3 text-xs">
          <div class="text-center">
            <div class="text-gray-500 mb-1">Tasks</div>
            <div class="font-semibold text-gray-900">{{ totalTasks }}</div>
          </div>
          <div class="text-center">
            <div class="text-gray-500 mb-1">Progress</div>
            <div class="font-semibold" :class="progressTextColor">{{ progressPercentage }}%</div>
          </div>
          <div class="text-center">
            <div class="text-gray-500 mb-1">Status</div>
            <div class="font-semibold" :class="statusTextColor">{{ statusText }}</div>
          </div>
        </div>

        <!-- Deadline -->
        <div v-if="goal.deadline" class="flex items-center text-sm">
          <div class="flex items-center text-gray-500 mr-2">
            <div class="w-4 h-4" v-html="icons.calendar" />
            <span>Deadline</span>
          </div>
          <div class="flex items-center" :class="deadlineTextColor">
            <span class="font-medium">{{ formattedDeadline }}</span>
            <div v-if="isOverdue" class="ml-2 px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium">
              Overdue
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Hover Overlay -->
    <div class="absolute inset-0 bg-gradient-to-r from-blue-600/5 to-purple-600/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none" />
  </BaseCard>
</template>

<script setup>
import { computed } from 'vue'
import BaseCard from '../Atoms/BaseCard.vue'
import ProgressBar from '../Atoms/ProgressBar.vue'
import { useGoalProgress } from '@/composables/useGoalProgress.js'

const props = defineProps({
  goal: {
    type: Object,
    required: true,
  },
})

const emit = defineEmits(['select', 'edit', 'delete'])

// Use the new composable for all progress calculations
const progressData = useGoalProgress(props.goal)

// Enhanced icons with better visual design
const icons = computed(() => ({
  calendar: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>',
  edit: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>',
  delete: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>',
}))

// Enhanced status styling
const statusBadgeClasses = computed(() => {
  const baseClasses = 'inline-flex items-center'
  const colorClasses = {
    completed: 'bg-green-100 text-green-800 border-green-200',
    in_progress: 'bg-blue-100 text-blue-800 border-blue-200',
    pending: 'bg-gray-100 text-gray-800 border-gray-200'
  }
  
  return `${baseClasses} px-2.5 py-1 rounded-full text-xs font-medium shadow-sm border ${colorClasses[progressData.status.value] || colorClasses.pending}`
})

const statusDotIcon = computed(() => {
  const icons = {
    completed: '<svg fill="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2l2 2-4"/></svg>',
    in_progress: '<svg fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/></svg>',
    pending: '<svg fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3" class="opacity-30"/></svg>'
  }
  
  return icons[progressData.status.value] || icons.pending
})

const progressTextColor = computed(() => {
  const colors = {
    completed: 'text-green-600',
    in_progress: 'text-blue-600',
    pending: 'text-gray-600'
  }
  return colors[progressData.status.value] || colors.pending
})

const statusTextColor = computed(() => {
  const colors = {
    completed: 'text-green-700',
    in_progress: 'text-blue-700',
    pending: 'text-gray-700'
  }
  return colors[progressData.status.value] || colors.pending
})

const deadlineTextColor = computed(() => {
  if (progressData.isOverdue.value) {
    return 'text-red-600'
  }
  if (progressData.daysRemaining.value <= 3) {
    return 'text-orange-600'
  }
  return 'text-gray-600'
})

// Simplified computed properties using the composable
const progressPercentage = computed(() => progressData.progressPercentage)
const statusText = computed(() => progressData.statusText.value)
const totalTasks = computed(() => progressData.totalTasks)
const completedTasks = computed(() => progressData.completedTasks)
const isOverdue = computed(() => progressData.isOverdue.value)

const formattedDeadline = computed(() => {
  if (!props.goal.deadline) return ''
  const date = new Date(props.goal.deadline)
  return date.toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
  })
})
</script>
