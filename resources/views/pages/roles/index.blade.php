@extends('layouts.app')

@section('content')
    <!-- CSS Animations added inline -->

    <div class="mx-auto">
        <x-page-header title="Manage Roles" />

        <div class="bg-white shadow rounded p-6 mt-1 mx-auto">
            <!-- Top Section: Add Role & Search -->
            <div class="flex justify-between items-center mb-4">
                <button id="openRoleModal" class="bg-primary text-white px-4 py-2 rounded hover:bg-primary-dark">
                    + Add New Role
                </button>

                <input type="text" id="searchRoleInput" placeholder="Search..."
                       class="px-3 py-2 border border-gray-300 rounded w-1/3 focus:outline-none focus:ring-2 focus:ring-primary">
            </div>

            <!-- Role List -->
            <div class="mt-4 overflow-y-hidden overflow-x-auto">
                <table class="w-full border border-gray-200 text-sm">
                    <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-4 py-2 text-left">ID</th>
                        <th class="border px-4 py-2 text-left">Name</th>
                        <th class="border px-4 py-2 text-right">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($roles as $role)
                        <tr class="border hover:bg-gray-50 transform  transition-all duration-500 ease-out row-animation">
                            <td class="border px-4 py-2">{{ $role->id }}</td>
                            <td class="border px-4 py-2">{{ $role->name }}</td>
                            <td class="border px-4 py-2 text-right">
                                <button onclick="viewRole({{ $role }})" class="bg-primary text-white px-2 py-1 rounded">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button onclick="editRole({{ $role }})" class="bg-gray-300 hover:bg-yellow-500 text-white px-2 py-1 mx-1 rounded">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form id="deleteRoleForm-{{ $role->id }}" action="{{ route('roles.destroy', $role->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmRoleDelete({{ $role->id }})" class="bg-gray-300 hover:bg-red-500 text-white px-2 py-1 rounded">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination Component -->
            <x-table-pagination :paginator="$roles" />
        </div>
    </div>

    <!-- Create Role Modal -->
    <div id="roleModal" class="modal fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden z-[1100]">
        <div class="bg-white p-6 rounded w-96 relative">
            <h2 class="text-lg font-bold mb-4">Create Role</h2>
            <form action="{{ route('roles.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="role_name" class="block text-gray-700">Role Name</label>
                    <input type="text" name="name" id="role_name" class="w-full px-3 py-2 border rounded" required>
                </div>
                <button type="submit" class="bg-primary text-white px-4 py-2 rounded hover:bg-primary-dark">Save</button>
                <button type="button" class="ml-2 bg-gray-400 text-white px-4 py-2 rounded" id="closeRoleModal">Cancel</button>
            </form>
        </div>
    </div>

    <!-- Edit Role Modal -->
    <div id="editRoleModal" class="modal fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden z-[1100]">
        <div class="bg-white p-6 rounded w-96 relative">
            <h2 class="text-lg font-bold mb-4">Edit Role</h2>
            <form id="editRoleForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_role_id" name="role_id">
                <div class="mb-4">
                    <label for="edit_role_name" class="block text-gray-700">Role Name</label>
                    <input type="text" name="name" id="edit_role_name" class="w-full px-3 py-2 border rounded" required>
                </div>
                <button type="submit" class="bg-primary text-white px-4 py-2 rounded hover:bg-primary-dark">Update</button>
                <button type="button" class="ml-2 bg-gray-400 text-white px-4 py-2 rounded" id="closeEditRoleModal">Cancel</button>
            </form>
        </div>
    </div>

    <!-- View Role Modal -->
    <div id="viewRoleModal" class="modal fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden z-[1100]">
        <div class="bg-white p-6 rounded w-96 relative">
            <h2 class="text-lg font-bold mb-4">View Role</h2>
            <p><strong>Name:</strong> <span id="view_role_name"></span></p>
            <button type="button" class="mt-4 bg-gray-400 text-white px-4 py-2 rounded" id="closeViewRoleModal">Close</button>
        </div>
    </div>

@endsection

@push('scripts')
    @vite(['resources/js/scripts/role.js'])
    @vite(['resources/js/scripts/index_script.js'])
@endpush
