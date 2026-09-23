@extends('layouts.app')
@push('style')
@endpush
@section('content')
<div class="mx-auto">
	<x-page-header title="Search Result" />
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
		{{-- Your existing info tables stay here --}}

        {{-- ====================== --}}
        {{-- New Section: Series Report --}}
        {{-- ====================== --}}
        <div class="mt-4">
            <div class="flex justify-between items-center mb-2">
                <h3 class="font-semibold">Sticker Missing Report</h3>
                <h2 class="font-semibold">Date: {{$date}}</h2>
                <div>
                    <a href="{{ route('missing-sticker.excel',['date' => $date, 'center' => $center]) }}" class="bg-green-600 text-white px-2 py-1 rounded text-sm">Excel</a>
                    <a href="{{ route('missing-sticker.pdf' ,['date' => $date, 'center' => $center])  }}" class="bg-red-600 text-white px-2 py-1 rounded text-sm">PDF</a>

                </div>
            </div>

            <div class="seriesTable">
                <table class="w-full text-sm border border-gray-300">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-2 py-1">Sl</th>
                            <th class="border px-2 py-1">Sticker</th>
                            <th class="border px-2 py-1">Total</th>
                            <th class="border px-2 py-1">Start</th>
                            <th class="border px-2 py-1">End</th>
                            <th class="border px-2 py-1">Missing</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($results as $res)
                            <tr>
                                {{-- Serial number --}}
                                <td class="text-center align-middle border px-2 py-1">{{ $loop->iteration }}</td>

                                <td class="text-center align-middle border px-2 py-1">{{ $res['series'] }}</td>
                                <td class="border px-2 py-1 text-center align-middle">{{ $res['total'] }}</td>
                                <td class="text-center align-middle border px-2 py-1">{{ $res['start'] }}</td>
                                <td class="text-center align-middle border px-2 py-1">{{ $res['end'] }}</td>
                                <td class="align-middle border px-2 py-1">
                                    @php 
                                        $missing = array_slice($res['missing'], 0, 20); 
                                        $more = array_slice($res['missing'], 20);
                                    @endphp

                                    {{ implode(', ', $missing) }}

                                    @if(!empty($more))
                                        <br>
                                        {{ implode(', ', $more) }}
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-2">No Series data available</td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>
        {{-- ====================== --}}

        {{-- Continue with your "SSL Data", "Tracking", "SMS" sections --}}
        {{-- ... existing code ... --}}
    </div>
</div> 
@endsection

@push('scripts')
    @vite(['resources/js/scripts/index_script.js'])
@endpush
