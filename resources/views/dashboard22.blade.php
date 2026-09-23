@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container mx-auto px-4 py-6">
      <!-- Welcome Header -->
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
            <div>
                  <h1 class="text-3xl font-bold text-gray-800">Dashboard Overview ...</h1>
                  <p class="text-gray-600">Welcome back, Admin! Here's what's happening with your business today.</p>
            </div>
            <div class="mt-4 md:mt-0">
                  <span class="text-sm text-gray-500">Last updated: {{ now()->format('F j, Y, g:i a') }}</span>
            </div>
      </div>

      <!-- Key Metrics Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Bookings -->
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                  <div class="flex items-center justify-between">
                        <div>
                              <p class="text-gray-500 font-medium">Total Bookings</p>
                              <h3 class="text-2xl font-bold text-gray-800">142</h3>
                        </div>
                        <div class="bg-blue-100 p-3 rounded-full">
                              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                              </svg>
                        </div>
                  </div>
                  <div class="mt-4">
                        <span class="text-green-500 text-sm font-medium">↑ 12% from last month</span>
                  </div>
            </div>

            <!-- Total Members -->
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                  <div class="flex items-center justify-between">
                        <div>
                              <p class="text-gray-500 font-medium">Total Members</p>
                              <h3 class="text-2xl font-bold text-gray-800">89</h3>
                        </div>
                        <div class="bg-green-100 p-3 rounded-full">
                              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                              </svg>
                        </div>
                  </div>
                  <div class="mt-4">
                        <span class="text-green-500 text-sm font-medium">↑ 5 new this month</span>
                  </div>
            </div>

            <!-- Total Revenue -->
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
                  <div class="flex items-center justify-between">
                        <div>
                              <p class="text-gray-500 font-medium">Total Revenue</p>
                              <h3 class="text-2xl font-bold text-gray-800">1,42,850</h3>
                        </div>
                        <div class="bg-purple-100 p-3 rounded-full">
                              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-500" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                              </svg>
                        </div>
                  </div>
                  <div class="mt-4">
                        <span class="text-green-500 text-sm font-medium">↑ 18% from last month</span>
                  </div>
            </div>

            <!-- Pending Approvals -->
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
                  <div class="flex items-center justify-between">
                        <div>
                              <p class="text-gray-500 font-medium">Pending Approvals</p>
                              <h3 class="text-2xl font-bold text-gray-800">7</h3>
                        </div>
                        <div class="bg-yellow-100 p-3 rounded-full">
                              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-500" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                              </svg>
                        </div>
                  </div>
                  <div class="mt-4">
                        <a href="#" class="text-blue-500 text-sm font-medium hover:underline">Review now</a>
                  </div>
            </div>
      </div>

      <!-- Charts and Detailed Metrics -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Revenue Chart -->
            <div class="bg-white rounded-lg shadow p-6 lg:col-span-2">
                  <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold text-gray-800">Monthly Revenue</h2>
                        <div class="flex space-x-2">
                              <button class="px-3 py-1 text-sm bg-blue-100 text-blue-600 rounded-md">This Year</button>
                              <button class="px-3 py-1 text-sm bg-gray-100 text-gray-600 rounded-md">Last Year</button>
                        </div>
                  </div>
                  <!-- Chart placeholder - replace with actual chart library -->
                  <div class="h-64 bg-gray-100 rounded-md flex items-center justify-center">
                        <p class="text-gray-500">Revenue chart will be displayed here</p>
                  </div>
                  <div class="mt-4 grid grid-cols-3 gap-4">
                        <div class="text-center">
                              <p class="text-gray-500 text-sm">Current Month</p>
                              <p class="font-semibold text-green-600">42,850</p>
                        </div>
                        <div class="text-center">
                              <p class="text-gray-500 text-sm">Last Month</p>
                              <p class="font-semibold">36,200</p>
                        </div>
                        <div class="text-center">
                              <p class="text-gray-500 text-sm">Change</p>
                              <p class="font-semibold text-green-600">+18%</p>
                        </div>
                  </div>
            </div>

            <!-- Bill Categories Breakdown -->
            <div class="bg-white rounded-lg shadow p-6">
                  <h2 class="text-lg font-semibold text-gray-800 mb-4">Bill Categories</h2>
                  <!-- Pie chart placeholder -->
                  <div class="h-48 bg-gray-100 rounded-md flex items-center justify-center mb-4">
                        <p class="text-gray-500">Pie chart will be displayed here</p>
                  </div>
                  <div class="space-y-3">
                        <div class="flex justify-between">
                              <div class="flex items-center">
                                    <span class="w-3 h-3 bg-blue-500 rounded-full mr-2"></span>
                                    <span class="text-sm text-gray-600">Room Rent</span>
                              </div>
                              <span class="text-sm font-medium">98,500</span>
                        </div>
                        <div class="flex justify-between">
                              <div class="flex items-center">
                                    <span class="w-3 h-3 bg-green-500 rounded-full mr-2"></span>
                                    <span class="text-sm text-gray-600">Food & Catering</span>
                              </div>
                              <span class="text-sm font-medium">24,300</span>
                        </div>
                        <div class="flex justify-between">
                              <div class="flex items-center">
                                    <span class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></span>
                                    <span class="text-sm text-gray-600">Laundry</span>
                              </div>
                              <span class="text-sm font-medium">12,750</span>
                        </div>
                        <div class="flex justify-between">
                              <div class="flex items-center">
                                    <span class="w-3 h-3 bg-purple-500 rounded-full mr-2"></span>
                                    <span class="text-sm text-gray-600">Other Services</span>
                              </div>
                              <span class="text-sm font-medium">7,300</span>
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
                        <div class="px-6 py-4 hover:bg-gray-50">
                              <div class="flex items-center justify-between">
                                    <div>
                                          <p class="font-medium text-gray-800">Booking #BK-2023-00142</p>
                                          <p class="text-sm text-gray-500">John Doe • Deluxe Room</p>
                                    </div>
                                    <div class="text-right">
                                          <p class="text-sm font-medium text-blue-600">Check-in: Today</p>
                                          <p class="text-xs text-gray-500">15 Oct - 20 Oct</p>
                                    </div>
                              </div>
                        </div>
                        <div class="px-6 py-4 hover:bg-gray-50">
                              <div class="flex items-center justify-between">
                                    <div>
                                          <p class="font-medium text-gray-800">Booking #BK-2023-00141</p>
                                          <p class="text-sm text-gray-500">Jane Smith • Standard Room</p>
                                    </div>
                                    <div class="text-right">
                                          <p class="text-sm font-medium text-green-600">Active</p>
                                          <p class="text-xs text-gray-500">12 Oct - 18 Oct</p>
                                    </div>
                              </div>
                        </div>
                        <div class="px-6 py-4 hover:bg-gray-50">
                              <div class="flex items-center justify-between">
                                    <div>
                                          <p class="font-medium text-gray-800">Booking #BK-2023-00140</p>
                                          <p class="text-sm text-gray-500">Robert Johnson • Suite</p>
                                    </div>
                                    <div class="text-right">
                                          <p class="text-sm font-medium text-gray-600">Completed</p>
                                          <p class="text-xs text-gray-500">05 Oct - 10 Oct</p>
                                    </div>
                              </div>
                        </div>
                  </div>
                  <div class="px-6 py-3 bg-gray-50 text-right">
                        <a href="#" class="text-sm font-medium text-blue-600 hover:underline">View all bookings</a>
                  </div>
            </div>

            <!-- Recent Bills -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                  <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">Recent Bills</h2>
                  </div>
                  <div class="divide-y divide-gray-200">
                        <div class="px-6 py-4 hover:bg-gray-50">
                              <div class="flex items-center justify-between">
                                    <div>
                                          <p class="font-medium text-gray-800">INV-2023-00158</p>
                                          <p class="text-sm text-gray-500">John Doe • Room #205</p>
                                    </div>
                                    <div class="text-right">
                                          <p class="text-sm font-medium text-green-600">8,450</p>
                                          <p class="text-xs text-gray-500">Paid • 14 Oct 2023</p>
                                    </div>
                              </div>
                        </div>
                        <div class="px-6 py-4 hover:bg-gray-50">
                              <div class="flex items-center justify-between">
                                    <div>
                                          <p class="font-medium text-gray-800">INV-2023-00157</p>
                                          <p class="text-sm text-gray-500">Jane Smith • Room #112</p>
                                    </div>
                                    <div class="text-right">
                                          <p class="text-sm font-medium text-green-600">6,200</p>
                                          <p class="text-xs text-gray-500">Paid • 12 Oct 2023</p>
                                    </div>
                              </div>
                        </div>
                        <div class="px-6 py-4 hover:bg-gray-50">
                              <div class="flex items-center justify-between">
                                    <div>
                                          <p class="font-medium text-gray-800">INV-2023-00156</p>
                                          <p class="text-sm text-gray-500">Robert Johnson • Room #301</p>
                                    </div>
                                    <div class="text-right">
                                          <p class="text-sm font-medium text-yellow-600">12,750</p>
                                          <p class="text-xs text-gray-500">Pending • 10 Oct 2023</p>
                                    </div>
                              </div>
                        </div>
                  </div>
                  <div class="px-6 py-3 bg-gray-50 text-right">
                        <a href="#" class="text-sm font-medium text-blue-600 hover:underline">View all bills</a>
                  </div>
            </div>
      </div>

      <!-- Room Occupancy -->
      <div class="bg-white rounded-lg shadow p-6 mb-8">
            <div class="flex justify-between items-center mb-4">
                  <h2 class="text-lg font-semibold text-gray-800">Room Occupancy Status</h2>
                  <a href="#" class="text-sm text-blue-600 hover:underline">View all rooms</a>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                  <div class="text-center">
                        <div class="bg-green-100 text-green-800 p-3 rounded-lg">
                              <p class="text-2xl font-bold">12</p>
                              <p class="text-sm">Occupied</p>
                        </div>
                  </div>
                  <div class="text-center">
                        <div class="bg-blue-100 text-blue-800 p-3 rounded-lg">
                              <p class="text-2xl font-bold">5</p>
                              <p class="text-sm">Reserved</p>
                        </div>
                  </div>
                  <div class="text-center">
                        <div class="bg-yellow-100 text-yellow-800 p-3 rounded-lg">
                              <p class="text-2xl font-bold">3</p>
                              <p class="text-sm">Maintenance</p>
                        </div>
                  </div>
                  <div class="text-center">
                        <div class="bg-red-100 text-red-800 p-3 rounded-lg">
                              <p class="text-2xl font-bold">2</p>
                              <p class="text-sm">Unavailable</p>
                        </div>
                  </div>
                  <div class="text-center">
                        <div class="bg-purple-100 text-purple-800 p-3 rounded-lg">
                              <p class="text-2xl font-bold">18</p>
                              <p class="text-sm">Available</p>
                        </div>
                  </div>
                  <div class="text-center">
                        <div class="bg-gray-100 text-gray-800 p-3 rounded-lg">
                              <p class="text-2xl font-bold">40</p>
                              <p class="text-sm">Total Rooms</p>
                        </div>
                  </div>
            </div>
      </div>
</div>
@endsection