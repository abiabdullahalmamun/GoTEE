@extends('layouts.app')

@section('content')
    <div class="mx-auto ">
        <div class="max-w-full mx-auto">
            <x-page-header title="Edit User" />
        </div>

        <div class="bg-white shadow rounded p-6 mt-1 max-w-full mx-auto">
            <form action="{{ route('users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="w-full">
                        <label for="name" class="block text-gray-700 font-semibold mb-1">User ID</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded focus:ring focus:ring-blue-300 focus:border-blue-500"
                               placeholder="Enter User ID" required>
                        @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                     <div class="w-full">
                     </div>
                      <div class="w-full">
                     </div>
                    <!-- Name Field -->
                    <div class="w-full">
                        <label for="name" class="block text-gray-700 font-semibold mb-1">Name</label>
                        <input type="text" id="fullname" name="fullname" value="{{ old('FullName', $user->FullName) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded focus:ring focus:ring-blue-300 focus:border-blue-500"
                               placeholder="Enter full name" required>
                        @error('FullName')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email Field (Readonly) -->
                    <div class="w-full">
                        <label for="email" class="block text-gray-700 font-semibold mb-1">Username / Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-100 cursor-not-allowed"
                               readonly>
                        @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Role Dropdown -->
                    <div class="w-full">
                        @if(auth()->id() == $user->id)
                            <input type="hidden" name="role_id" value="{{ $user->role_id }}">
                        @else
                             <label for="role" class="block text-gray-700 font-semibold mb-1">Select Role</label>
                            <select name="role_id" id="role"
                                class="w-full px-3 py-2 border border-gray-300 rounded focus:ring focus:ring-blue-300 focus:border-blue-500"
                                required>
                                <option value="">-- Select Role --</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        @endif


<!-- 
                        <select name="role_id" id="role"
                            class="w-full px-3 py-2 border border-gray-300 rounded focus:ring focus:ring-blue-300 focus:border-blue-500"
                            required
                            {{ auth()->id() == $user->id ? 'disabled' : '' }}>
                        <option value="">-- Select Role --</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
 -->



                        @error('role_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Field (Optional) -->
                    <div class="w-full relative">
                        <label for="password" class="block text-gray-700 font-semibold mb-1">New Password (Leave blank to keep existing)</label>
                        <input type="password" id="password" name="password" onkeyup="checkPasswordStrength()"
                               class="w-full px-3 py-2 border border-gray-300 rounded focus:ring focus:ring-blue-300 focus:border-blue-500"
                               placeholder="Enter new password">
                        <i id="eye-icon" class="fas fa-eye absolute right-3 top-10 cursor-pointer text-gray-500"
                           onclick="togglePassword()"></i>
                        @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror

                        <!-- Password Strength Bar -->
                        <div class="mt-2">
                            <div class="h-2 w-full bg-gray-200 rounded overflow-hidden">
                                <div id="password-strength-bar" class="h-2 bg-gray-300 strength-bar"></div>
                            </div>
                            <p id="password-strength-text" class="text-xs text-gray-600 mt-1">Strength</p>
                        </div>
                    </div>

                    <!-- Confirm Password Field -->
                    <div class="w-full">
                        <label for="password_confirmation" class="block text-gray-700 font-semibold mb-1">Confirm New Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" onkeyup="matchPasswords()"
                               class="w-full px-3 py-2 border border-gray-300 rounded focus:ring focus:ring-blue-300 focus:border-blue-500"
                               placeholder="Confirm new password">
                        <p id="password-match-text" class="text-xs mt-1"></p>
                    </div>
                    <div class="w-full">
                        <label for="role" class="block text-gray-700 font-semibold mb-1">Select Center</label>
                        <select name="centerId" id="centerId" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring focus:ring-blue-300 focus:border-blue-500" >
                            <option value="">All</option>
                            @foreach($center as $itm)
                                <option value="{{ $itm->id }}" {{ $user->centerId == $itm->id ? 'selected' : '' }} >{{ $itm->center_name }}</option>
                            @endforeach
                        </select>
                        @error('centerId')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="w-full">
                        <label for="role" class="block text-gray-700 font-semibold mb-1">Select Status</label>
                        <select name="is_active" id="is_active"
                                class="w-full px-3 py-2 border border-gray-300 rounded focus:ring focus:ring-blue-300 focus:border-blue-500" required>
                          
                            <option value="1" {{ $user->is_active == 1 ? 'selected' : '' }}> Active </option>
                            <option value="0" {{ $user->is_active == 0 ? 'selected' : '' }}> Inactive </option>
                           
                        </select>
                        @error('status')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="mt-6 text-right">
                    <button type="submit"
                            class="bg-primary text-white px-4 py-2 rounded hover:bg-primary-dark transition">
                        Update User
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
