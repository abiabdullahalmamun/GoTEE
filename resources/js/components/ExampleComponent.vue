<template>
    <div class="counter-container">
        <button @click="decrement" :disabled="count <= minValue" class="btn">-</button>
        <span class="count">{{ count }}</span>
        <button @click="increment" :disabled="count >= maxValue" class="btn">+</button>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const count = ref(1); // Default quantity
const minValue = 1; // Minimum quantity
const maxValue = 10; // Maximum quantity

const increment = () => {
    if (count.value < maxValue) {
        count.value++;
    }
};

const decrement = () => {
    if (count.value > minValue) {
        count.value--;
    }
};
</script>

<style scoped>
.counter-container {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 1.2rem;
}

.btn {
    padding: 5px 15px;
    font-size: 1rem;
    cursor: pointer;
    border: none;
    background: #007bff;
    color: white;
    border-radius: 5px;
}

.btn:disabled {
    background: #ccc;
    cursor: not-allowed;
}

.count {
    min-width: 30px;
    text-align: center;
    font-weight: bold;
}
</style>
