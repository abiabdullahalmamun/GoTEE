@extends('layouts.app')

@section('content')
    <div class="mx-auto">
        <!-- Page Header Component -->
        <div class="max-w-full md:max-w-5xl mx-auto">
            <x-page-header title="User Details" />
        </div>

        <!-- User Details Card -->
        <div class="bg-white shadow rounded p-6 mt-1 max-w-full md:max-w-5xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div class="w-full">
                    <label class="block text-gray-700 font-semibold mb-1">Name:</label>
                    <p class="text-gray-900">{{ $user->name }}</p>
                </div>

                <!-- Email / Username -->
                <div class="w-full">
                    <label class="block text-gray-700 font-semibold mb-1">Username / Email:</label>
                    <p class="text-gray-900">{{ $user->email }}</p>
                </div>

                <!-- Status -->
                <div class="w-full flex items-center">
                    <label class="block text-gray-700 font-semibold mb-1">Status:</label>
                    <span id="status-text"
                          class="ml-2 text-sm px-3 py-1 rounded-full font-semibold
                          {{ $user->is_active ? 'bg-green-500 text-white' : 'bg-red-500 text-white' }}">
                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>

                <!-- Created At -->
                <div class="w-full">
                    <label class="block text-gray-700 font-semibold mb-1">Created At:</label>
                    <p class="text-gray-900">{{ $user->created_at->format('d M Y, h:i A') }}</p>
                </div>

            </div>

            <!-- Actions -->
            <div class="mt-6 flex justify-between">
                <a href="{{ route('users.index') }}"
                   class="bg-primary text-white px-4 py-2 rounded hover:bg-primary-dark transition">
                    Back
                </a>

{{--                <div class="space-x-2">--}}
{{--                    <a href="{{ route('users.edit', $user) }}"--}}
{{--                       class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition">--}}
{{--                        Edit--}}
{{--                    </a>--}}

{{--                    <form action="{{ route('users.destroy', $user) }}" method="POST"--}}
{{--                          onsubmit="return confirm('Are you sure you want to delete this user?');" class="inline">--}}
{{--                        @csrf--}}
{{--                        @method('DELETE')--}}
{{--                        <button type="submit"--}}
{{--                                class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition">--}}
{{--                            Delete--}}
{{--                        </button>--}}
{{--                    </form>--}}
{{--                </div>--}}
            </div>
        </div>
    </div>
@endsection
