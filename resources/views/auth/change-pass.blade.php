@extends('layouts.app')

@section('content')
    <div class="mx-auto ">
        <!-- Page Header Component -->

        <div class=" max-w-full mx-auto">
            <x-page-header title="Change User Password" />

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

        </div>
        <!-- User Create Form -->
        <div class="bg-white shadow rounded p-6 mt-1 max-w-full mx-auto">
               <form action="{{ route('password.update') }}" method="POST">
                    @csrf

                    <div class="md:flex">


                        <!-- Profile Info Section -->
                        <div class="md:w-1/3 p-6 bg-gray-50">
                            <div class="text-center">
                                <!-- <div class="w-32 h-32 rounded-full mx-auto mb-4 overflow-hidden border-4 border-white shadow-lg">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=random"
                                         alt="Profile" class="w-full h-full object-cover">
                                </div> -->
                                <h3 class="text-xl font-semibold text-gray-800">{{ Auth::user()->name }}</h3>
                                <p class="text-gray-600">{{ Auth::user()->email }}</p>
                            </div>

                            <div class="mt-6">
                                <h4 class="text-lg font-medium text-gray-700 mb-3">Account Details</h4>
                                <ul class="space-y-2">
                                    <li class="flex items-center text-gray-600">
                                        <i class="fas fa-user-tag mr-2 w-5"></i>
                                       <span>
                                            Member since: {{ Auth::user()->created_at?->format('M Y') ?? 'N/A' }}
                                        </span>
                                    </li>
                                    <li class="flex items-center text-gray-600">
                                        <i class="fas fa-phone-alt mr-2 w-5"></i>
                                        <span>{{ Auth::user()->phone ?? 'Not provided' }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                            <!-- Password Change Section -->
                            <div class="md:w-2/3 p-6">
                                <h3 class="text-2xl font-semibold text-gray-800 mb-6">Change Password</h3>
                                <!-- Error Messages -->
                 <!--                @if ($errors->any())
                                    <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                @if(session('success'))
                                    <div class="max-w-7xl mx-auto px-4 py-2">
                                        <div class="bg-primary-opt border border-primary text-white px-4 py-3 rounded relative" role="alert">
                                            <span class="block sm:inline text-white">{{ session('success') }}</span>
                                        </div>
                                    </div>
                                @endif -->
                                <!-- Current Password Form -->
                                <!-- <form id="initiatePasswordChange" class="space-y-4"> -->
                                    <!-- @csrf -->
                                    <div>
                                        <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
                                        <input type="password" id="current_password" name="current_password"
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" required>
                                    </div>

                                    <!-- Add this right after your password field -->
                                    <div class="w-full relative">
                                        <label for="password" class="block text-gray-700 font-semibold mb-1">Password</label>
                                        <input type="password" id="password" name="password"
                                               class="w-full px-3 py-2 border border-gray-300 rounded focus:ring focus:ring-blue-300 focus:border-blue-500"
                                               placeholder="Enter password" required>
                                        <i id="eye-icon" class="fas fa-eye absolute right-3 top-10 cursor-pointer text-gray-500"></i>
                                        @error('password')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror

                                        <!-- Password Requirements -->
                                        <div class="mt-2 text-xs text-gray-600">
                                            <p>Password must contain:</p>
                                            <ul class="list-disc list-inside pl-3">
                                                <li id="req-uppercase" class="text-red-500">At least 1 uppercase letter</li>
                                                <li id="req-lowercase" class="text-red-500">At least 1 lowercase letter</li>
                                                <li id="req-number" class="text-red-500">At least 1 number</li>
                                                <li id="req-special" class="text-red-500">At least 1 special character</li>
                                                <li id="req-length" class="text-red-500">Minimum 8 characters</li>
                                            </ul>
                                        </div>

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
                                        <label for="password_confirmation" class="block text-gray-700 font-semibold mb-1">Confirm Password</label>
                                        <input type="password" id="password_confirmation" name="password_confirmation"
                                               class="w-full px-3 py-2 border border-gray-300 rounded focus:ring focus:ring-blue-300 focus:border-blue-500"
                                               placeholder="Confirm password" required>
                                        <p id="password-match-text" class="text-xs mt-1"></p>
                                    </div>

                                    <button type="submit"
                                            class="w-full md:w-auto px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-dark transition duration-300">
                                      UPDATE
                                    </button>
                                <!-- </form> -->
                            </div>

                    </div>
                </form>
        </div>
    </div>

@endsection
