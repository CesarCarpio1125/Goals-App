<template>
  <div class="bg-blue-600 text-white p-4">
    <div class="flex items-center justify-between">
      <h1 class="text-xl font-bold">Goals App</h1>
      <div class="flex items-center space-x-4">
        <span>{{ userName }}</span>
        <Link 
          href="/logout" 
          method="post" 
          as="button"
          class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded"
          @click="handleLogout"
        >
          Sign Out
        </Link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'

const page = usePage()
const userName = computed(() => page.props.auth?.user?.name || 'User')

const handleLogout = (event) => {
  console.log('Logout clicked')
  console.log('Current user:', page.props.auth?.user)
  console.log('CSRF meta tag:', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'))
  console.log('Page props:', page.props)
}
</script>
