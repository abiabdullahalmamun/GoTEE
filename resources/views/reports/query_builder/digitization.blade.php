@extends('layouts.app')
@push('style')
@endpush
@section('content')
<div class="mx-auto">
	<x-page-header title="Digitization Summary" />
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
	
	
	<div class="bg-white shadow rounded p-3 mt-1 mx-auto">
	
        <div class="mt-4">
            <div class="flex justify-between items-center mb-2">
                <h3 id="reportTitle" class="font-semibold">{{$report}}</h3>
                <h2 id="fromDate" class="font-semibold">From: {{$from_date}}</h2>
                <h2 id="toDate" class="font-semibold">To: {{$to_date}}</h2>
                 <h2 id="centerName" class="font-semibold">Center: {{$center}}</h2>
   <!--              <div>
                    <a href="{{ route('missing-sticker.excel',['date' => $from_date, 'center' => $center]) }}" class="bg-green-600 text-white px-2 py-1 rounded text-sm">Excel</a>
                    <a href="{{ route('missing-sticker.pdf',['date' => $from_date, 'center' => $center])  }}" class="bg-red-600 text-white px-2 py-1 rounded text-sm">PDF</a>

                </div> -->
            </div>

            <div class="seriesTable">
                <table  id="reportTable" class="w-full text-sm border border-gray-300">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-2 py-1">Sl</th>
                            <th class="border px-2 py-1">Date</th>
                            <th class="border px-2 py-1">Send to HCI</th>
                            <th class="border px-2 py-1">Received From HCI</th>
                            <th class="border px-2 py-1">DVD created</th>
                        </tr>
                    </thead>
                    <tbody>
                   @foreach($results as $index => $row)
                        <tr>
                            <td class="text-center align-middle border px-2 py-1">{{ $loop->iteration }}</td>

                            <td class="border px-2 py-1 text-center align-middle">{{ $row['sent2hci_date'] }}</td>
                            <td class="border px-2 py-1 text-center align-middle">{{ $row['totalSend'] }}</td>
                            <td class="border px-2 py-1 text-center align-middle">{{ $row['step8Count'] }}</td>
                            <td class="border px-2 py-1 text-center align-middle">{{ $row['step9Count'] }}</td>
                  
                        </tr>
                    @endforeach
                    </tbody>
                   <tfoot>
                    <tr>
                        <td></td>
                        <td class="text-center align-middle border px-2 py-1"><b> TOTAL</b></td>
                        <td class="text-center align-middle border px-2 py-1"><b>{{ $grandTotal['totalSend'] }}</b></td>
                        <td class="text-center align-middle border px-2 py-1"><b>{{ $grandTotal['step8Count'] }}</b></td>
                        <td class="text-center align-middle border px-2 py-1"><b>{{ $grandTotal['step9Count'] }}</b></td>
                    </tr>
                </tfoot>

                </table>
            </div>
        </div>
     
    </div>
</div> 
@endsection

@push('scripts')
     @vite(['resources/js/scripts/recSummary.js'])
    @vite(['resources/js/scripts/index_script.js'])
@endpush
