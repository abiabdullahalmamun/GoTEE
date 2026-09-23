@extends('layouts.app')
@push('style')
    <style>
        .parent { background-color: #f3f4f6; font-weight: bold; }
        .submenu { background-color: #e2e8f0; }
        .child { background-color: #f8fafc; }
        .toggle-children { cursor: pointer; margin-right: 5px; }
    </style>
@endpush
@section('content')
    <div class="mx-auto">
        <x-page-header title="Assign Role Permissions" />
        <div class="bg-white shadow rounded p-6 mt-1 mx-auto">
            <form action="{{ route('role_permissions.store') }}" method="POST">
                @csrf

                <!-- Role Selection -->
                <div class="mb-4">
                    <label for="role_id" class="block text-gray-700 font-semibold">Select Role</label>
                    <select name="role_id" id="role_id" class="w-full px-3 py-2 border rounded">
                        <option value="">-- Select Role --</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Permissions Table (Initially Hidden) -->
                <div id="permission-container" class="hidden overflow-y-hidden overflow-x-auto ">
                    <table class="w-full border-collapse border border-gray-200 text-sm">
                        <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-2 py-2 text-left">Menu</th>
                            <th class="border px-2 py-2 text-left"><input type="checkbox" id="checkAllView"> View</th>
                            <th class="border px-2 py-2 text-left"><input type="checkbox" id="checkAllCreate"> Create</th>
                            <th class="border px-2 py-2 text-left"><input type="checkbox" id="checkAllEdit"> Edit</th>
                            <th class="border px-2 py-2 text-left"><input type="checkbox" id="checkAllDelete"> Delete</th>
                        </tr>
                        </thead>
                        <tbody id="permissionTable">
                        @foreach($menus as $menu)
                            @include('pages.role_permissions.menu_item', ['menu' => $menu, 'level' => 1])
                        @endforeach
                        </tbody>
                    </table>

                    <button type="submit" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Save Permissions</button>
                </div>
            </form>
        </div>
    </div>


@endsection


@push('scripts')
    @vite(['resources/js/scripts/role_permissions.js'])
@endpush
