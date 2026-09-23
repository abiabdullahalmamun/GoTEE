@extends('layouts.app')

@section('content')
    <div class="mx-auto ">
        <div class="max-w-full mx-auto">
            <x-page-header title="Edit Display Service" />
        </div>

        <div class="bg-white shadow rounded p-6 mt-1 max-w-full mx-auto">
            <form action="{{ route('visa_types.updateAssign') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Name Field -->
                    <div class="w-full">
                        <label for="name" class="block text-gray-700 font-semibold mb-1">VisaType ID</label>
                        <input type="text" id="visaTypeId" name="visaTypeId" value="{{ $visaType->id }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring focus:ring-blue-300 focus:border-blue-500"        placeholder="Enter full name" readonly required>
                       
                        @error('visaTypeId')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="w-full">
                        <label for="name" class="block text-gray-700 font-semibold mb-1">Visa Type</label>
                        <input type="text" id="visa_type" name="visa_type" value="{{ $visaType->visa_type}}"   class="w-full px-3 py-2 border border-gray-300 rounded focus:ring focus:ring-blue-300 focus:border-blue-500"   placeholder="Enter full name" readonly required>
                        @error('visa_type')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
<br>
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="w-full" id="dev-svc-wrapper">
        <label class="block text-gray-700 font-semibold mb-1">Center TDD</label>

        @if($VisaTypeTdd->isNotEmpty())
<!--             @foreach ($VisaTypeTdd as $assigned)
                @php
                    $center = $Center->firstWhere('id', $assigned->centerId);
                    $daysValue = $assigned->days ?? $visaType->days ?? 7;
                @endphp

                <div class="flex items-center gap-2 mb-2 shop-group">
                    <select name="assigned_devs[]" class="w-1/2 px-3 py-2 border border-gray-300 rounded bg-yellow-300">
                        <option value="{{ $center->id }}" selected>{{ $center->center_name }}</option>
                    </select>

                    <input  
                        type="text" 
                        name="extra_inputs[]" 
                        value="{{ $daysValue }}" 
                        placeholder="Enter days"  
                        class="w-1/2 px-3 py-2 border border-gray-300 rounded" 
                    >

                    <button type="button" class="remove-shop-btn text-red-500 hover:text-red-700">&times;</button>
                </div>
            @endforeach -->

            @foreach ($VisaTypeTdd as $assigned)
                @php
                    $center = $Center->firstWhere('id', $assigned->centerId);
                    $daysValue = $assigned->days ?? $visaType->days ?? 7;
                @endphp

                @if ($center)
                    <div class="flex items-center gap-2 mb-2 shop-group">
                        <select name="assigned_devs[]" class="w-1/2 px-3 py-2 border border-gray-300 rounded bg-yellow-300">
                          <option value="{{ $center->id }}" selected>{{ $center->center_name }}</option>
                        </select>

                        <input  
                            type="text" 
                            name="extra_inputs[]" 
                            value="{{ $daysValue }}" 
                            placeholder="Enter days"  
                            class="w-1/2 px-3 py-2 border border-gray-300 rounded" 
                        >

                        <button type="button" class="remove-shop-btn text-red-500 hover:text-red-700">&times;</button>
                    </div>
                @else
                    <p class="text-red-500 text-sm mb-2">
                        Missing center for assigned centerId: {{ $assigned->centerId }}
                    </p>
                @endif
            @endforeach

        @endif
    </div>
</div>

<!-- Add More Button -->
<div class="mt-2">
    <button type="button" id="add-svc-btn" class="text-blue-500 hover:underline">
        + Add Center
    </button>
</div>



                <!-- EmpShop Dropdown -->

                <!-- Submit Button -->
                <div class="mt-6 text-left">
                     <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
   
                <!--     <button type="submit" class="bg-primary text-white px-4 py-2 rounded hover:bg-primary-dark transition"> -->
                        Update VisaType
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const addBtn = document.getElementById('add-svc-btn');
    const wrapper = document.getElementById('dev-svc-wrapper');

    // Pass PHP variables to JS
    const allCenters = @json($Center);
    const savedCenters = @json($VisaTypeTdd->pluck('centerId'));
    const defaultDays = @json($visaType->days ?? 7); // use $visaType->days or fallback 7

    addBtn.addEventListener('click', function () {
        allCenters.forEach(center => {
            if (!savedCenters.includes(center.id)) {
                const newRow = document.createElement('div');
                newRow.classList.add('flex', 'items-center', 'gap-2', 'mb-2', 'shop-group');

                const selectHtml = `
                    <select name="assigned_devs[]" class="w-1/2 px-3 py-2 border border-gray-300 rounded bg-yellow-300">
                        <option value="${center.id}" selected>${center.center_name}</option>
                    </select>
                `;

                const inputHtml = `
                    <input type="text" name="extra_inputs[]" placeholder="Enter days" value="${defaultDays}"
                        class="w-1/2 px-3 py-2 border border-gray-300 rounded">
                `;

                const removeBtnHtml = `
                    <button type="button" class="remove-shop-btn text-red-500 hover:text-red-700">&times;</button>
                `;

                newRow.innerHTML = selectHtml + inputHtml + removeBtnHtml;
                wrapper.appendChild(newRow);

                savedCenters.push(center.id);
            }
        });
    });

    // Remove row handler
    wrapper.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-shop-btn')) {
            const row = e.target.closest('.shop-group');
            const select = row.querySelector('select[name="assigned_devs[]"]');
            const removedId = parseInt(select.value);
            const index = savedCenters.indexOf(removedId);
            if (index > -1) savedCenters.splice(index, 1);
            row.remove();
        }
    });
});
</script>
@endpush


