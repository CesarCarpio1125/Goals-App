<template>
  <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl p-8 text-white">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
      <div class="mb-6 lg:mb-0">
        <h1 class="text-3xl font-bold mb-2">
          Welcome back, {{ userName }}!
        </h1>
        <p class="text-blue-100 text-lg">
          {{ greetingMessage }}
        </p>
        <div class="mt-4 flex flex-wrap gap-3">
          <StatBadge
            :icon="getIcon('fire')"
            :value="`${streak} day streak`"
          />
          <StatBadge
            :icon="getIcon('trophy')"
            :value="`${achievementsCount} achievements`"
          />
        </div>
      </div>
      
      <div class="flex flex-col items-center lg:items-end space-y-3">
        <MotivationalMessage
          :type="motivationType"
          :title="motivationTitle"
          :message="motivationMessage"
          :progress="overallProgress"
          :show-progress="true"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import MotivationalMessage from '../Atoms/MotivationalMessage.vue'
import StatBadge from '../Atoms/StatBadge.vue'
import { useIcons } from '@/composables/useIcons'
import { 
  getMotivationalTitle, 
  getMotivationalMessage, 
  getGreeting 
} from '@/utils/progressUtils'

const props = defineProps({
  userName: {
    type: String,
    required: true,
  },
  stats: {
    type: Object,
    required: true,
    default: () => ({
      total: 0,
      completed: 0,
      in_progress: 0,
      completion_rate: 0,
    }),
  },
  streak: {
    type: Number,
    default: 0,
  },
  achievementsCount: {
    type: Number,
    default: 0,
  },
})

const { getIcon } = useIcons()

const greetingMessage = computed(() => getGreeting())
const overallProgress = computed(() => Math.round(props.stats.completion_rate || 0))

const motivationType = computed(() => {
  if (overallProgress.value >= 75) return 'achievement'
  if (overallProgress.value >= 50) return 'milestone'
  if (overallProgress.value >= 25) return 'motivation'
  return 'encouragement'
})

const motivationTitle = computed(() => getMotivationalTitle(overallProgress.value))
const motivationMessage = computed(() => getMotivationalMessage(props.stats, overallProgress.value))
</script>
