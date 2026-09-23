@extends('layouts.app')
@push('style')
@endpush
@section('content')
    <div class="mx-auto">
        <x-page-header title="Operators" />

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

        <div class="bg-white shadow rounded p-6 mt-1 mx-auto">
            <div class="flex justify-between items-center mb-4">
                <button id="openModal" class="bg-primary text-white px-4 py-2 rounded hover:bg-primary-dark">
                    + Add New Operator
                </button>

                <form class="text-right" method="GET" style="width: 50%">
                    <input type="text" name="search"
                        class="px-3 py-2 border border-gray-300 rounded w-1/3 focus:outline-none focus:ring-2 focus:ring-primary"
                        id="searchInput" placeholder="Search..">
                </form>
            </div>

            <div class="mt-4">
                <div id="parent-floor-list" class="floor-list overflow-y-hidden overflow-x-auto ">
                    <table class="w-full border border-gray-200 text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border px-4 py-2 text-left">SL</th>
                                <th class="border px-4 py-2 text-left">EmpID</th>
                                <th class="border px-4 py-2 text-left">Name</th>
                                <th class="border px-4 py-2 text-left">CardId</th>
                                <th class="border px-4 py-2 text-left">PIN</th>
                                 <th class="border px-4 py-2 text-left">UpdatedAt</th>
                                <th class="border px-4 py-2 text-left">Status</th>
                                <th class="border px-4 py-2 text-center">Shops</th>
                                <th class="border px-4 py-2 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="datatable">
                            @forelse ($query as $key => $data)
                                <tr
                                    class="border hover:bg-gray-50 transform  transition-all duration-500 ease-out row-animation">
                                    <td class="border px-4 py-2">{{ ++$key }}</td>
                                    <td class="border px-4 py-2">{{ $data->emp_id }}</td>
                                    <td class="border px-4 py-2">{{ $data->emp_name }}</td>
                                     <td class="border px-4 py-2">{{ $data->card_id }}</td>
                                    <td class="border px-4 py-2"> {{ $data->pin}}</td>
                                   
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
                                      <td class="border px-4 py-2 text-center">   <a href="{{ route('operators.assign', $data->id) }}" class="bg-primary text-white px-2 py-1 rounded text-xs hover:bg-orange-500 transition">
                                         <i class="fas fa-eye"></i>

                                    </a> </td>
                                    <td class="border px-4 py-2 text-center">
                                

                                        <button onclick="editOperator({{ $data }})" rel="tooltip" title="Edit"
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
                                <tr
                                    class="border hover:bg-gray-50 transform transition-all duration-500 ease-out row-animation">
                                    <td class="border px-4 py-1" colspan="5">
                                        <p class="py-2 text-center">No data found</p>
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>
                <x-table-pagination :paginator="$query" />
            </div>
        </div>
    </div>

    <!-- Create Menu Modal -->
    <div id="OperatorModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden z-[1100]">
        <div class="bg-white p-6 rounded w-96 relative">
            <h2 class="text-lg font-bold mb-4" id="modalTitle">Add Operator</h2>
            @include('pages.operators.partials.create')
        </div>
    </div>

    <!-- Edit Menu Modal -->
    <div id="editModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden z-[1100]">
        <div class="bg-white p-6 rounded w-96">
            <h2 class="text-lg font-bold mb-4">Edit Operator</h2>
            @include('pages.operators.partials.edit')
        </div>
    </div>

    <!-- View Menu Modal -->
    <div id="viewModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden z-[1100]">
        <div class="bg-white p-6 rounded w-96 relative">
            <h2 class="text-lg font-bold mb-4">Assign Operator and Shop</h2>
            @include('pages.operators.partials.view')
        </div>
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/scripts/operator.js'])
    @vite(['resources/js/scripts/index_script.js'])
@endpush
