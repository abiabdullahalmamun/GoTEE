@extends('layouts.app')

@push('style')
    {{-- DataTables CSS is bundled via Vite in device.js --}}
@endpush

@section('content')
    <div class="mx-auto">
        <x-page-header title="Operators" />

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
                    + Add Operation
                </button>
            </div>

            <div class="mt-4">
                <div id="parent-floor-list" class="overflow-y-hidden overflow-x-auto">
                    <table id="deviceTable" class="display w-full border border-gray-200 text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border px-4 py-2 text-left">SL</th>
                                <th class="border px-4 py-2 text-left">EmpID</th>
                                <th class="border px-4 py-2 text-left">Name</th>
                                <th class="border px-4 py-2 text-left">Designation</th>
                                
                                <th class="border px-4 py-2 text-left">Gender</th>
                                <th class="border px-4 py-2 text-left">Dateofjoin</th>
                                <th class="border px-4 py-2 text-left">Evaluation</th>
                                <th class="border px-4 py-2 text-center">Grade</th>
                                
                                <th class="border px-4 py-2 text-center">Remarks</th>
                                <th class="border px-4 py-2 text-center">CardID</th>
                                <th class="border px-4 py-2 text-center">EmpType</th>
                                <th class="border px-4 py-2 text-center">createdBy</th>
                                
                                <th class="border px-4 py-2 text-center">createdAt</th>
                                <th class="border px-4 py-2 text-center">Status</th>
                                <th class="border px-4 py-2 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($query as $key => $data)
                                <tr class="hover:bg-gray-50 transition">
                                                                      <td class="border px-4 py-2">{{ ++$key }}</td>
                                    <td class="border px-4 py-2">{{ $data->emp_id }}</td>
                                    <td class="border px-4 py-2">{{ $data->emp_name }}</td>
                                    <td class="border px-4 py-2"> {{ $data->desig?->designation}}</td>
        
        <td class="border px-4 py-2">{{ [1 => 'Male', 2 => 'Female', 3 => 'Others'][$data->gender1] ?? 'N/A' }}
                                    </td>
        <td class="border px-4 py-2">{{ $data->Date_of_join }}</td>
        <td class="border px-4 py-2">{{ $data->evaluation }}</td>
        <td class="border px-4 py-2"> {{ $data->grade?->gradeName}}</td>
        
         <td class="border px-4 py-2">{{ $data->remarks }}</td>
         <td class="border px-4 py-2">{{ $data->card_id }}</td>
         <td class="border px-4 py-2"> 
    {{ [1 => 'IT', 2 => 'Operator', 3 => 'IE', 4 => 'Technician', 5 => 'QC Finish', 6 => 'QC End', 7 => 'QC Roam'][$data->emp_type] ?? 'N/A' }}
             </td>
        <td class="border px-4 py-2"> {{ $data->user?->name }}</td>
                                   
                                    <td class="border px-4 py-2">{{ $data->updated_at }}</td>
                                    <td class="border px-4 py-2">
                                        <span
                                            class="
                                            text-xs font-medium mr-2 px-2.5 py-0.5 rounded
                                            {{ $data->status
                                                ? 'bg-green-100/50 text-green-800 border border-green-800'
                                                : 'bg-red-500/50 text-red-800 border border-red-800' }}">
                                            {{ $data->status ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                     
                                    <td class="border px-4 py-2 text-center">
                                        <button onclick="editDevice({{ $data }})" rel="tooltip" title="Edit"
                                            data-id="4"
                                            class="bg-gray-300 hover:bg-yellow-500 text-white px-2 py-1 mx-1 rounded">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        
                                        <form action="{{ route('operators.destroy', $data->id) }}" method="POST"
                                            style="display: inline;" onclick="confirmRoleDelete({{ $data->id }})">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="bg-gray-300 hover:bg-red-500 text-white px-2 py-1 rounded"
                                                onclick="return confirm('Are you sure?')">
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
            <h2 class="text-lg font-bold mb-4" id="modalTitle">Create Operator</h2>
            @include('pages.operators.partials.create')
        </div>
    </div>

    {{-- Edit Modal --}}
    <div id="editModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden z-[1100]">
        <div class="bg-white p-6 rounded w-96">
            <h2 class="text-lg font-bold mb-4">Edit Operator</h2>
            @include('pages.operators.partials.edit')
        </div>
    </div>


@endsection

@push('scripts')
    @vite(['resources/js/scripts/operator.js'])
@endpush
