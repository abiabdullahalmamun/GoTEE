@extends('layouts.app')
@push('style')
@endpush
@section('content')
    <div class="mx-auto">
        <x-page-header title="Sticker Types" />
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
                    + Add Sticker
                </button>

              <!--   <form class="text-right" method="GET" style="width: 50%">
                    <input type="text" name="search"
                        class="px-3 py-2 border border-gray-300 rounded w-1/3 focus:outline-none focus:ring-2 focus:ring-primary"
                        id="searchInput" placeholder="Search..">
                </form> -->
            </div>

            <div class="mt-4">
                <div id="parent-floor-list" class="floor-list overflow-y-hidden overflow-x-auto ">
                    <table id="visaTable" class="w-full border border-gray-200 text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border px-4 py-2 text-left">SL</th>
                                <th class="border px-4 py-2 text-left">StickerInfo</th>
								<th class="border px-4 py-2 text-left">Symbol</th>
								<th class="border px-4 py-2 text-left">Center</th>
                                <th class="border px-4 py-2 text-left">Remarks</th>
                                <th class="border px-4 py-2 text-left">CreatedBy<br>TDD</th>
                               <th class="border px-4 py-2 text-left">Created At</th>
                                <th class="border px-4 py-2 text-left">Updated At</th>
                                <th class="border px-4 py-2 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="datatable">
                            @forelse ($query as $key => $data)
                                <tr
                                    class="border hover:bg-gray-50 transform  transition-all duration-500 ease-out row-animation">
                                    <td class="border px-4 py-2">{{ ++$key }}</td>
                                    <td class="border px-4 py-2">{{ $data->StickerInfo }}</td>
									<td class="border px-4 py-2">{{ $data->sticker }}</td>
									<td class="border px-4 py-2">{{ $data->center->center_name }}</td>
									<td class="border px-4 py-2">{{ $data->remarks }}</td>
                                    <td class="border px-4 py-2">{{ $data->user?->name  ?? 'N/A'  }}</td>
									<td class="border px-4 py-2">{{ $data->created_at }}</td>
                                    <td class="border px-4 py-2">{{ $data->updated_at }}</td>
                                    <td class="border px-4 py-2 text-center">
                           
                                        <button onclick="editShop({{ $data }})" rel="tooltip" title="Edit"
                                            data-id="4"
                                            class="bg-gray-300 hover:bg-yellow-500 text-white px-2 py-1 mx-1 rounded">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        
                                        <form action="{{ route('sticker_type.destroy', $data->id) }}" method="POST"
                                            style="display: inline;" onclick="confirmDelete({{ $data->id }})">
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
    <div id="VisaTypeModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden z-[1100]">
        <div class="bg-white p-6 rounded w-96 relative">
            <h2 class="text-lg font-bold mb-4" id="modalTitle">Create Sticker</h2>
            @include('pages.StickerMap.partials.create')
        </div>
    </div>

    <!-- Edit Menu Modal -->
    <div id="editModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden z-[1100]">
        <div class="bg-white p-6 rounded w-96">
            <h2 class="text-lg font-bold mb-4">Edit Sticker</h2>
            @include('pages.StickerMap.partials.edit')
        </div>
    </div>

    <!-- View Menu Modal -->
    <div id="viewModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden z-[1100]">
        <div class="bg-white p-6 rounded w-96 relative">
            <h2 class="text-lg font-bold mb-4">View Sticker</h2>
            @include('pages.StickerMap.partials.view')
        </div>
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/scripts/sticker.js'])
    @vite(['resources/js/scripts/index_script.js'])
@endpush
