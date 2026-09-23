<template>
    <div class="chart-container">
        <canvas ref="chartCanvas"></canvas>
    </div>
</template>

<script>
import { Chart, registerables } from 'chart.js';
import { Bar } from 'vue-chartjs';

Chart.register(...registerables);

export default {
    name: 'HorizontalBarChart',
    extends: Bar,
    props: {
        chartData: {
            type: Object,
            required: true
        },
        chartOptions: {
            type: Object,
            default: () => ({
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.dataset.label}: ${context.raw}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        grid: {
                            display: false
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
