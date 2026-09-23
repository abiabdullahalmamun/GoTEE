@extends('layouts.app')
@push('style')
@endpush
@section('content')
    <div class="mx-auto">
        <x-page-header title="Search Data" />
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

<div class="flex items-center justify-start space-x-2 px-4 py-2">
    <form action="{{ url('/searchWebfile') }}" method="POST" class="flex items-center space-x-2">
        @csrf
        @method('POST')
        <input type="text" name="searchTx1" id="searchTx1"
            class="px-4 py-2 border border-gray-300 rounded w-52 !text-black bg-white"
            placeholder="Search...">
        <button type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Search
        </button>
    </form>
</div>


@endsection

@push('scripts')
    <!-- @vite(['resources/js/scripts/app_receive.js']) -->
    @vite(['resources/js/scripts/index_script.js'])
@endpush
