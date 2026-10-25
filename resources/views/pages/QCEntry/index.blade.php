@extends('layouts.app')
@push('style')
@endpush
@section('content')
    <div class="mx-auto">
        <x-page-header title="QC Entry" />
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
        <form class="w-full" method="POST" action="{{ route('qc-entry.store') }}">        
            <!-- <form > -->
                   @csrf
                @method('POST')

<div class="flex justify-between items-center mb-4">
     <div>
        <label for="total" class="text-gray-700"><i>AppID:</i></label>
         <span class="ml-2 font-bold">
        <input type="text" name="appdev" id="appdev" value="{{ $deviceId }}"  required readonly
        class="border-none outline-none focus:ring-0">
        </span>
    </div>
    <div>
    <label class="text-gray-700"><i>Operator:</i></label>
    <span id="operator-value" class="ml-2 font-bold">
        <b>{{ $operator }}</b>
    </span>
    <input type="number" name="oprId" id="oprId" value="{{$oprId}}" readonly hidden>
</div>
 
     <div>
        <label for="total" class="text-gray-700"><i>LineID:</i></label>
        <!-- <span  id="count" class="ml-2 font-bold"><b>{{ $lineID }}</b></span> -->
        <span class="ml-2 font-bold">
        <input type="text" name="lineid" id="lineid" value="{{ $lineID }}"  required readonly
        class="border-none outline-none focus:ring-0">
        </span>
    
    </div>
        <div>
        <label for="total" class="text-gray-700"><i>Total:</i></label>
        <span  id="count" class="ml-2 font-bold"><b>{{ $count }}</b></span>
    </div>
</div>

<div class="flex justify-between items-start mb-4 gap-4">

    <!-- Left side: Wider input controls -->
    <div id="pass-buttons"  style="display: none;" class="flex flex-col space-y-3 flex-none w-80">

        <!-- First input row -->
        <div class="flex items-center gap-2">
            <button type="button" id="web-minus"
                class="w-24 px-5 py-2 border rounded bg-green-500 text-white hover:bg-green-600">
                -1
            </button>

            <input type="number" id="web" name="web"
                class="form-control px-3 py-2 border rounded w-24"
                value="0" min="0" step="1">

            <button type="button" id="web-plus"
                class="w-24 px-5 py-2 border rounded bg-green-500 text-white hover:bg-green-600">
                +1
            </button>
        </div>

        <!-- Second input row -->
        <div class="flex items-center gap-2">
            <button type="button" id="web2-minus"
                class="w-24 px-5 py-2 border rounded bg-red-500 text-white hover:bg-red-600">
                -1
            </button>

            <input type="number" id="web2" name="web2"
                class="form-control px-3 py-2 border rounded w-24"
                value="0" min="0" step="1">

            <button type="button" id="web2-plus"
                class="w-24 px-5 py-2 border rounded bg-red-500 text-white hover:bg-red-600">
                +1
            </button>
        </div>

    </div>

    <!-- Right side: Bundle details -->
    <div id="bundle-details" class="flex-1 min-w-0">
    </div>

    <input type="hidden" name="orderId" id="orderId" value="">
    <input type="hidden" name="bundleSl" id="bundleSl" value="">

</div>
 

 <div id="save-button-container" style="display:none;" class="mb-4">

    <input  type="hidden" name="selected_faults"
           id="selected_faults_input" value="[]">  

    <input  type="hidden" name="selected_process"
           id="selected_process_input" value="[]">

    <!-- Two large buttons -->
    <div class="grid grid-cols-2 gap-4 mb-4">

        <button type="button" id="bt1"
            class="w-full min-h-20 p-4 rounded-lg bg-blue-600
                   text-white text-lg font-bold hover:bg-blue-700">
             Process Defect
        </button>

        <button type="button" id="bt2"
            class="w-full min-h-20 p-4 rounded-lg bg-orange-500
                   text-white text-lg font-bold hover:bg-orange-600">
             Other Defect
        </button>

    </div>

    <!-- Display selected defects -->
    <div id="selected-fault-display"
         class="flex flex-wrap gap-2 mb-4">
    </div>

    <!-- Final submission -->
    <button type="submit" id="submit-qc"
        class="px-5 py-3 border rounded bg-green-500
               text-white font-bold hover:bg-green-600">
        SAVE
    </button>

</div>

<div id="fault-modal"
     style="display:none;"
     class="fixed inset-0 z-[100000] bg-black/50
            items-center justify-center p-4">

    <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl
                max-h-[85vh] overflow-y-auto p-5">

        <div class="flex justify-between items-center mb-4">
            <h3 id="fault-modal-title"
                class="text-lg font-bold">
                Select Defect
            </h3>

            <button type="button" id="close-fault-modal"
                class="px-3 py-2 bg-gray-200 rounded">
                Close
            </button>
        </div>

        <div id="fault-modal-buttons"
             class="grid grid-cols-3 sm:grid-cols-4 lg:grid-cols-5 gap-3">
        </div>

    </div>
</div>
<!-- <div id="save-button-container"
     style="display: none;"
     class="mb-4">
    
    <input type="hidden" name="selected_faults" id="selected_faults_input" value="[]" >
    <input type="hidden" name="selected_process" id="selected_process_input" value="[]" >
 
    <div id="defect-buttons"
         class="grid grid-cols-6 gap-2 w-full mb-4">
    </div>

    <div class="flex justify-between items-start gap-4">
        <button type="submit" id="submit-qc"
            class="px-5 py-2 border rounded bg-green-500 text-white hover:bg-blue-600"
            style="margin-top: 12px;">
            SAVE
        </button>
    </div>

</div>
 -->
 

 		
</form> 

            </div>
        </div>
    </div>


@endsection

@push('scripts')
    <script>
        window.faultList = @json($FaultList);
        window.goTeeDeviceId = @json($deviceId);
        window.goTeeOperatorId = @json($oprId);
        window.checkQcCardUrl = @json(route('check-qc-card'));
    </script>

    @vite(['resources/js/scripts/qcEntry.js'])
    @vite(['resources/js/scripts/index_script.js'])
@endpush

