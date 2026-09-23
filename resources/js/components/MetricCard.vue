<template>
    <div
        class="bg-white rounded-lg shadow p-6 border-l-4 cursor-pointer hover:shadow-md transition-shadow"
        :class="`border-${color}-500`"
        @click="handleClick"
    >
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 font-medium">{{ title }}</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ value }}</h3>
            </div>
            <div class="p-3 rounded-full" :class="`bg-${color}-100`">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" :class="`text-${color}-500`" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path v-if="icon === 'calendar'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    <path v-if="icon === 'users'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    <path v-if="icon === 'currency'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    <path v-if="icon === 'alert'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
        </div>
        <div v-if="change !== undefined" class="mt-4">
      <span class="text-sm font-medium" :class="change >= 0 ? 'text-green-500' : 'text-red-500'">
        {{ change >= 0 ? '↑' : '↓' }} {{ Math.abs(change) }}% from last month
      </span>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        title: String,
        value: [String, Number],
        icon: String,
        color: String,
        change: Number,
        url: String
    },
    methods: {
        handleClick() {
            if (this.url) {
                window.location.href = this.url;
            }
        }
    }
};
</script>
