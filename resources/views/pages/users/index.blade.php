@extends('layouts.app')

@section('content')
    <div class=" mx-auto">
        <!-- Header Bar -->
        <x-page-header title="User Management" />
        @if (session('success'))
            <div class="p-4 mb-4 text-sm text-green-800 bg-green-100 rounded-lg" role="alert">   {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="p-4 mb-4 text-sm text-red-800 bg-red-100 rounded-lg" role="alert">
                {{ session('error') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="p-4 mb-4 text-sm text-red-800 bg-red-100 rounded-lg" role="alert">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Card Section with Animation -->
        <div class="bg-white p-4 mt-1 shadow rounded ">
            <div class="flex justify-between mb-4 flex-wrap gap-2">
                <!-- Create Button -->
                <a href="{{ route('users.create') }}"
                   class="bg-primary text-white px-4 py-2 rounded shadow hover:bg-blue-600 transition duration-300">
                    Create User
                </a>

                <!-- Search Form -->
    <!--             <form method="GET" action="{{ route('users.index') }}" class="flex space-x-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..."
                           class="bg-gray-100 px-3 py-1 rounded border border-gray-300 focus:border-primary focus:bg-white focus:ring focus:ring-blue-400 focus:outline-none w-64 transition duration-200">

                </form> -->
            </div>

            <!-- User Table -->
            <div class="overflow-y-hidden overflow-x-auto " id="content-box">
{{--                transform transition-transform duration-500 ease-out opacity-0 translate-y-4 id="content-box"--}}
                <table  id="userTable" class="w-full border-collapse border border-gray-200 text-sm">
                    <thead class="bg-gray-100 text-left rounded-sm">
                    <tr>
                        <th class="border px-2 py-2">SL</th>
                        <th class="border px-2 py-2">UserId</th>
                         <th class="border px-2 py-2">Name</th>
                        <th class="border px-2 py-2">CenterName</th>
                      
                        <th class="border px-2 py-2">Username/Email</th>
                         <th class="border px-2 py-2">Role</th>
                        <th class="border px-2 py-2">Status</th>
                       
                        <th class="border px-2 py-2 text-right">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($users as $i => $user)
                        <tr class="border hover:bg-gray-50 transform transition-all duration-500 ease-out row-animation
        {{ $user->is_approve ? 'font-bold' : 'text-yellow-600' }}">
                            <td class="px-2 py-2">{{ ++$i }}</td>
                            <td class="px-2 py-2">{{ $user->name }}</td>
                            <td class="px-2 py-2">{{ $user->FullName }}</td>
                            <td class="px-2 py-2">{{ $user->center?->center_name }}</td>
                           
                            <td class="px-2 py-2">{{ $user->email }}</td>
                             <td class="px-2 py-2">{{ $user->role?->name }}</td>
                            <td class="px-2 py-2">
                                  <span class="
                                text-xs font-medium mr-2 px-2.5 py-0.5 rounded-md
                                {{ $user->is_active
                                    ? 'bg-green-100/50 text-green-800 border border-green-800'
                                    : 'bg-red-500/20 text-red-600 border border-red-700' }}">
                                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                                  </span>
                            </td>
                            

                            <td class="px-2 py-2 text-center">
                  
                                <!-- Action Buttons -->
                                <div class="flex justify-end space-x-1">
                                    <!-- View Button (Light Blue) -->
                                <form action="{{ route('users.updateAssign', $user->id) }}" method="POST"
                                    style="display: inline;" onclick="confirmRoleDelete({{ $user->id }})">
                                    @csrf
                                    @method('POST')
                                    <button type="submit"
                                        class="bg-gray-300 hover:bg-red-500 text-white px-2 py-1 text-xs rounded"
                                        onclick="return confirm('Are you sure?')">
                                         <i class="fas fa-redo"></i>
                                    </button>
                                </form>

                                    <!-- Edit Button (Dark Blue) -->
                                    <a href="{{ route('users.edit', $user) }}" class="bg-gray-300 text-white px-2 py-1 rounded text-xs hover:bg-orange-500 transition">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <!-- Delete Button (Red) -->
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="bg-gray-300 text-white px-2 py-1 rounded text-xs hover:bg-red-500 transition">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>


            </div>

            <!-- Pagination Component -->
            <x-table-pagination :paginator="$users" />
        </div>
    </div>

@endsection

@push('scripts')
      @vite(['resources/js/scripts/user.js'])
      @vite(['resources/js/scripts/index_script.js'])
@endpush

