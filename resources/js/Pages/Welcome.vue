<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50">
    <!-- Navigation -->
    <nav class="relative z-10 px-6 py-6">
      <div class="max-w-7xl mx-auto flex items-center justify-between">
        <!-- Logo -->
        <div class="flex items-center space-x-3">
          <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-purple-600 rounded-xl flex items-center justify-center">
            <div class="w-6 h-6 text-white" v-html="getIcon('target')" />
          </div>
          <span class="text-xl font-bold text-gray-900">Goals App</span>
        </div>

        <!-- Auth buttons -->
        <div class="flex items-center space-x-4">
          <Link
            v-if="canLogin"
            :href="route('login')"
            class="text-gray-700 hover:text-blue-600 font-medium transition-colors duration-200"
          >
            Sign In
          </Link>
          <Link
            v-if="canRegister"
            :href="route('register')"
            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200 text-sm"
          >
            Get Started
            <div class="w-4 h-4 ml-2" v-html="getIcon('arrow')" />
          </Link>
        </div>
      </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative px-6 py-20 lg:py-32">
      <div class="max-w-7xl mx-auto">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
          <!-- Content -->
          <div class="text-center lg:text-left">
            <div class="inline-flex items-center px-4 py-2 bg-blue-100 text-blue-700 rounded-full text-sm font-medium mb-6">
              <div class="w-4 h-4 mr-2" v-html="getIcon('rocket')" />
              Transform your dreams into achievements
            </div>

            <h1 class="text-4xl lg:text-6xl font-bold text-gray-900 mb-6">
              Achieve Your
              <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">
                Goals
              </span>
            </h1>

            <p class="text-xl text-gray-600 mb-8 max-w-2xl">
              Track progress, stay motivated, and celebrate every win. The smart way to turn your ambitions into reality.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start items-center">
              <Link
                v-if="canRegister"
                :href="route('register')"
                class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors duration-200"
              >
                Start Free Today
                <div class="w-5 h-5 ml-2" v-html="getIcon('rocket')" />
              </Link>
              <Link
                v-if="canLogin"
                :href="route('login')"
                class="inline-flex items-center px-6 py-3 border border-blue-600 text-blue-600 font-semibold rounded-lg hover:bg-blue-50 transition-colors duration-200"
              >
                <div class="w-5 h-5 mr-2" v-html="getIcon('user')" />
                Sign In
              </Link>
            </div>
          </div>

          <!-- Hero Image/Graphic -->
          <div class="relative">
            <div class="relative z-10">
              <!-- Main graphic -->
              <div class="bg-white rounded-2xl shadow-2xl p-8 border border-gray-100">
                <div class="space-y-4">
                  <!-- Goal cards preview -->
                  <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl p-6 border border-blue-100">
                    <div class="flex items-center justify-between mb-4">
                      <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                          <div class="w-6 h-6 text-white" v-html="getIcon('target')" />
                        </div>
                        <div>
                          <h3 class="font-semibold text-gray-900">Learn Vue 3</h3>
                          <p class="text-sm text-gray-600">Master modern frontend</p>
                        </div>
                      </div>
                      <span class="text-2xl font-bold text-blue-600">85%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                      <div class="bg-gradient-to-r from-blue-600 to-purple-600 h-2 rounded-full" style="width: 85%" />
                    </div>
                  </div>

                  <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-6 border border-green-100">
                    <div class="flex items-center justify-between mb-4">
                      <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-green-600 rounded-lg flex items-center justify-center">
                          <div class="w-6 h-6 text-white" v-html="getIcon('check')" />
                        </div>
                        <div>
                          <h3 class="font-semibold text-gray-900">Fitness Goal</h3>
                          <p class="text-sm text-gray-600">30 days streak</p>
                        </div>
                      </div>
                      <span class="text-2xl font-bold text-green-600">100%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                      <div class="bg-gradient-to-r from-green-600 to-emerald-600 h-2 rounded-full" style="width: 100%" />
                    </div>
                  </div>

                  <div class="bg-gradient-to-r from-yellow-50 to-amber-50 rounded-xl p-6 border border-yellow-100">
                    <div class="flex items-center justify-between mb-4">
                      <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-yellow-600 rounded-lg flex items-center justify-center">
                          <div class="w-6 h-6 text-white" v-html="getIcon('star')" />
                        </div>
                        <div>
                          <h3 class="font-semibold text-gray-900">Read 12 Books</h3>
                          <p class="text-sm text-gray-600">8 completed</p>
                        </div>
                      </div>
                      <span class="text-2xl font-bold text-yellow-600">67%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                      <div class="bg-gradient-to-r from-yellow-600 to-amber-600 h-2 rounded-full" style="width: 67%" />
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Background decorations -->
            <div class="absolute -top-4 -right-4 w-72 h-72 bg-gradient-to-br from-blue-400 to-purple-600 rounded-full opacity-20 blur-3xl" />
            <div class="absolute -bottom-4 -left-4 w-72 h-72 bg-gradient-to-tr from-purple-400 to-pink-600 rounded-full opacity-20 blur-3xl" />
          </div>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section class="py-20 px-6">
      <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16">
          <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">
            Everything You Need to Succeed
          </h2>
          <p class="text-xl text-gray-600 max-w-3xl mx-auto">
            Powerful features designed to keep you motivated and on track with your goals.
          </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
          <!-- Feature 1 -->
          <div class="bg-white rounded-2xl p-8 border border-gray-200 hover:shadow-lg transition-shadow duration-300">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-6">
              <div class="w-6 h-6 text-blue-600" v-html="getIcon('trending')" />
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-3">Progress Tracking</h3>
            <p class="text-gray-600">
              Visualize your journey with beautiful progress bars and analytics that keep you motivated.
            </p>
          </div>

          <!-- Feature 2 -->
          <div class="bg-white rounded-2xl p-8 border border-gray-200 hover:shadow-lg transition-shadow duration-300">
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mb-6">
              <div class="w-6 h-6 text-green-600" v-html="getIcon('fire')" />
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-3">Stay Motivated</h3>
            <p class="text-gray-600">
              Get personalized motivational messages and celebrate every milestone along the way.
            </p>
          </div>

          <!-- Feature 3 -->
          <div class="bg-white rounded-2xl p-8 border border-gray-200 hover:shadow-lg transition-shadow duration-300">
            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mb-6">
              <div class="w-6 h-6 text-purple-600" v-html="getIcon('trophy')" />
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-3">Achievements</h3>
            <p class="text-gray-600">
              Unlock badges and achievements as you crush your goals and build new habits.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 px-6">
      <div class="max-w-4xl mx-auto text-center">
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-3xl p-12 text-white">
          <h2 class="text-3xl lg:text-4xl font-bold mb-4">
            Ready to Transform Your Life?
          </h2>
          <p class="text-xl mb-8 opacity-90">
            Join thousands of users who are already achieving their dreams with Goals App.
          </p>
          <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
            <Link
              v-if="canRegister"
              :href="route('register')"
              class="inline-flex items-center px-6 py-3 bg-white text-blue-600 font-semibold rounded-lg hover:bg-blue-50 transition-colors duration-200"
            >
              Start Your Journey
              <div class="w-5 h-5 ml-2" v-html="getIcon('rocket')" />
            </Link>
            <Link
              :href="route('login')"
              class="inline-flex items-center px-6 py-3 border border-white text-white font-semibold rounded-lg hover:bg-white hover:text-blue-600 transition-colors duration-200"
            >
              Learn More
            </Link>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="py-12 px-6 border-t border-gray-200">
      <div class="max-w-7xl mx-auto text-center">
        <div class="flex items-center justify-center space-x-3 mb-4">
          <div class="w-8 h-8 bg-gradient-to-br from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
            <div class="w-5 h-5 text-white" v-html="getIcon('target')" />
          </div>
          <span class="text-lg font-semibold text-gray-900">Goals App</span>
        </div>
        <p class="text-gray-600">
          © 2026 Goals App. Built with passion for achievers.
        </p>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { useIcons } from '@/composables/useIcons'

defineProps({
  canLogin: {
    type: Boolean,
  },
  canRegister: {
    type: Boolean,
  },
})

const { getIcon } = useIcons()
</script>
