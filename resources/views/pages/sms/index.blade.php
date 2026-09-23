@extends('layouts.app')
@push('style')
@endpush
@section('content')
    <div class="mx-auto">
        <x-page-header title="SMS Management" />
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
        <div class="bg-white shadow rounded p-6 mt-1 mx-auto">
            <div class="apt-ovr8 flex justify-between items-center mb-4">
               
                <form method="POST" action="{{ route('send-sms.store') }}" enctype="multipart/form-data" >
                   @csrf
                    @method('POST')
				
        <div class="row">
    		<div class="mb-3 col-12 col-md-4 col-lg-6 flex space-x-4">
            	<div class="form-group flex-1">
            	<label for="to_date"><i>Type Text:</i></label><br>
        	 	<textarea name="smstext"
                    class="form-control @error('Contact') is-invalid @enderror w-full px-3 py-2 border rounded"
                    placeholder="Type Text here..." required>{{ old('smstext') }}</textarea>
                @error('smstext')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
	            </div>
	        </div>

        	<div class="mb-3 col-12 col-md-4 col-lg-6 flex space-x-4">
            	<div class="form-group flex-1">
            		<label for="to_date"><i>Choose import file:</i></label><br>
                    <input type="file" class="form-control w-full border" name="import_file" id="import_file" >
				</div>
			</div>
			
        	<div class="mb-3 col-12 col-md-4 col-lg-6 flex space-x-4">
            	<div class="form-group flex-1">
            		<button type="submit" id="submit" class="bg-primary text-white px-4 py-2 rounded hover:bg-primary-dark">SUBMIT</button>
				</div>
			</div>
		</div>
	<!-- 			<table>
			   
					<tr>
					
						<td>
							<label for="form_date"><i>Remarks</i></label><br>
                            <input type="text" class="form-control w-full px-3 py-2 border" name="remarks"  required  autocomplete="off" value="">
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
							- FOR SINGLE WEBFILE: PLEASE TYPE IT (NO NEED TO ATTACH XLS)<br>
                            - FOR MULTIPLE WEBFILE: PLEASE SELECT XLS FILE (KEEP THE WEBFILE FIELD BLANK)
						</td>
					</tr>
					
				</table> -->
				
				
				
				

            





                </form>
            </div>


        </div>
    </div>


@endsection

@push('scripts')
    <!-- @vite(['resources/js/scripts/region.js']) -->
    @vite(['resources/js/scripts/index_script.js'])
@endpush
