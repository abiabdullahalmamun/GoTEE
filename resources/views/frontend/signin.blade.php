@extends('frontend.layouts.master')

@section('title', "Sign In")

@section('content')

    <!-- Header with Slider and Overlay -->
    <header>
        <div class="h-[66px] md:h-[70px] lg:h-[70px]">
            @include('frontend.layouts.topbar')
        </div>
    </header>

    <div class="relative"> <!-- Added relative positioning container -->
        <img src="{{ asset('assets/images/header/image.jpg') }}"
             alt="Gallery Image"
             class="gallery-item w-full object-cover max-h-40" />

        <!-- Overlay text centered both horizontally and vertically -->
        <div class="absolute inset-0 flex items-center justify-center">
            <h2 class="text-primary text-3xl md:text-5xl font-bold mb-1 text-center drop-shadow-lg">
                SIGN IN
            </h2>
        </div>
    </div>


    <section id="service-item" class="service-pdf-view bg-label-primary-light ">
        <div class="max-w-7xl mx-auto px-4 py-8">

            <!-- Login Card -->
            <div class="max-w-md mx-auto  border-2 border-primary rounded-lg shadow-md overflow-hidden p-8">
                <!-- Error Messages -->
                @if ($errors->any())
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
                @endif

                @if(session('error'))
                    <div class="max-w-7xl mx-auto px-4 py-2">
                        <div class="bg-red-400 border border-red-600 text-white px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email -->
                    <div>
                        <label class="block text-gray-700 font-medium">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                               class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary"
                               required autofocus autocomplete="username">

                        @error('email')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-gray-700 font-medium">Password</label>
                        <div class="relative">
                            <input type="password" id="password" name="password"
                                   class="w-full p-2 pr-10 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary"
                                   required autocomplete="current-password" >

                            <span class="absolute right-3 top-3 cursor-pointer text-gray-500">
                    <i id="eye-icon" class="fas fa-eye" ></i>
                </span>
                        </div>

                        @error('password')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                        @enderror

                        <!-- Password Strength Bar -->
                        <div class="mt-2">
                            <div class="w-full h-2 bg-gray-300 rounded">
                                <div id="password-strength-bar" class="h-2 strength-bar rounded"></div>
                            </div>
                            <p id="password-strength-text" class="text-sm text-gray-600 mt-1"></p>
                        </div>
                    </div>

        

                    <!-- Submit Button -->
                    <button type="submit"
                            class="w-full bg-primary hover:bg-primary-dark text-white font-medium py-3 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition duration-200">
                        Sign In
                    </button>

            
                </form>
            </div>
        </div>
    </section>


@endsection
@push('fScripts')
@vite(['resources/js/scripts/password_status.js'])
@endpush
