<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Calendar from '@/Components/Organisms/Calendar.vue'

const page = usePage()

// Get goals from page props
const goals = computed(() => page.props.goals || [])

// Page metadata
const pageTitle = computed(() => 'Calendar - Goals & Deadlines')

// Initialize with today's date
const initialDate = computed(() => new Date())

// SEO and meta
const pageDescription = computed(() => 
  'View your goals, deadlines, and progress in a calendar format. Stay organized and never miss important dates.'
)
</script>

<template>
  <Head :title="pageTitle">
    <meta name="description" :content="pageDescription" />
  </Head>

  <AuthenticatedLayout>
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 py-8">
      <!-- Page Header -->
      <div class="mb-8">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Calendar</h1>
            <p class="mt-2 text-gray-600">
              Track your goals, deadlines, and progress in one place
            </p>
          </div>
          
          <div class="flex items-center space-x-3">
            <Link
              :href="route('dashboard')"
              class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
              </svg>
              Dashboard
            </Link>
            
            <Link
              :href="route('goals.index')"
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition-colors"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              New Goal
            </Link>
          </div>
        </div>
      </div>

      <!-- Calendar Component -->
      <Calendar 
        :goals="goals"
        :initial-date="initialDate"
      />

      <!-- Empty State -->
      <div v-if="goals.length === 0" class="mt-12 text-center">
        <div class="bg-gray-100 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
          <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No goals to display</h3>
        <p class="text-gray-500 mb-6">Create your first goal to see it in the calendar.</p>
        <Link
          :href="route('goals.create')"
          class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition-colors"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          Create Your First Goal
        </Link>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
