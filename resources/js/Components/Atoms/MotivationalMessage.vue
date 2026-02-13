<template>
  <div
    class="relative overflow-hidden rounded-xl p-6 transition-all duration-500"
    :class="themeClasses"
  >
    <div class="relative z-10">
      <div class="flex items-start space-x-3">
        <div class="flex-shrink-0">
          <div
            class="w-6 h-6"
            :class="iconColorClass"
            v-html="icon"
          />
        </div>
        <div class="flex-1">
          <h3 class="text-lg font-semibold mb-2" :class="textColorClass">
            {{ title }}
          </h3>
          <p class="text-sm leading-relaxed" :class="subTextColorClass">
            {{ message }}
          </p>
          <div v-if="showProgress" class="mt-4">
            <ProgressBar
              :percentage="progress"
              size="small"
              :show-label="false"
              :show-icon="false"
            />
          </div>
        </div>
      </div>
    </div>
    
    <!-- Background decoration -->
    <div
      class="absolute top-0 right-0 w-32 h-32 opacity-10 transform translate-x-8 -translate-y-8"
    >
      <div
        class="w-full h-full"
        :class="iconColorClass"
        v-html="icon"
      />
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import ProgressBar from './ProgressBar.vue'
import { useIcons } from '@/composables/useIcons'
import { useTheme } from '@/composables/useTheme'

const props = defineProps({
  type: {
    type: String,
    default: 'motivation',
    validator: (value) => ['motivation', 'achievement', 'milestone', 'encouragement'].includes(value),
  },
  title: {
    type: String,
    required: true,
  },
  message: {
    type: String,
    required: true,
  },
  progress: {
    type: Number,
    default: 0,
  },
  showProgress: {
    type: Boolean,
    default: false,
  },
})

const { getIcon } = useIcons()
const { getMotivationTheme } = useTheme()

const theme = computed(() => getMotivationTheme(props.type))
const icon = computed(() => getIcon(props.type))

const themeClasses = computed(() => 
  `bg-gradient-to-br ${theme.value.gradient} border ${theme.value.colors[100]}`
)

const textColorClass = computed(() => theme.value.colors[900])
const subTextColorClass = computed(() => theme.value.colors.text)
const iconColorClass = computed(() => theme.value.colors.text)
</script>
