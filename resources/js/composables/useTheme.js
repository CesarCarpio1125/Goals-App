import { computed } from 'vue'

// Centralized color system
export const THEME_COLORS = {
  // Primary colors for different states
  blue: {
    50: 'bg-blue-50',
    100: 'border-blue-100',
    600: 'bg-blue-600',
    700: 'bg-blue-700',
    900: 'text-blue-900',
    text: 'text-blue-600',
  },
  green: {
    50: 'bg-green-50',
    100: 'border-green-100',
    600: 'bg-green-600',
    700: 'bg-green-700',
    900: 'text-green-900',
    text: 'text-green-600',
  },
  yellow: {
    50: 'bg-yellow-50',
    100: 'border-yellow-100',
    600: 'bg-yellow-600',
    700: 'bg-yellow-700',
    900: 'text-yellow-900',
    text: 'text-yellow-600',
  },
  red: {
    50: 'bg-red-50',
    100: 'border-red-100',
    600: 'bg-red-600',
    700: 'bg-red-700',
    900: 'text-red-900',
    text: 'text-red-600',
  },
  purple: {
    50: 'bg-purple-50',
    100: 'border-purple-100',
    600: 'bg-purple-600',
    700: 'bg-purple-700',
    900: 'text-purple-900',
    text: 'text-purple-600',
  },
  orange: {
    50: 'bg-orange-50',
    100: 'border-orange-100',
    600: 'bg-orange-600',
    700: 'bg-orange-700',
    900: 'text-orange-900',
    text: 'text-orange-600',
  },
  amber: {
    50: 'bg-amber-50',
    100: 'border-amber-100',
    600: 'bg-amber-600',
    700: 'bg-amber-700',
    900: 'text-amber-900',
    text: 'text-amber-600',
  },
  indigo: {
    50: 'bg-indigo-50',
    100: 'border-indigo-100',
    600: 'bg-indigo-600',
    700: 'bg-indigo-700',
    900: 'text-indigo-900',
    text: 'text-indigo-600',
  },
  pink: {
    50: 'bg-pink-50',
    100: 'border-pink-100',
    600: 'bg-pink-600',
    700: 'bg-pink-700',
    900: 'text-pink-900',
    text: 'text-pink-600',
  },
}

// Progress color mapping based on percentage
export const PROGRESS_COLORS = {
  excellent: { min: 75, color: THEME_COLORS.green },
  good: { min: 50, color: THEME_COLORS.blue },
  average: { min: 25, color: THEME_COLORS.yellow },
  poor: { min: 0, color: THEME_COLORS.red },
}

// Motivational theme configurations
export const MOTIVATION_THEMES = {
  motivation: {
    colors: THEME_COLORS.orange,
    gradient: 'from-orange-50 to-red-50',
  },
  achievement: {
    colors: THEME_COLORS.yellow,
    gradient: 'from-yellow-50 to-amber-50',
  },
  milestone: {
    colors: THEME_COLORS.blue,
    gradient: 'from-blue-50 to-indigo-50',
  },
  encouragement: {
    colors: THEME_COLORS.purple,
    gradient: 'from-purple-50 to-pink-50',
  },
}

export function useTheme() {
  const getProgressColor = (percentage) => {
    if (percentage >= 75) return PROGRESS_COLORS.excellent.color
    if (percentage >= 50) return PROGRESS_COLORS.good.color
    if (percentage >= 25) return PROGRESS_COLORS.average.color
    return PROGRESS_COLORS.poor.color
  }

  const getMotivationTheme = (type) => {
    return MOTIVATION_THEMES[type] || MOTIVATION_THEMES.motivation
  }

  const getColorClasses = (colorName, classes = ['50', '100', 'text']) => {
    const color = THEME_COLORS[colorName]
    return classes.map(cls => color[cls]).filter(Boolean)
  }

  return {
    getProgressColor,
    getMotivationTheme,
    getColorClasses,
    THEME_COLORS,
    PROGRESS_COLORS,
    MOTIVATION_THEMES,
  }
}
