@extends('layouts.app')

@push('style')
    {{-- DataTables CSS is bundled via Vite in device.js --}}
@endpush

@section('content')
    <div class="mx-auto">
        <x-page-header title="Money Receipt" />

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
                    + Add Money Receipt
                </button>
            </div>

            <div class="mt-4">
                <div id="parent-floor-list" class="overflow-y-hidden overflow-x-auto">
                    <table id="deviceTable" class="display w-full border border-gray-200 text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-left">SL</th>
                                <th class="px-4 py-2 text-left">BookNo</th>
                                 <th class="px-4 py-2 text-left">StartNo</th>
                                  <th class="px-4 py-2 text-left">EndNo</th>
                                <th class="px-4 py-2 text-left">CreatedBy</th>
                                <th class="px-4 py-2 text-left">Created At</th>
                                <th class="px-4 py-2 text-left">Status</th>
                                <th class="px-4 py-2 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($query as $key => $data)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-2">{{ ++$key }}</td>
                                    <td class="px-4 py-2">{{ $data->BookNo }}</td>
                                     <td class="px-4 py-2">{{ $data->startNo }}</td>
                                      <td class="px-4 py-2">{{ $data->endNo }}</td>
                                    <td class="px-4 py-2">{{ $data->user?->name }}</td>
                                    <td class="px-4 py-2">{{ $data->created_at }}</td>
                                    <td class="px-4 py-2">
                                        <span class="text-xs font-medium mr-2 px-2.5 py-0.5 rounded
                                            {{ $data->status
                                                ? 'bg-green-100/50 text-green-800 border border-green-800'
                                                : 'bg-red-500/50 text-red-800 border border-red-800' }}">
                                            {{ $data->status ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                        <button
                                            onclick='editDevice(@json($data))'
                                            class="bg-gray-300 hover:bg-yellow-500 text-white px-2 py-1 mx-1 rounded"
                                            title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                <!--         <button onclick="editDevice({{$data}})"
                                            class="bg-gray-300 hover:bg-yellow-500 text-white px-2 py-1 mx-1 rounded"
                                            title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button> -->

                                        <form action="{{ route('moneyReceipt.destroy', $data->id) }}" method="POST"
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
                                    <td class="px-4 py-2 text-center" colspan="6">No data found</td>
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
            <h2 class="text-lg font-bold mb-4" id="modalTitle">Create Money Receipt</h2>
            @include('pages.moneyReceipt.partials.create')
        </div>
    </div>

    {{-- Edit Modal --}}
    <div id="editModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden z-[1100]">
        <div class="bg-white p-6 rounded w-96">
            <h2 class="text-lg font-bold mb-4">Edit Money Receipt</h2>
            @include('pages.moneyReceipt.partials.edit')
        </div>
    </div>


@endsection

@push('scripts')
    @vite(['resources/js/scripts/moneyReceipt.js'])
@endpush
