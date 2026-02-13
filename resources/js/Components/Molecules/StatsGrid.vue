<template>
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <BaseCard hover padding="large">
      <StatCard
        :value="stats.total"
        label="Total Goals"
        :icon="TargetIcon"
        color="blue"
      />
    </BaseCard>

    <BaseCard hover padding="large">
      <StatCard
        :value="stats.completed"
        label="Completed"
        :icon="CheckCircleIcon"
        color="green"
        :trend="completionTrend"
        :trend-value="completionRate"
      />
    </BaseCard>

    <BaseCard hover padding="large">
      <StatCard
        :value="stats.in_progress"
        label="In Progress"
        :icon="ClockIcon"
        color="yellow"
      />
    </BaseCard>

    <BaseCard hover padding="large">
      <StatCard
        :value="completionRate + '%'"
        label="Success Rate"
        :icon="TrendingUpIcon"
        color="purple"
        :trend="successTrend"
        :trend-value="successTrendValue"
      />
    </BaseCard>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import BaseCard from '../Atoms/BaseCard.vue'
import StatCard from '../Atoms/StatCard.vue'

const props = defineProps({
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
  previousStats: {
    type: Object,
    default: null,
  },
})

const completionRate = computed(() => {
  return Math.round(props.stats.completion_rate || 0)
})

const completionTrend = computed(() => {
  if (!props.previousStats) return 'neutral'
  
  const current = props.stats.completed
  const previous = props.previousStats.completed || 0
  
  if (current > previous) return 'up'
  if (current < previous) return 'down'
  return 'neutral'
})

const successTrend = computed(() => {
  if (!props.previousStats) return 'neutral'
  
  const current = props.stats.completion_rate
  const previous = props.previousStats.completion_rate || 0
  
  if (current > previous) return 'up'
  if (current < previous) return 'down'
  return 'neutral'
})

const successTrendValue = computed(() => {
  if (!props.previousStats) return 0
  
  const current = props.stats.completion_rate
  const previous = props.previousStats.completion_rate || 0
  
  return Math.abs(Math.round(current - previous))
})
</script>
