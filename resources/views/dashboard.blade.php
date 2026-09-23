@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container mx-auto px-4 py-1">     
    <!-- Welcome Header -->
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-2">
          <div>
              <h1 class="text-3xl font-bold text-gray-800">Dashboard.</h1>
          </div>
          <div class="mt-4 md:mt-0">
              <span class="text-sm font-semibold text-gray-500 current-time">Last updated: {{ now()->format('F j, Y, g:i a') }}</span>
          </div>
      </div>
         @php
                  $currentMonth = \Carbon\Carbon::now()->format('M');
            @endphp


<div class="bg-white rounded-lg shadow p-4 mb-4 border-l-4 border-indigo-500">

    <table class="w-full text-sm">
        <thead class="text-gray-500 font-medium border-b">
            <tr>
                <th class="py-1 text-center">sl</th>
                <th class="py-1 text-center">api</th>
                <th class="text-center">payload</th>
                <th class="text-center">response</th>
            </tr>
        </thead>

        <tbody class="font-semibold text-gray-800" id="logTableBody">
            
        </tbody>
    </table>

</div>

 

</div>
@endsection

@php
    $token = auth()->user()->createToken('dashboard-token')->plainTextToken;
@endphp

@push('scripts')
<script>
    window.API_TOKEN = "{{ $token }}";
</script>

@vite(['resources/js/scripts/dashboard.js'])
@endpush
