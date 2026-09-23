@extends('layouts.app')
@push('style')
@endpush
@section('content')
    <div class="mx-auto">
        <x-page-header title="Form Fill-In" />
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
		<div class="status bg-white shadow rounded mt-2 max-w-full mx-auto">
			
		<form method="POST" action="{{ route('form-fill.store') }}"  >
			@csrf
			@method('POST')
					
            <table width=100%>
			  <tr>
			  	<td width="80%"> </td>
 
				
				<td>
					<!-- <div class="label">Received</div> -->
					<div class="number">Total:  <b>{{$total}}</b></div>
				</td>
	<!-- 			<td>
					<div class="label"> 
						<button type="button" id="tdd" class="callbtn bg-green-500 text-white px-3 py-1 max-w-full"><b>TDD</b></button>	
					</div>
					 
				</td> -->
			  </tr>
			</table>
        </div>
		
		<!-- Queue & Form Area -->

		<div class="queue bg-white shadow mt-2 max-w-full mx-auto">
            <table border="0">
			  <tr>
 
<td>
<div class="form-container">

		
		<div class="form-row">
            <label>Webfile: </label>
			<input type="text" id="wf_no" name="wf_no" autocomplete="off" maxlength="12">
		</div>

	  <div id="web_check" style="display: none"> 

 
              
		<div class="form-row">
			<label  >Name:</label> 
			<input type="text" id="name" name="name" maxlength="30"  class="hideDiv">
		</div>

		<div class="form-row">
			<label>Passport:</label>
			<input class="hideDiv" type='text' id='passport' name='passport1'  maxlength="15" placeholder="Passport Number" title="Passport Number">
	 
		</div>

		<div class="form-row">
			<label>Contact:&nbsp;</label>
			<input class="hideDiv" type="text" id="contact" name="contact" maxlength="11">
		</div>
				<div class="form-row">
			<label>Remarks:&nbsp;</label>
			<input class="hideDiv" type="text" id="remarks" name="remarks"  value="">
		</div>
		<div class="form-row">
			<label>Fee:&nbsp;</label>
			<input class="hideDiv" type="number" id="fee" name="fee" maxlength="11" value="300">
		</div>

		<!-- <button id="bt3" type="button" class="reject text-white px-4 py-2 rounded">REJECT</button> -->
		
		<button id="bt2" name="accept" class="accept text-white px-4 py-2 rounded"  type="submit">ACCEPT</button> 


		</div>
	<!-- </div> -->
</div>
</td>
			  </tr>
			</table>
        </div>
		

    </div>



@endsection

@push('scripts')
    @vite(['resources/js/scripts/form_fill.js'])
    @vite(['resources/js/scripts/index_script.js'])
@endpush
