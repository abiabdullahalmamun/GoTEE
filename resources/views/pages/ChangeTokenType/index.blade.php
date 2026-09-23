@extends('layouts.app')
@push('style')
@endpush
@section('content')
    <div class="mx-auto">
        <x-page-header title="Change TokenType" />
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
               
                <form method="POST" action="{{ route('changeTokenType.store') }}" enctype="multipart/form-data" >
                   @csrf
                    @method('POST')
					
				
				<table>
 
					<tr>
						<td colspan="2">
							<label for="form_date"><i>Remarks</i></label><br>
                            <input type="text" class="form-control w-full px-3 py-2 border" name="remarks"  required  autocomplete="off" value="">
						</td>
					</tr>
			   
					<tr>
			 			<td width="50%">
							<div class="form-group">
								<label for="center"><i>TokenType:</i></label><br>
								<select class="form-control w-full px-3 py-2 border" name="svcType" required="required" id="svcType">
									<option value=""></option>
									@foreach($svcType as $item)
									<option value="{{ $item['id'] }}">{{ $item['service_name'] }}</option>
									@endforeach
								</select>
							</div>
						</td>
						<td width="50%">
							<label for="form_date"><i>Webfile</i></label><br>
                            <input type="text" class="form-control w-full px-3 py-2 border" name="WebFile_no"  autocomplete="off" value="">
						</td>
					</tr>
					
					<tr>
						<td colspan="2" align="center">
							OR
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
					
					<tr>
						<td colspan="2" class="caution">
							<br>
							 <span class="text-red-600 font-semibold">
					            - PLEASE GENERATE TOKEN BEFORE CHANGING TYPE
					        </span><br>
							- FOR SINGLE WEBFILE: PLEASE TYPE IT (NO NEED TO ATTACH XLS)<br>
                            - FOR MULTIPLE WEBFILE: PLEASE SELECT XLS FILE (KEEP THE WEBFILE FIELD BLANK)
						</td>
					</tr>
					
				</table>
				
				
				
				

            





                </form>
            </div>


        </div>
    </div>


@endsection

@push('scripts')
    <!-- @vite(['resources/js/scripts/region.js']) -->
    @vite(['resources/js/scripts/index_script.js'])
@endpush
