@extends('layouts.app')
@push('style')
@endpush
@section('content')
    <div class="mx-auto">
        <x-page-header title=" HCI/AHCI Activity by Operator." />
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
      <!--   <div class="bg-white shadow rounded p-6 mt-1 mx-auto">
            <div class="apt-ovr8 flex justify-between items-center mb-4"> -->
          <div class="bg-white shadow rounded p-6 mt-1 w-full">
        <form class="w-full">        
            <!-- <form > -->
                   @csrf
                @method('POST')

<div class="flex justify-between items-center mb-4">
    <!-- Input on Left -->
    <div class="flex items-center space-x-2">
        <label for="web" class="text-gray-700"><i>Action:</i></label>
        <select class="form-control w-full px-3 py-2 border" name="actId" required="required" id="actId">
           <option value="">All</option>
           <option value="31">Accept</option>
           <option value="32">Reject</option>
           <option value="33">Direct Delivery</option>
        </select>
    </div>

     <div>
        <label for="total" class="text-gray-700"><i>Total Received:</i></label>
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
    @vite(['resources/js/scripts/acceptReject.js'])
    @vite(['resources/js/scripts/index_script.js'])
@endpush
