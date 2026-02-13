<template>
  <button
    :type="type"
    :disabled="disabled || loading"
    :class="buttonClasses"
    @click="handleClick"
  >
    <div
      v-if="loading"
      class="animate-spin w-5 h-5 mr-2"
      v-html="spinnerIcon"
    />
    
    <div
      v-else-if="icon && iconPosition === 'left'"
      class="w-5 h-5 mr-2"
      v-html="iconSvg"
    />
    
    <span :class="textClasses">
      <slot>{{ text }}</slot>
    </span>
    
    <div
      v-if="!loading && icon && iconPosition === 'right'"
      class="w-5 h-5 ml-2"
      v-html="iconSvg"
    />
  </button>
</template>

<script setup>
import { computed } from 'vue'
import { useIcons } from '@/composables/useIcons'
import { useTheme } from '@/composables/useTheme'
import { getButtonClasses, getTextClasses } from '@/utils/buttonUtils'

const props = defineProps({
  type: {
    type: String,
    default: 'button',
    validator: (value) => ['button', 'submit', 'reset'].includes(value),
  },
  text: {
    type: String,
    default: '',
  },
  icon: {
    type: String,
    default: null,
  },
  iconPosition: {
    type: String,
    default: 'left',
    validator: (value) => ['left', 'right'].includes(value),
  },
  color: {
    type: String,
    default: 'blue',
    validator: (value) => ['blue', 'green', 'yellow', 'red', 'purple', 'white'].includes(value),
  },
  variant: {
    type: String,
    default: 'solid',
    validator: (value) => ['solid', 'outline', 'ghost'].includes(value),
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg'].includes(value),
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  loading: {
    type: Boolean,
    default: false,
  },
  fullWidth: {
    type: Boolean,
    default: false,
  },
  fullWidthMobile: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['click'])

const { getIcon } = useIcons()
const { getColorClasses } = useTheme()

const spinnerIcon = computed(() => getIcon('spinner'))
const iconSvg = computed(() => props.icon ? getIcon(props.icon) : null)

// Use utility functions for cleaner code
const buttonClasses = computed(() => 
  getButtonClasses(props, props.loading)
)

const textClasses = computed(() => 
  getTextClasses(props.variant, props.color)
)

const handleClick = () => {
  if (!props.disabled && !props.loading) {
    emit('click')
  }
}
</script>
