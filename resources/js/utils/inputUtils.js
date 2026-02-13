// Utility functions for input components
export const getInputClasses = (props, isFocused, hasIcon) => {
  const baseClasses = 'block w-full rounded-lg border-0 py-3 px-4 text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-offset-0 transition-all duration-200'
  const paddingClasses = shouldShowIcon(props.icon, props.modelValue) ? 'pl-10' : 'pl-4'
  const stateClasses = getStateClasses(props.error, isFocused, props.color)
  
  return `${baseClasses} ${paddingClasses} ${stateClasses}`
}

export const shouldShowIcon = (icon, modelValue) => icon && !modelValue

export const getStateClasses = (error, isFocused, color) => {
  if (error) return 'ring-2 ring-red-500 bg-red-50'
  if (isFocused) return `ring-2 ring-${color}-500 bg-white`
  return 'bg-gray-50 ring-1 ring-gray-200 hover:bg-white'
}

export const getIconColorClass = (error, color) => error ? 'text-red-500' : `text-${color}-500`

export const getLabelColorClass = (error, textClass) => error ? 'text-red-600' : textClass
