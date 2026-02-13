<template>
  <div class="flex items-center bg-white/20 backdrop-blur-sm rounded-lg px-3 py-2">
    <div class="w-5 h-5 mr-2" v-html="icon" />
    <span class="font-medium">{{ value }}</span>
  </div>
</template>

<script setup>
defineProps({
  icon: {
    type: String,
    required: true,
  },
  value: {
    type: String,
    required: true,
  },
})
</script>
