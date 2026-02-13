<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    status: {
        type: Number,
        default: 404
    },
    message: {
        type: String,
        default: ''
    }
});

const errorTitle = computed(() => {
    const titles = {
        403: 'Access Forbidden',
        404: 'Page Not Found',
        500: 'Server Error',
        503: 'Service Unavailable'
    };
    return titles[props.status] || 'Error';
});

const errorMessage = computed(() => {
    const messages = {
        403: "You don't have permission to access this resource.",
        404: "The page you're looking for seems to have wandered off. It might have been moved, deleted, or never existed in the first place.",
        500: "Something went wrong on our end. We're working to fix it.",
        503: "We're currently down for maintenance. Please check back soon."
    };
    return messages[props.status] || 'An error occurred.';
});

const errorEmoji = computed(() => {
    const emojis = {
        403: '🔒',
        404: '🔍',
        500: '⚠️',
        503: '🔧'
    };
    return emojis[props.status] || '❌';
});

const goBack = () => {
    window.history.back();
};
</script>

<template>
    <div
        class="min-h-screen bg-gradient-to-br from-red-50 via-white to-blue-50 flex items-center justify-center px-4 sm:px-6 relative overflow-hidden">
        <!-- Animated Background Elements -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div
                class="absolute top-20 left-10 w-72 h-72 bg-red-200 rounded-full mix-blend-multiply filter blur-xl opacity-30 animate-blob">
            </div>
            <div
                class="absolute top-40 right-10 w-72 h-72 bg-blue-200 rounded-full mix-blend-multiply filter blur-xl opacity-30 animate-blob animation-delay-2000">
            </div>
            <div
                class="absolute -bottom-8 left-20 w-72 h-72 bg-purple-200 rounded-full mix-blend-multiply filter blur-xl opacity-30 animate-blob animation-delay-4000">
            </div>
        </div>

        <!-- Main Content -->
        <div class="relative z-10 max-w-2xl w-full pt-5 px-5">
            <div class="text-center space-y-8">
                <!-- Error Number with Animation -->
                <div class="relative">
                    <h1
                        class="text-[10rem] sm:text-[14rem] flex items-center justify-center font-black text-emerald-400 leading-none select-none animate-pulse-slow">
                        {{ status }}
                    </h1>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="text-center space-y-2">
                            <div class="text-6xl sm:text-6xl animate-bounce-slow">
                                {{ errorEmoji }}
                            </div>
                            <p class="text-2xl sm:text-3xl font-black text-emerald-600 tracking-tight">
                                Oops!
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Message -->
                <div class="space-y-4 px-4">
                    <h2 class="text-3xl sm:text-4xl font-black text-gray-900 tracking-tight">
                        {{ errorTitle }}
                    </h2>
                    <p class="text-base sm:text-lg text-gray-600  mx-auto leading-relaxed">
                        {{ errorMessage }}
                    </p>
                    <p v-if="message" class="text-base sm:text-lg text-gray-600  mx-auto leading-relaxed">
                        {{ message }}
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center pt-4">
                    <Link href="/"
                        class="group relative inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-8 py-4 rounded-2xl font-bold transition-all duration-300 shadow-xl shadow-emerald-200 hover:shadow-2xl hover:shadow-emerald-300 hover:-translate-y-1 w-full sm:w-auto justify-center">
                        <i class="pi pi-home text-lg"></i>
                        <span>Back to Home</span>
                        <div
                            class="absolute inset-0 rounded-2xl bg-white opacity-0 group-hover:opacity-20 transition-opacity">
                        </div>
                    </Link>

                    <button @click="goBack"
                        class="group relative inline-flex items-center gap-2 bg-white hover:bg-gray-50 text-gray-700 px-8 py-4 rounded-2xl font-bold transition-all duration-300 shadow-lg hover:shadow-xl border-2 border-gray-200 hover:border-emerald-200 w-full sm:w-auto justify-center">
                        <i class="pi pi-arrow-left text-lg"></i>
                        <span>Go Back</span>
                    </button>
                </div>

                <!-- Helpful Links -->
                <div class="pt-8 border-t border-gray-200 mt-12">
                    <p class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">
                        Quick Links
                    </p>
                    <div class="flex flex-wrap gap-4 justify-center">
                        <Link href="/"
                            class="text-emerald-600 hover:text-emerald-700 font-semibold text-sm hover:underline transition-colors">
                            Home
                        </Link>
                        <Link href="/products"
                            class="text-emerald-600 hover:text-emerald-700 font-semibold text-sm hover:underline transition-colors">
                            Products
                        </Link>                       
                    </div>
                </div>
``
                <!-- Error Code -->
                <div class="pt-8">
                    <p class="text-xs text-gray-400 font-mono">
                        ERROR_CODE: {{ status }}_{{ errorTitle.toUpperCase().replace(/ /g, '_') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes blob {

    0%,
    100% {
        transform: translate(0, 0) scale(1);
    }

    33% {
        transform: translate(30px, -50px) scale(1.1);
    }

    66% {
        transform: translate(-20px, 20px) scale(0.9);
    }
}

@keyframes pulse-slow {

    0%,
    100% {
        opacity: 0.15;
    }

    50% {
        opacity: 0.25;
    }
}

@keyframes bounce-slow {

    0%,
    100% {
        transform: translateY(0);
    }

    50% {
        transform: translateY(-20px);
    }
}

.animate-blob {
    animation: blob 7s infinite;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}

.animate-pulse-slow {
    animation: pulse-slow 3s ease-in-out infinite;
}

.animate-bounce-slow {
    animation: bounce-slow 3s ease-in-out infinite;
}
</style>