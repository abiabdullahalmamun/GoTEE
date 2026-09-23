<script>
import { Chart as ChartJS, ArcElement, Tooltip, Legend } from 'chart.js'
import { PieChart } from 'vue-chart-3'

ChartJS.register(ArcElement, Tooltip, Legend)

export default {
    name: 'PieChart',
    components: { Pie },
    props: {
        chartData: {
            type: Object,
            required: true
        },
        chartOptions: {
            type: Object,
            default: () => ({
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: (context) => {
                                const label = context.label || ''
                                const value = context.raw || 0
                                const total = context.dataset.data.reduce((a, b) => a + b, 0)
                                const percentage = Math.round((value / total) * 100)
                                return `${label}: ৳${value.toLocaleString()} (${percentage}%)`
                            }
                        }
                    }
                }
            })
        }
    }
}
</script>

<template>
    <div class="chart-container">
        <Pie
            :data="chartData"
            :options="chartOptions"
        />
    </div>
</template>

<style scoped>
.chart-container {
    position: relative;
    height: 100%;
    width: 100%;
    min-height: 200px;
}
</style>
