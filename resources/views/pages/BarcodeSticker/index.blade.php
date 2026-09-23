@extends('layouts.app')
@push('style')
@endpush
@section('content')
    <div class="mx-auto">
        <x-page-header title="Barcode Sticker Print" />
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
            <div class="sticker flex justify-between items-center mb-4">
               
                <form method="POST" action="{{ route('barcode-sticker.store') }}" target="_blank">
                   @csrf
                    @method('POST')
				
				<table>
					<tr>
						<td colspan="2">
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
					</tr>
					
					<tr>
						<td>
							<label for="sticker_type"><i>Sticker Type:</i></label><br>
							<select class="form-control w-full px-3 py-2 border" name="sticker" required="required" id="sticker">
                                <option value=""></option>
                                @foreach($stickerTypeList as $item)
                                <option value="{{ $item['id'] }}">{{ $item['sticker'] }}</option>
                                @endforeach
                            </select>
						</td>
						<td>
							<label for="form_date"><i>Date:</i></label><br>
                            <input type="date" class="form-control datepicker w-full" name="date" data-date-format="yyyy/mm/dd" required autocomplete="off" value="{{ date('Y-m-d') }}">
						</td>
					</tr>
					
					<tr>
						<td>
							<label for="start-number"><i>Start Number:</i></label><br>
                            <input type="number" name="start_number" id="start-number" class="form-control" required>
						</td>
						<td>
							<label for="end-number"><i>End Number:</i></label><br>
                            <input type="number" name="end_number" id="end-number" class="form-control" required>
						</td>
					</tr>
					<tr>
						<td colspan="2" class="text-right">
							<br>
							<button type="submit" id="submit" class="bg-primary text-white px-4 py-2 rounded hover:bg-primary-dark">Print</button>
						</td>
					</tr>
					
				</table>
<br>
<br>
<br>
               





                </form>
            </div>


        </div>
    </div>


@endsection

@push('scripts')
    <!-- @vite(['resources/js/scripts/region.js']) -->
    @vite(['resources/js/scripts/index_script.js'])
@endpush
