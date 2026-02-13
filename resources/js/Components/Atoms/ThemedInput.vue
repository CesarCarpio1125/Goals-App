<template>
  <div class="relative">
    <label
      :for="id"
      class="block text-sm font-medium mb-2 transition-colors duration-200"
      :class="labelColorClass"
    >
      {{ label }}
    </label>
    
    <div class="relative">
      <div
        v-if="icon && !modelValue"
        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
        :class="modelValue ? 'opacity-0' : 'opacity-100'"
      >
        <div class="w-5 h-5 transition-all duration-200" :class="iconColorClass">
          <div v-html="icon" />
        </div>
      </div>
      
      <input
        :id="id"
        :type="type"
        :value="modelValue"
        :placeholder="placeholder"
        :required="required"
        :disabled="disabled"
        :autocomplete="autocomplete"
        :class="inputClasses"
        @input="handleInput"
        @focus="handleFocus"
        @blur="handleBlur"
      />
      
      <div
        v-if="showPasswordToggle && type === 'password'"
        class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer"
        @click="togglePassword"
      >
        <div class="w-5 h-5" :class="iconColorClass" v-html="passwordToggleIcon" />
      </div>
    </div>
    
    <div
      v-if="error"
      class="mt-2 text-sm transition-colors duration-200"
      :class="errorColorClass"
    >
      {{ error }}
    </div>
    
    <div
      v-if="hint && !error"
      class="mt-2 text-xs transition-colors duration-200"
      :class="hintColorClass"
    >
      {{ hint }}
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useIcons } from '@/composables/useIcons'
import { useTheme } from '@/composables/useTheme'
import { getInputClasses, shouldShowIcon, getIconColorClass, getLabelColorClass } from '@/utils/inputUtils'

const props = defineProps({
  id: {
    type: String,
    required: true,
  },
  modelValue: {
    type: String,
    default: '',
  },
  label: {
    type: String,
    required: true,
  },
  type: {
    type: String,
    default: 'text',
    validator: (value) => ['text', 'email', 'password', 'tel'].includes(value),
  },
  placeholder: {
    type: String,
    default: '',
  },
  icon: {
    type: String,
    default: null,
  },
  error: {
    type: String,
    default: '',
  },
  hint: {
    type: String,
    default: '',
  },
  required: {
    type: Boolean,
    default: false,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  autocomplete: {
    type: String,
    default: '',
  },
  color: {
    type: String,
    default: 'blue',
    validator: (value) => ['blue', 'green', 'yellow', 'red', 'purple'].includes(value),
  },
  showPasswordToggle: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['update:modelValue', 'focus', 'blur'])

const { getIcon } = useIcons()
const { getColorClasses } = useTheme()

const isFocused = ref(false)
const showPassword = ref(false)

const inputClasses = computed(() => 
  getInputClasses(props, isFocused.value, shouldShowIcon(props.icon, props.modelValue))
)

const iconColorClass = computed(() => 
  getIconColorClass(props.error, props.color)
)

const labelColorClass = computed(() => {
  const textClass = getColorClasses(props.color, ['text'])[1]
  return getLabelColorClass(props.error, textClass)
})

const errorColorClass = computed(() => 'text-red-600')
const hintColorClass = computed(() => 'text-gray-500')

const passwordToggleIcon = computed(() => 
  showPassword.value ? getIcon('eye-off') : getIcon('eye')
)

const handleInput = (event) => {
  emit('update:modelValue', event.target.value)
}

const handleFocus = () => {
  isFocused.value = true
  emit('focus')
}

const handleBlur = () => {
  isFocused.value = false
  emit('blur')
}

const togglePassword = () => {
  showPassword.value = !showPassword.value
  const input = document.getElementById(props.id)
  if (input) {
    input.type = showPassword.value ? 'text' : 'password'
  }
}
</script>
