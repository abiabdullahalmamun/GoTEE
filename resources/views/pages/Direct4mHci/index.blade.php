@extends('layouts.app')
@push('style')
@endpush
@section('content')
    <div class="mx-auto">
        <x-page-header title="Direct From HCI" />
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
       <!--  <div class="bg-white shadow rounded p-6 mt-1 mx-auto">
            <div class="apt-ovr8 flex justify-between items-center mb-4">
               
            <form > -->
             <div class="bg-white shadow rounded p-6 mt-1 w-full">
        <form class="w-full">  
                   @csrf
                @method('POST')


<div class="flex justify-between items-center mb-4">
    <!-- Input on Left -->
    <div class="flex items-center space-x-2">
        <label for="web" class="text-gray-700"><i>Webfile/Passport:</i></label>
     <input type="text" id="web" name="web" 
       class="px-3 py-2 border rounded w-64 bg-blue-500 font-bold text-yellow-300">


        <!-- <input type="text" id="web" name="web" class="form-control px-3 py-2 border rounded w-64" > -->
    </div>
    <div class="flex items-center space-x-2">
        
    </div>
    <!-- Label on Right -->
    <div>
        <label for="total" class="text-gray-700"><i>Total Direct:</i></label>
        <span  id="count" class="ml-2 font-bold"><b>{{ $count }}</b></span>
    </div>
</div>
<div class="flex justify-between items-center mb-4">
    <span  id="smsg" class="ml-2 font-bold"><b>{{ $smsg }}</b></span>
</div>
<!-- Latest Records Section -->
<div id="latest-records" class="mt-4">
    <!-- Latest 10 records will be rendered here -->
</div>

			
		
			


</form> 



            </div>


        </div>
    </div>


@endsection

@push('scripts')
    @vite(['resources/js/scripts/direct4mhci.js'])
    @vite(['resources/js/scripts/index_script.js'])
@endpush
