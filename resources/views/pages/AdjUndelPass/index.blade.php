@extends('layouts.app')
@push('style')
@endpush
@section('content')
    <div class="mx-auto">
        <x-page-header title="Adjust Undelivered Passport" />
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

        <div class="bg-white shadow rounded p-6 mt-1 mx-auto">
            <div class="apt-ovr8 flex justify-between items-center mb-4">
               <form method="POST" action="{{ route('adjust-undelivered.sort') }}" enctype="multipart/form-data">
               <!--  <form method="POST" action="{{ route('applicant_list_import.store') }}"  enctype="multipart/form-data" > -->
                   @csrf
                    @method('POST')
					
				
				<table>
					<tr>
						<td width=50%>
							<div class="form-group">
								<label for="center"><i>Center:</i></label><br>
								<select class="form-control w-full px-3 py-2 border" name="centerId" required="required" id="centerId">
									<!-- <option value=""></option> -->
									@foreach($centerList as $item)
									<option value="{{ $item['id'] }}" {{ $cenId ==$item['id'] ? 'selected' : '' }} >{{ $item['center_name'] }}</option>
									@endforeach
								</select>
							</div>
						</td>
		
					</tr>
					
					<tr>
						<td colspan="2">
							<label for="to_date"><i>Choose import file:</i></label><br>
                            <input type="file" class="form-control w-full border" name="import_file" id="import_file" >   
						</td>
					</tr>
					<tr>
						<td colspan="2" class="text-right">
							<br>
							<button type="submit" id="submit" class="bg-primary text-white px-4 py-2 rounded hover:bg-primary-dark">SUBMIT</button>
						</td>
					</tr>
					
				</table>
				
				<!-- accept=".txt"  -->
			
                </form>
            </div>


        </div>
    </div>


@endsection

@push('scripts')
    <!-- @vite(['resources/js/scripts/region.js']) -->
    @vite(['resources/js/scripts/index_script.js'])
@endpush
