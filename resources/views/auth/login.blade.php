<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Fade-in animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in {
            animation: fadeIn 0.8s ease-out;
        }

        /* Password strength bar animation */
        .strength-bar {
            transition: width 0.3s ease-in-out;
        }
    </style>


</head>
<body class="flex items-center justify-center min-h-screen bg-label-primary">

<div class="w-full max-w-md p-6 bg-white shadow-lg rounded-lg fade-in">

    <!-- Header -->
    <div class=" text-primary text-center">
        <h2 class="text-xl font-semibold">- Login -</h2>
    </div>

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

    <!-- Session Status Message -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- Form -->
    <form method="POST" action="{{ route('login') }}" class="mt-1 space-y-4">
        @csrf

        <!-- Email -->
        <div>
            <label class="block text-gray-700 font-medium">Email</label>
            <input type="email" name="email" value="{{ old('email') }}"
                   class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary"
                   required autofocus autocomplete="username">

            @error('uname')
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
        <div class="flex justify-end mt-4">
            <button type="submit"
                    class="px-5 py-2 bg-primary text-white rounded shadow-md transition-all duration-300 ease-in-out transform hover:bg-primary-dark active:scale-95">
                Login
            </button>
        </div>
    </form>
</div>
@vite(['resources/js/scripts/password_status.js'])
</body>
</html>
