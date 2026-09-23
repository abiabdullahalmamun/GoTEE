@extends('layouts.app')
@push('style')
@endpush
@section('content')
<div class="mx-auto">
        <x-page-header title="Send To HCI" />
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
<div class="bg-white shadow rounded mt-2 px-3 py-2 max-w-full mx-auto">
    <form method="POST" action="{{ route('app-send2hci.store') }}"  >
		@csrf
		@method('POST')


		
		<div class="flex justify-between items-center gap-4 w-full">
    <!-- Left section: Label + Select -->
			<div class="flex items-center gap-2">
					<label for="form_date"><i>Receive Date:</i></label><br>
					<input type="date" class="form-control datepicker w-full px-3 py-2 border" name="rec_date" id="rec_date" data-date-format="yyyy/mm/dd" required autocomplete="off" value="{{ date('Y-m-d') }}">
			</div>
			<div class="flex items-center gap-2">
				<label for="centerId" class="whitespace-nowrap">Center:</label>
				<select class="cname form-control border px-2 py-1" name="centerId" required id="centerId">
					<option value=""></option>
					@foreach($centerList as $item)
						<option value="{{ $item['id'] }}">{{ $item['center_name'] }}</option>
					@endforeach
				</select>
			</div>

			<!-- Right section: Buttons -->
			<div class="flex gap-2">
				<button type="button" id="checkAllBtn" class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600">
					Check All
				</button>
				<button type="button" id="uncheckAllBtn" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">
					Uncheck All
				</button>
			</div>
		</div>

		<hr class="my-4 border-t border-gray-300" />

		<div id="data-list" class="rawdata mt-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-10 gap-1">
		</div>
		
		<hr class="my-4 border-t border-gray-300" />

		<button type="submit" id="submit" class="bg-primary text-white px-4 py-2 rounded hover:bg-primary-dark">SUBMIT</button>

    </form>
</div>
</div>


@endsection

@push('scripts')
    @vite(['resources/js/scripts/send2hci.js'])
    @vite(['resources/js/scripts/index_script.js'])
@endpush
