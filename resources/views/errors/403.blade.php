@extends('layouts.app')

@section('content')
    <div class="flex justify-center items-center mt-10" >
        <div class="text-center">
            <h1 class="text-4xl font-bold text-red-500">403 - Permission Denied</h1>
            <p class="text-lg text-gray-700 mt-2">You do not have permission to access this page.</p>
            <a href="{{ url('/dashboard') }}" class="mt-4 inline-block bg-primary text-white px-4 py-2 rounded hover:bg-primary-dark transition">
                Go to Dashboard
            </a>
        </div>
    </div>
@endsection
