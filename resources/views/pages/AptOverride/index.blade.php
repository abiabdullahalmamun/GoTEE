@extends('layouts.app')
@push('style')
@endpush
@section('content')
    <div class="mx-auto">
        <x-page-header title="Appointment Override" />
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
               
                <form method="POST" action="{{ route('apt-override.store') }}" enctype="multipart/form-data" >
                   @csrf
                    @method('POST')
					
				
				<table>
					<tr>
						<td width=50%>
							<div class="form-group">
								<label for="center"><i>FormType:</i></label><br>
								<select class="form-control w-full px-3 py-2 border" name="formtype" required="required" id="formtype">
									<option value="">SELECT</option>
									<option class="bg-yellow-200" value="1">FOREIGN PASSPORT</option>
									<option class="bg-yellow-100" value="2">WAIVE</option>
									<option class="bg-yellow-100" value="3">OTHERS</option>
								</select>
							</div>
						</td>
					</tr>
					<tr>
						<td width=50%>
							<div class="form-group">
								<label for="center"><i>Center:</i></label><br>
								<select class="form-control w-full px-3 py-2 border" name="centerId" required="required" id="centerId">
									<option value=""></option>
									@foreach($centerList as $item)
									<option value="{{ $item['id'] }}">{{ $item['center_name'] }}</option>
									@endforeach
								</select>
							</div>
						</td>
						<td width=50%>
							<div class="form-group">
								<label for="sticker_type"><i>Visa Type:</i></label><br>
								<select class="form-control w-full px-3 py-2 border" name="visatypeId" required="required" id="visatypeId">
                                   <option value=""></option>
                                   @foreach($VisaType as $item)
									<option value="{{ $item['id'] }}">{{ $item['visa_type'] }}</option>
									@endforeach
                        
                            </select>
							</div>
						</td>
					</tr>
			   
					<tr>
						<td>
							<label for="form_date"><i>Appointment Date:</i></label><br>
							<input type="date" class="form-control datepicker w-full px-3 py-2 border" name="date" data-date-format="yyyy/mm/dd" required autocomplete="off" value="{{ date('Y-m-d') }}">
						</td>
						<td>
							<label for="form_date"><i>Remarks</i></label><br>
                            <input type="text" class="form-control w-full px-3 py-2 border" name="remarks"  required  autocomplete="off" value="">
						</td>
					</tr>
					
					<tr>
						<td>
							<br>
							<label for="form_date"><i>Webfile</i></label><br>
                            <input type="text" class="form-control w-full px-3 py-2 border" name="WebFile_no"  autocomplete="off" value="">
						</td>
						<td>
							<br>
							<label for="form_date"><i>Phone</i></label><br>
                            <input type="text" class="form-control w-full px-3 py-2 border" name="phone"    autocomplete="off" value="">
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
