<template>
    <div class="container mx-auto px-4 py-6">
        <!-- Welcome Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Dashboard Overview</h1>
                <p class="text-gray-600">Welcome back, Admin! Here's what's happening with your business today.</p>
            </div>
            <div class="mt-4 md:mt-0">
                <span class="text-sm text-gray-500">Last updated: {{ lastUpdated }}</span>
            </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="flex justify-center items-center h-64">
            <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
        </div>

        <!-- Content -->
        <div v-else>
            <!-- Key Metrics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Bookings -->
                <MetricCard
                    title="Total Bookings"
                    :value="metrics.total_bookings"
                    icon="calendar"
                    color="blue"
                    :change="metrics.booking_change"
                    url="/admin/room-booking"
                />

                <!-- Total Members -->
                <MetricCard
                    title="Total Members"
                    :value="metrics.total_members"
                    icon="users"
                    color="green"
                    :change="metrics.member_change"
                    url="/members"
                />

                <!-- Total Revenue -->
                <MetricCard
                    title="Total Revenue"
                    :value="'৳ ' + metrics.total_revenue.toLocaleString()"
                    icon="currency"
                    color="purple"
                    :change="metrics.revenue_change"
                    url="#"
                />

                <!-- Pending Approvals -->
                <MetricCard
                    title="Pending Approvals"
                    :value="metrics.pending_approvals"
                    icon="alert"
                    color="yellow"
                    url="/admin/room-booking"
                />
            </div>

            <!-- Charts and Detailed Metrics -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Revenue Chart -->
                <div class="bg-white rounded-lg shadow p-6 lg:col-span-2">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold text-gray-800">Monthly Revenue</h2>
                        <div class="flex space-x-2">
                            <button
                                @click="loadRevenueData('year')"
                                :class="{'bg-blue-100 text-blue-600': revenuePeriod === 'year', 'bg-gray-100 text-gray-600': revenuePeriod !== 'year'}"
                                class="px-3 py-1 text-sm rounded-md"
                            >
                                This Year
                            </button>
                            <button
                                @click="loadRevenueData('month')"
                                :class="{'bg-blue-100 text-blue-600': revenuePeriod === 'month', 'bg-gray-100 text-gray-600': revenuePeriod !== 'month'}"
                                class="px-3 py-1 text-sm rounded-md"
                            >
                                Last 6 Months
                            </button>
                        </div>
                    </div>
                    <div class="h-64">
                        <BarChart v-if="revenueChartData" :chart-data="revenueChartData" />
                    </div>
                    <div class="mt-4 grid grid-cols-3 gap-4">
                        <div class="text-center">
                            <p class="text-gray-500 text-sm">Current Month</p>
                            <p class="font-semibold" :class="metrics.revenue_change >= 0 ? 'text-green-600' : 'text-red-600'">
                                ৳ {{ metrics.current_month_revenue.toLocaleString() }}
                            </p>
                        </div>
                        <div class="text-center">
                            <p class="text-gray-500 text-sm">Last Month</p>
                            <p class="font-semibold">৳ {{ metrics.last_month_revenue.toLocaleString() }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-gray-500 text-sm">Change</p>
                            <p class="font-semibold" :class="metrics.revenue_change >= 0 ? 'text-green-600' : 'text-red-600'">
                                {{ metrics.revenue_change >= 0 ? '+' : '' }}{{ metrics.revenue_change }}%
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Bill Categories Breakdown -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Bill Categories</h2>
                    <div class="h-48">
                        <PieChart v-if="billCategoriesData" :chart-data="billCategoriesData" />
                    </div>
                    <div class="mt-6 space-y-3">
                        <div v-for="(category, index) in topBillCategories" :key="index" class="flex justify-between">
                            <div class="flex items-center">
                                <span class="w-3 h-3 rounded-full mr-2" :class="chartColors[index].bg"></span>
                                <span class="text-sm text-gray-600">{{ category.name }}</span>
                            </div>
                            <span class="text-sm font-medium">৳ {{ category.amount.toLocaleString() }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Bookings & Recent Bills -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Recent Bookings -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">Recent Bookings</h2>
                    </div>
                    <div class="divide-y divide-gray-200">
                        <div v-for="booking in recentBookings" :key="booking.id" class="px-6 py-4 hover:bg-gray-50">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-gray-800">Booking #{{ booking.booking_no }}</p>
                                    <p class="text-sm text-gray-500">{{ booking.user_name }} • {{ booking.room_no }}</p>
                                </div>
                                <div class="text-right">
                                    <p :class="getBookingStatusClass(booking)">{{ getBookingStatusText(booking) }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ formatDate(booking.checkin_at) }} - {{ formatDate(booking.checkout_at) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-3 bg-gray-50 text-right">
                        <a href="/admin/room-booking" class="text-sm font-medium text-blue-600 hover:underline">View all bookings</a>
                    </div>
                </div>

                <!-- Recent Bills -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">Recent Bills</h2>
                    </div>
                    <div class="divide-y divide-gray-200">
                        <div v-for="bill in recentBills" :key="bill.id" class="px-6 py-4 hover:bg-gray-50">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-gray-800">{{ bill.invoice_no }}</p>
                                    <p class="text-sm text-gray-500">{{ bill.user_name }} • Room #{{ bill.room_no }}</p>
                                </div>
                                <div class="text-right">
                                    <p :class="bill.paid_status ? 'text-green-600' : 'text-yellow-600'" class="text-sm font-medium">
                                        ৳ {{ bill.total_payable.toLocaleString() }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ bill.paid_status ? 'Paid' : 'Pending' }} • {{ formatDate(bill.bill_date) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-3 bg-gray-50 text-right">
                        <a href="/bill-master" class="text-sm font-medium text-blue-600 hover:underline">View all bills</a>
                    </div>
                </div>
            </div>

            <!-- Room Occupancy -->
            <div class="bg-white rounded-lg shadow p-6 mb-8">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-800">Room Occupancy Status</h2>
                    <a href="/rooms" class="text-sm text-blue-600 hover:underline">View all rooms</a>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Pie Chart -->
                    <div class="h-64">
                        <PieChart
                            v-if="roomOccupancyData"
                            :chart-data="roomOccupancyData"
                            :chart-options="{
                                plugins: {
                                    legend: {
                                        position: 'right',
                                        labels: {
                                            usePointStyle: true,
                                            pointStyle: 'circle',
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
                                                const value = context.raw;
                                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                                const percentage = Math.round((value / total) * 100);
                                                return `${label}: ${value} (${percentage}%)`;
                                            }
                                        }
                                    }
                                },
                                cutout: '60%'
                            }"
                        />
                    </div>

                    <!-- Status Cards -->
                    <div class="grid grid-cols-2 gap-4">
                        <div v-for="(status, index) in roomStatuses" :key="index" class="text-center">
                            <div :class="statusClasses[index].bg" class="p-4 rounded-lg h-full flex flex-col justify-center">
                                <p class="text-2xl font-bold" :class="statusClasses[index].text">{{ status.count }}</p>
                                <p class="text-sm">{{ status.status }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import MetricCard from './MetricCard.vue';
import BarChart from './charts/BarChart.vue';
import PieChart from './charts/PieChart.vue';
import axios from 'axios';
import moment from 'moment';

export default {
    components: {
        MetricCard,
        BarChart,
        PieChart
    },
    data() {
        return {
            loading: true,
            lastUpdated: null,
            metrics: {
                total_bookings: 0,
                total_members: 0,
                total_revenue: 0,
                pending_approvals: 0,
                current_month_revenue: 0,
                last_month_revenue: 0,
                revenue_change: 0,
                booking_change: 0,
                member_change: 0
            },
            revenueChartData: null,
            billCategoriesData: null,
            roomOccupancyData: null,
            recentBookings: [],
            recentBills: [],
            revenuePeriod: 'year',
            topBillCategories: [],
            roomStatuses: [
                { status: 'Occupied', count: 4 },
                { status: 'Reserved', count: 20 },
                { status: 'Maintenance', count: 1 },
                { status: 'Available', count: 1 },
                { status: 'Total Rooms', count: 6 }
            ],
            chartColors: [
                { bg: 'bg-blue-500', text: 'text-blue-800', hex: '#3B82F6' },
                { bg: 'bg-green-500', text: 'text-green-800', hex: '#10B981' },
                { bg: 'bg-yellow-500', text: 'text-yellow-800', hex: '#F59E0B' },
                { bg: 'bg-purple-500', text: 'text-purple-800', hex: '#8B5CF6' },
                { bg: 'bg-red-500', text: 'text-red-800', hex: '#EF4444' },
                { bg: 'bg-indigo-500', text: 'text-indigo-800', hex: '#6366F1' }
            ],
            statusClasses: [
                { bg: 'bg-red-100', text: 'text-red-800' },      // Occupied
                { bg: 'bg-yellow-100', text: 'text-yellow-800' }, // Reserved
                { bg: 'bg-purple-100', text: 'text-purple-800' }, // Maintenance
                { bg: 'bg-green-100', text: 'text-green-800' },   // Available
                { bg: 'bg-blue-100', text: 'text-blue-800' }      // Total Rooms
            ]
        };
    },
    created() {
        this.fetchDashboardData();
        this.lastUpdated = moment().format('MMMM D, YYYY, h:mm a');
    },
    methods: {
        async fetchDashboardData() {
            try {
                this.loading = true;
                const [metricsRes, bookingsRes, billsRes, categoriesRes, roomsRes] = await Promise.all([
                    axios.get('/api/admin/dashboard/metrics'),
                    axios.get('/api/admin/dashboard/recent-bookings'),
                    axios.get('/api/admin/dashboard/recent-bills'),
                    axios.get('/api/admin/dashboard/bill-categories'),
                    axios.get('/api/admin/dashboard/room-status')
                ]);

                this.metrics = metricsRes.data;
                this.recentBookings = bookingsRes.data;
                this.recentBills = billsRes.data;
                this.topBillCategories = categoriesRes.data.categories;
                this.roomStatuses = roomsRes.data || this.roomStatuses; // Fallback to default data

                this.prepareRevenueChart(metricsRes.data.revenue_data);
                this.prepareBillCategoriesChart(categoriesRes.data);
                this.prepareRoomOccupancyChart();

                this.loading = false;
            } catch (error) {
                console.error('Error fetching dashboard data:', error);
                this.loading = false;
            }
        },
        prepareRoomOccupancyChart() {
            // Filter out "Total Rooms" from the pie chart
            const pieData = this.roomStatuses.filter(status => status.status !== 'Total Rooms');

            const backgroundColors = pieData.map((_, index) => {
                // Map Tailwind colors to hex values
                const colorMap = {
                    'bg-red-100': '#f35b5b',
                    'bg-yellow-100': '#efdf78',
                    'bg-purple-100': '#bdadfa',
                    'bg-green-100': '#71e3ab',
                    'bg-blue-100': '#588ad3'
                };
                return colorMap[this.statusClasses[index].bg] || '#999999';
            });

            this.roomOccupancyData = {
                labels: pieData.map(status => status.status),
                datasets: [{
                    data: pieData.map(status => status.count),
                    backgroundColor: backgroundColors,
                    borderWidth: 1,
                    borderColor: '#fff'
                }]
            };
        },
        async loadRevenueData(period) {
            this.revenuePeriod = period;
            try {
                const response = await axios.get(`/api/admin/dashboard/revenue?period=${period}`);
                this.prepareRevenueChart(response.data);
            } catch (error) {
                console.error('Error loading revenue data:', error);
            }
        },
        prepareRevenueChart(data) {
            this.revenueChartData = {
                labels: data.labels,
                datasets: [
                    {
                        label: 'Revenue (৳)',
                        backgroundColor: '#3B82F6',
                        data: data.values,
                        borderRadius: 4
                    }
                ]
            };
        },
        prepareBillCategoriesChart(data) {
            const backgroundColors = this.topBillCategories.map((_, index) => {
                return this.chartColors[index].hex;
            });

            this.billCategoriesData = {
                labels: this.topBillCategories.map(cat => cat.name),
                datasets: [{
                    data: this.topBillCategories.map(cat => cat.amount),
                    backgroundColor: backgroundColors,
                    borderWidth: 1,
                    borderColor: '#fff'
                }]
            };
        },
        getBookingStatusText(booking) {
            const today = moment().startOf('day');
            const checkin = moment(booking.checkin_at).startOf('day');
            const checkout = moment(booking.checkout_at).startOf('day');

            if (!booking.is_approved) return 'Pending Approval';
            if (booking.is_released) return 'Completed';
            if (today.isSame(checkin)) return 'Check-in Today';
            if (today.isBetween(checkin, checkout)) return 'Active';
            if (today.isBefore(checkin)) return 'Upcoming';
            return 'Completed';
        },
        getBookingStatusClass(booking) {
            const status = this.getBookingStatusText(booking);
            switch (status) {
                case 'Pending Approval': return 'text-yellow-600 text-sm font-medium';
                case 'Check-in Today': return 'text-blue-600 text-sm font-medium';
                case 'Active': return 'text-green-600 text-sm font-medium';
                case 'Upcoming': return 'text-purple-600 text-sm font-medium';
                default: return 'text-gray-600 text-sm font-medium';
            }
        },
        formatDate(date) {
            return moment(date).format('MMM D');
        }
    }
};
</script>
