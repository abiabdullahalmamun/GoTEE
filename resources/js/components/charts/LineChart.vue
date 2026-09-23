<template>
    <div class="chart-container">
        <canvas ref="chartCanvas"></canvas>
    </div>
</template>

<script>
import { Chart, LineController, LinearScale, PointElement, LineElement, Tooltip, Legend, CategoryScale } from 'chart.js';
import { Line } from 'vue-chartjs';

// Explicitly register the required components
Chart.register(
    LineController,
    LinearScale,
    PointElement,
    LineElement,
    Tooltip,
    Legend,
    CategoryScale
);

export default {
    name: 'LineChart',
    extends: Line,
    props: {
        chartData: {
            type: Object,
            required: true,
            default: () => ({
                labels: [],
                datasets: []
            })
        },
        chartOptions: {
            type: Object,
            default: () => ({
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += '৳' + context.parsed.y.toLocaleString();
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '৳' + value.toLocaleString();
                            }
                        },
                        grid: {
                            drawBorder: false
                        }
                    }
                },
                elements: {
                    point: {
                        radius: 4,
                        hoverRadius: 6,
                        backgroundColor: '#fff',
                        borderWidth: 2
                    }
                }
            })
        }
    },
    mounted() {
        // Add null check before rendering
        if (this.chartData && this.chartData.datasets) {
            this.renderChart(this.chartData, this.chartOptions);
        }
    },
    watch: {
        chartData: {
            handler(newData) {
                // Destroy old chart before rendering new one
                if (this.$data._chart) {
                    this.$data._chart.destroy();
                }
                if (newData && newData.datasets) {
                    this.renderChart(newData, this.chartOptions);
                }
            },
            deep: true
        },
        chartOptions: {
            handler(newOptions) {
                if (this.$data._chart) {
                    this.$data._chart.destroy();
                }
                if (this.chartData && this.chartData.datasets) {
                    this.renderChart(this.chartData, newOptions);
                }
            },
            deep: true
        }
    },
    beforeUnmount() {
        // Clean up chart instance before component is destroyed
        if (this.$data._chart) {
            this.$data._chart.destroy();
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
