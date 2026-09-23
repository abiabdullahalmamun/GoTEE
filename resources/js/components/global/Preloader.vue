<template>
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-white bg-opacity-90">
        <div class="text-center">
            <div class="relative w-24 h-24 mx-auto mb-4">
                <div class="absolute inset-0 rounded-full border-4 border-blue-500 opacity-70 animate-ping"></div>
                <svg class="w-full h-full" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M100 0C44.8 0 0 44.8 0 100C0 155.2 44.8 200 100 200C155.2 200 200 155.2 200 100C200 44.8 155.2 0 100 0ZM100 180C55.6 180 20 144.4 20 100C20 55.6 55.6 20 100 20C144.4 20 180 55.6 180 100C180 144.4 144.4 180 100 180Z" fill="#3B82F6"/>
                    <path class="animate-spin origin-center" d="M100 20C144.4 20 180 55.6 180 100C180 107.2 178.8 114.4 176.4 121.2L160.4 115.6C162 110.4 162.8 105.2 162.8 100C162.8 66.8 136 40 102.8 40L100 20Z" fill="#1D4ED8"/>
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <img :src="logo" alt="Logo" class="w-10 h-10 object-contain">
                </div>
            </div>
            <div class="text-blue-600 font-medium text-lg">{{ text }}</div>
            <div class="w-48 h-1 bg-gray-200 rounded-full overflow-hidden mt-4 mx-auto" v-if="showProgress">
                <div class="h-full bg-blue-500 rounded-full animate-progress"></div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'Preloader',
    props: {
        text: {
            type: String,
            default: 'Loading...'
        },
        logo: {
            type: String,
            default: '/resources/assets/images/dashboard.png'
        },
        showProgress: {
            type: Boolean,
            default: true
        }
    }
}
</script>

<style scoped>
.animate-ping {
    animation: ping 1.5s cubic-bezier(0, 0, 0.2, 1) infinite;
}

.animate-spin {
    animation: spin 1.5s linear infinite;
}

.animate-progress {
    animation: progress 2s ease-in-out infinite;
}

@keyframes ping {
    0% {
        transform: scale(0.8);
        opacity: 0;
    }
    70%, 100% {
        transform: scale(1.4);
        opacity: 0;
    }
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

@keyframes progress {
    0% {
        width: 0%;
        transform: translateX(0);
    }
    50% {
        width: 100%;
        transform: translateX(0);
    }
    100% {
        width: 100%;
        transform: translateX(100%);
    }
}
</style>
