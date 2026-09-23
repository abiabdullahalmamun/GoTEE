@extends('layouts.app')

@push('style')
    {{-- DataTables CSS is bundled via Vite in device.js --}}
@endpush

@section('content')
    <div class="mx-auto">
        <x-page-header title="Devices" />

        {{-- Alerts --}}
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

        {{-- Main card --}}
        <div class="bg-white shadow rounded p-6 mt-1 mx-auto">
            <div class="mt-4">
                <div id="parent-floor-list" class="overflow-y-hidden overflow-x-auto">
                     <h3>Only in DB</h3>  

                     <form action="{{ route('push.db') }}" method="POST">
                        @csrf
                        <input type="hidden" name="centerId" value="{{ $cenId }}">
                        <input type="hidden" name="list" value="{{ json_encode($onlyInDb) }}">

                        <button type="submit" class="btn btn-danger mb-2">
                            Process only DB List
                        </button>
                    </form>
                    <table id="deviceTable" class="display w-full border border-gray-200 text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-left">SL</th>
                                <th class="px-4 py-2 text-left">Passport</th>
                               
                            </tr>
                        </thead>
                        <tbody>
     
                             @forelse($onlyInDb as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="text-center">No data</td></tr>
                            @endforelse

                        </tbody>
                    </table>
                                 <h3>Only in xls</h3>
                     <form action="{{ route('push.excel') }}" method="POST">
                        @csrf
                        <input type="hidden" name="centerId" value="{{ $cenId }}">
                        <input type="hidden" name="list" value="{{ json_encode($onlyInExcel) }}">

                        <button type="submit" class="btn btn-danger mb-2">
                            Process Only xls List
                        </button>
                    </form>
                    <table id="deviceTable" class="display w-full border border-gray-200 text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-left">SL</th>
                                <th class="px-4 py-2 text-left">Passport</th>
                               
                            </tr>
                        </thead>
                        <tbody>
     
                             @forelse($onlyInExcel as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="text-center">No data</td></tr>
                            @endforelse

                        </tbody>
                    </table>
                                               <h3>common</h3>
                    <table id="deviceTable" class="display w-full border border-gray-200 text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-left">SL</th>
                                <th class="px-4 py-2 text-left">Passport</th>
                               
                            </tr>
                        </thead>
                        <tbody>
     
                             @forelse($common as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="text-center">No data</td></tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>

                {{-- ❌ Removed: <x-table-pagination :paginator="$query" /> --}}
            </div>
        </div>
    </div>

 
@endsection

<!-- @push('scripts')
    @vite(['resources/js/scripts/device.js'])
@endpush -->
