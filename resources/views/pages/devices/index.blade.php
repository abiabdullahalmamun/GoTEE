@extends('layouts.app')

@push('style')
    {{-- DataTables CSS is bundled via Vite in device.js --}}
@endpush

@section('content')
    <div class="mx-auto">
        <x-page-header title="Devices" />

        {{-- Alerts --}}
        @if (session('success'))
            <div class="p-4 mb-4 text-sm text-green-800 bg-green-100 rounded-lg" role="alert">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="p-4 mb-4 text-sm text-red-800 bg-red-100 rounded-lg" role="alert">
                {{ session('error') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="p-4 mb-4 text-sm text-red-800 bg-red-100 rounded-lg">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Main card --}}
        <div class="bg-white shadow rounded p-6 mt-1 mx-auto">
            <div class="flex justify-between items-center mb-4">
                <button id="openModal" class="bg-primary text-white px-4 py-2 rounded hover:bg-primary-dark">
                    + Add Device
                </button>
            </div>

            <div class="mt-4">
                <div id="parent-floor-list" class="overflow-y-hidden overflow-x-auto">
                    <table id="deviceTable" class="display w-full border border-gray-200 text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-left">SL</th>
                                <th class="px-4 py-2 text-left">Unit Name</th>
                                <th class="px-4 py-2 text-left">DeviceID</th>
                                <th class="px-4 py-2 text-left">Device Type</th>
                                <th class="px-4 py-2 text-left">IP Address</th>
                                <th class="px-4 py-2 text-left">MAC Address</th>
                                <!-- <th class="px-4 py-2 text-left">Location</th> -->
                                <th class="px-4 py-2 text-left">Last Com.</th>
                                <th class="px-4 py-2 text-left">Created By</th>
                                <th class="px-4 py-2 text-left">LED</th>
                                <th class="px-4 py-2 text-left">Message</th>
                                <!-- <th class="px-4 py-2 text-left">Created At</th> -->
                                <!-- <th class="px-4 py-2 text-left">Updated At</th> -->
                                <th class="px-4 py-2 text-left">Status</th>
                                <th class="px-4 py-2 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($query as $key => $data)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-2">{{ ++$key }}</td>
                                    <td class="px-4 py-2">{{ $data->center?->center_name }}</td>
                                     <td class="px-4 py-2">{{ $data->devID }}</td>
                                    <td class="px-4 py-2">
                                        {{ $data->devType == 1 ? 'Linker' : ($data->devType == 2 ? 'Mender' : 'Unknown') }}
                                    </td>
                                    <td class="px-4 py-2">{{ $data->ip }}</td>
                                    <td class="px-4 py-2">{{ $data->mac }}</td>
                                    <!-- <td class="px-4 py-2">{{ $data->location }}</td> -->
                                    <td class="px-4 py-2">{{ $data->lastCom }}</td>
                                    <td class="px-4 py-2">{{ $data->user?->name }}</td>
                                  
                      @php
                        $states = [
                            1 => ['text' => 'Green',      'class' => 'text-green-600'],
                            2 => ['text' => 'Red',        'class' => 'text-red-600'],
                            3 => ['text' => 'Blue',       'class' => 'text-blue-600'],
                            4 => ['text' => 'Red+Blue',   'class' => 'text-purple-600'],
                            5 => ['text' => 'Green+Red',  'class' => 'text-orange-600'],
                            6 => ['text' => 'Green+Blue', 'class' => 'text-cyan-600'],
                            7 => ['text' => 'Off',        'class' => 'text-gray-500'],
                            8 => ['text' => 'All',        'class' => 'text-black font-bold'],
                        ];

                        $state = $states[$data->opstate] ?? [
                            'text' => 'Unknown',
                            'class' => 'text-gray-400'
                        ];
                    @endphp

                    <td class="px-4 py-2">
                        <span class="{{ $state['class'] }}">
                            {{ $state['text'] }}
                        </span>
                    </td>
                                    <td class="px-4 py-2">{{ $data->message }}</td>
                                    <td class="px-4 py-2">
                                        <span class="text-xs font-medium mr-2 px-2.5 py-0.5 rounded
                                            {{ $data->status
                                                ? 'bg-green-100/50 text-green-800 border border-green-800'
                                                : 'bg-red-500/50 text-red-800 border border-red-800' }}">
                                            {{ $data->status ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                        {!! $data->devType == 2 ? '<a href="'.route('devices.assign', $data->id).'" class="bg-primary text-white px-2 py-1 rounded hover:bg-orange-500"><i class="fas fa-eye"></i></a>'  : '' 
                                        !!}
                                      <!--   <a href="{{ route('operators.assign', $data->id) }}" class="bg-primary text-white px-2 py-1 rounded  hover:bg-orange-500"> <i class="fas fa-eye"></i></a> -->
                                        <button onclick="editDevice({{ $data }})"
                                            class="bg-gray-300 hover:bg-yellow-500 text-white px-2 py-1 mx-1 rounded"
                                            title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <form action="{{ route('devices.destroy', $data->id) }}" method="POST"
                                            style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="bg-gray-300 hover:bg-red-500 text-white px-2 py-1 rounded"
                                                onclick="return confirm('Are you sure?')" title="Delete">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="px-4 py-2 text-center" colspan="12">No data found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- ❌ Removed: <x-table-pagination :paginator="$query" /> --}}
            </div>
        </div>
    </div>

    {{-- Create Modal --}}
    <div id="ShopModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden z-[1100]">
        <div class="bg-white p-6 rounded w-96 relative">
            <h2 class="text-lg font-bold mb-4" id="modalTitle">Create Device</h2>
            @include('pages.devices.partials.create')
        </div>
    </div>

    {{-- Edit Modal --}}
    <div id="editModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden z-[1100]">
        <div class="bg-white p-6 rounded w-96">
            <h2 class="text-lg font-bold mb-4">Edit Device</h2>
            @include('pages.devices.partials.edit')
        </div>
    </div>


@endsection

@push('scripts')
    @vite(['resources/js/scripts/device.js'])
@endpush
