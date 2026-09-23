@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container mx-auto px-4 py-6">
      <!-- Welcome Header -->
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
            <div>
                  <h1 class="text-3xl font-bold text-gray-800">Dashboard</h1>
                  <p class="text-gray-600">Welcome back..</p>
            </div>
            <div class="mt-4 md:mt-0">
                  <span class="text-sm text-gray-500">Last updated: {{ now()->format('F j, Y, g:i a') }}</span>
            </div>
      </div>

        @php
            $currentMonth = \Carbon\Carbon::now()->format('M');
        @endphp
      <!-- Key Metrics Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Bookings -->
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                  <div class="flex items-center justify-between">
                        <div>
                              <p class="text-gray-500 font-medium">Total Receive in {{ $currentMonth}}</p>
                              <h3 class="text-2xl font-bold text-gray-800 total-receive"></h3>
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
                        <span class="text-green-500 text-sm font-medium change-receive">0% from last</span>
                   </div>
            </div>

            <!-- Total Members -->
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                  <div class="flex items-center justify-between">
                        <div>
                              <p class="text-gray-500 font-medium">Total Sent2HCI in {{ $currentMonth}}</p>
                              <h3 class="text-2xl font-bold text-gray-800 total-sent2hci"></h3>
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
                        <span class="text-green-500 text-sm font-medium change-sent2hci">↑ 0%</span>
                  </div>
            </div>

            <!-- Total Revenue -->
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
                  <div class="flex items-center justify-between">
                        <div>
                              <p class="text-gray-500 font-medium">Total ReadyCenter in {{ $currentMonth}}</p>
                              <h3 class="text-2xl font-bold text-gray-800 total-readycenter"> </h3>
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
                         <span class="text-green-500 text-sm font-medium change-readycenter">↑ 0%</span>
                  </div>
            </div>

            <!-- Pending Approvals -->
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
                  <div class="flex items-center justify-between">
                        <div>
                              <p class="text-gray-500 font-medium">Total Delivery in {{ $currentMonth}}</p>
                              <h3 class="text-2xl font-bold text-gray-800 total-delivery"></h3>
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
                      <!--   <a href="#" class="text-blue-500 text-sm font-medium hover:underline">Review now</a> -->
                      <span class="text-green-500 text-sm font-medium change-delivery">↑ 0%</span>
                  </div>
            </div>
      </div>

      <!-- Charts and Detailed Metrics -->

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Center chart -->
    <div class="bg-white rounded-lg shadow p-6 flex flex-col">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-800">Today's Operation</h2>
        </div>
        <div class="flex-1" style="height: 300px;"> 
            <canvas id="centerBarChart" class="w-full h-full"></canvas>
        </div>
    </div>

    <!-- History chart -->
    <div class="bg-white rounded-lg shadow p-6 flex flex-col">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-800">Receive Trend</h2>
        </div>
        <div class="flex-1" style="height: 300px;">
            <canvas id="historyChart" class="w-full h-full"></canvas>
        </div>
    </div>
</div>


<!-- 
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
   
    <div class="bg-white rounded-lg shadow p-6 flex flex-col">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-800">Today's Operation</h2>
        </div>
        <div class="flex-1" style="min-height: 12rem;"> 
            <canvas id="centerBarChart" class="w-full h-full"></canvas>
        </div>
    </div>

   
    <div class="bg-white rounded-lg shadow p-6 flex flex-col">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-800">Receive Trend</h2>
        </div>
        <div class="flex-1" style="min-height: 12rem;">
            <canvas id="historyChart" class="w-full h-full"></canvas>
        </div>
    </div>
</div> -->


      <!-- Recent Bookings & Recent Bills -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
 
      </div>
      <!-- Room Occupancy -->
<!--       <div class="bg-white rounded-lg shadow p-6 mb-8">
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
      </div> -->
</div>
@endsection

@php
    $user = auth()->user();
    // Create a new token, or reuse existing token if you want
    $token = $user->createToken('dashboard-token')->plainTextToken;
@endphp

<script>
    window.API_TOKEN = "{{ $token }}";
</script>
@push('scripts')
      @vite(['resources/css/app.css', 'resources/js/app.js'])
    @vite(['resources/js/scripts/dashboard.js'])
    <!-- @vite(['resources/js/scripts/index_script.js']) -->
@endpush