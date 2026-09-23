<template>
    <div class="bg-white rounded-lg shadow p-6 hover:shadow-md transition-shadow h-full">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 truncate">{{ title }}</p>
                <p class="mt-1 text-3xl font-semibold text-gray-900">{{ value }}</p>
            </div>
            <div :class="`bg-${color}-100 p-3 rounded-full`">
                <svg class="w-6 h-6" :class="`text-${color}-600`" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path v-if="icon === 'currency-dollar'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    <path v-if="icon === 'package'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    <path v-if="icon === 'exclamation'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    <path v-if="icon === 'user-add'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    <path v-if="icon === 'users'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    <path v-if="icon === 'trending-up'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    <path v-if="icon === 'shopping-cart'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
        </div>
        <div v-if="change !== undefined" class="mt-4 flex items-center">
      <span :class="change >= 0 ? 'text-green-600' : 'text-red-600'" class="flex items-center">
        <svg v-if="change >= 0" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
        </svg>
        <svg v-else class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
        {{ Math.abs(change) }}%
      </span>
            <span class="text-gray-500 text-sm ml-2">vs previous period</span>
        </div>
        <div v-if="url" class="mt-4">
            <a :href="url" class="text-sm font-medium text-blue-600 hover:underline">View details</a>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        title: {
            type: String,
            required: true
        },
        value: {
            type: [String, Number],
            required: true
        },
        icon: {
            type: String,
            required: true,
            validator: (value) => [
                'currency-dollar',
                'package',
                'exclamation',
                'user-add',
                'users',
                'trending-up',
                'shopping-cart'
            ].includes(value)
        },
        color: {
            type: String,
            default: 'blue',
            validator: (value) => ['blue', 'green', 'red', 'purple', 'yellow', 'indigo'].includes(value)
        },
        change: {
            type: Number,
            default: undefined
        },
        url: {
            type: String,
            default: ''
        }
    }
}
</script>
