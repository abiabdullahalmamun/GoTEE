@extends('layouts.app')
@push('style')
@endpush
@section('content')
    <div class="mx-auto">
        <x-page-header title="Forms Received from HCI/AHCI." />
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
               
                <form method="POST" action="{{ route('receiveDocument4mhci.store') }}" enctype="multipart/form-data" >
                   @csrf
                    @method('POST')
					
				
				<table>
	 
					<tr>
						<td width=50%>
								<label for="form_date"><i>Receive Date:</i></label><br>
							<input type="date" class="form-control datepicker w-full px-3 py-2 border" name="date" data-date-format="yyyy/mm/dd" required autocomplete="off" value="{{ date('Y-m-d') }}">
			
						</td>
						<td width=50%>
							<label for="center"><i>File Type</i></label><br>
		 							<select class="form-control w-full px-3 py-2 border" name="filetype" required="required" id="filetype">
								<option value="1">ICON</option>
								<option value="2">IVAC</option>
								
							</select>		
						</td>
					</tr>
			   
					<tr>
						<td>
							<div class="form-group">
								<label for="center"><i>HCI/AHCI:</i></label><br>
								<select class="form-control w-full px-3 py-2 border" name="regionId" required="required" id="regionId">
									<option value=""></option>
									@foreach($hciList as $item)
									<option value="{{ $item['id'] }}">{{ $item['region_name'] }}</option>
									@endforeach
								</select>
							</div>
						</td>
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
					
			<!-- 		<tr>
						<td colspan="2" class="caution">
							<br>
						    - FOR MULTIPLE WEBFILE: PLEASE SELECT XLS FILE (KEEP THE WEBFILE FIELD BLANK)
						</td>
					</tr> -->
					
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
