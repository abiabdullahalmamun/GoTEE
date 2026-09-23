@extends('layouts.app')
@push('style')
@endpush
@section('content')
<div class="mx-auto">
    <x-page-header title="Un-utilized Sticker Report" />
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
    
    {{-- Filter Form --}}
	<div class="bg-white shadow rounded p-2 mt-1 mx-auto">
		<div class="appReceive">
			<form method="POST" action="{{ url('/missing-sticker') }}" class="mb-2 flex gap-2">
			    @csrf
		        @method('POST')
		        
				Date:
				<input type="date" name="from_date" value="{{request('from_date')}}" class="border p-2 rounded">
				
		                <div class="flex-1 flex items-center gap-2">
                    <label class="w-20">Center:</label>
                    <select name="center" id="center" class="flex-1 border p-2 rounded">
                      @if($role == 1 || $role == 5 || $role == 13)
					        <option value="">ALL</option>
					        @foreach($centers as $cnt)
					            <option value="{{ $cnt->id }}" {{ $cenId == $cnt->id ? 'selected' : '' }}>
					                {{ $cnt['center_name'] }}
					            </option>
					        @endforeach
					    @else
					        @php
					            $userCenter = $centers->firstWhere('id', $cenId);
					        @endphp
					        <option value="{{ $cenId }}" selected>
					            {{ $userCenter ? $userCenter->center_name : 'N/A' }}
					        </option>
					    @endif
                    </select>
                </div>

				User:
		

				<button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Search</button>
			</form>
		</div>
		<hr class="my-2">
		
		{{-- Export Buttons --}}
		<!-- <div class=" flex gap-2">
			<a href="{{ route('Receive-Report.excel', request()->all()) }}" class="bg-green-600 text-white px-2 py-1 rounded text-sm">Excel</a>
			<a href="{{ route('Receive-Report.pdf', request()->all()) }}" class="bg-red-600 text-white px-2 py-1 rounded text-sm">PDF</a>
		 -->	
			<!--  <button id="openPrint" class="bg-primary text-white px-4 py-2 rounded hover:bg-primary-dark">Print</button>-->	
			<!-- <button onclick="window.print()" class="bg-gray-600 text-white px-4 py-2 rounded">Print</button> -->
		<!-- </div> -->
		
	</div>

</div>
@endsection

@push('scripts')
<script type="module">
import 'datatables.net-dt/css/jquery.dataTables.css';
import $ from 'jquery';
import 'datatables.net';

$(document).ready(function() {
    $('#reportTable').DataTable({
        paging: false, // Laravel pagination is used
        searching: true,
        ordering: true
    });
});
</script>
@endpush

@push('scripts')
    <!-- @vite(['resources/js/scripts/region.js']) -->
    @vite(['resources/js/scripts/index_script.js'])
@endpush
