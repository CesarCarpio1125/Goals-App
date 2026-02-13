// Utility functions for button components
export const getButtonClasses = (props, isLoading) => {
  const baseClasses = 'inline-flex items-center justify-center font-medium rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2'
  const sizeClasses = getSizeClasses(props.size)
  const variantClasses = getVariantClasses(props.variant, props.color, isLoading)
  const widthClasses = getWidthClasses(props.fullWidth, props.fullWidthMobile)
  
  return `${baseClasses} ${sizeClasses} ${variantClasses} ${widthClasses}`
}

export const getSizeClasses = (size) => {
  const sizes = {
    sm: 'px-3 py-2 text-sm',
    md: 'px-4 py-3 text-base',
    lg: 'px-6 py-4 text-lg',
  }
  return sizes[size] || sizes.md
}

export const getVariantClasses = (variant, color, isLoading) => {
  if (isLoading) return 'opacity-75 cursor-not-allowed'
  
  const variants = {
    solid: getSolidVariant(color),
    outline: getOutlineVariant(color),
    ghost: getGhostVariant(color),
  }
  
  return variants[variant] || variants.solid
}

export const getSolidVariant = (color) => {
  if (color === 'white') {
    return 'bg-white text-gray-900 hover:bg-gray-100 focus:ring-gray-500'
  }
  return `bg-${color}-600 text-white hover:bg-${color}-700 focus:ring-${color}-500`
}

export const getOutlineVariant = (color) => {
  if (color === 'white') {
    return 'bg-transparent text-white border border-white hover:bg-white hover:text-gray-900 focus:ring-white'
  }
  return `bg-transparent text-${color}-600 border border-${color}-600 hover:bg-${color}-50 focus:ring-${color}-500`
}

export const getGhostVariant = (color) => {
  return `text-${color}-600 hover:bg-${color}-50 focus:ring-${color}-500`
}

export const getWidthClasses = (fullWidth, fullWidthMobile) => {
  if (fullWidth) return 'w-full'
  if (fullWidthMobile) return 'w-full sm:w-auto'
  return ''
}

export const getTextClasses = (variant, color) => {
  if (variant === 'solid' && color === 'white') return 'text-gray-900'
  return ''
}
