<template>
    <div class="chart-container">
        <canvas ref="chartCanvas"></canvas>
    </div>
</template>

<script>
import { Chart, registerables } from 'chart.js';
import { Doughnut } from 'vue-chartjs';

Chart.register(...registerables);

export default {
    name: 'DoughnutChart',
    extends: Doughnut,
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
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ৳${value.toLocaleString()} (${percentage}%)`;
                            }
                        }
                    }
                }
            })
        }
    },
    mounted() {
        this.renderChart(this.chartData, this.chartOptions);
    },
    watch: {
        chartData: {
            handler(newData) {
                this.renderChart(newData, this.chartOptions);
            },
            deep: true
        },
        chartOptions: {
            handler(newOptions) {
                this.renderChart(this.chartData, newOptions);
            },
            deep: true
        }
    }
};
</script>

<style scoped>
.chart-container {
    position: relative;
    height: 100%;
    width: 100%;
    min-height: 250px;
}
</style>
