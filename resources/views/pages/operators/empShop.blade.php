@extends('layouts.app')

@section('content')
    <div class="mx-auto ">
        <div class="max-w-full mx-auto">
            <x-page-header title="Edit User" />
        </div>

        <div class="bg-white shadow rounded p-6 mt-1 max-w-full mx-auto">
            <form action="{{ route('operators.updateAssign') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Name Field -->
                    <div class="w-full">
                        <label for="name" class="block text-gray-700 font-semibold mb-1">Name</label>
                        <input type="text" id="emp_id" name="emp_id" value="{{ $user->emp_id }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded focus:ring focus:ring-blue-300 focus:border-blue-500"
                               placeholder="Enter full name" required>
                        <input type="text" id="eid" name="eid" value="{{ $user->id }}"
                               hidden required>
                        @error('emp_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                       <div class="w-full">
                        <label for="name" class="block text-gray-700 font-semibold mb-1">Name</label>
                        <input type="text" id="Name" name="Name" value="{{ $user->emp_name}}"
                               class="w-full px-3 py-2 border border-gray-300 rounded focus:ring focus:ring-blue-300 focus:border-blue-500"
                               placeholder="Enter full name" required>
                        @error('emp_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
<br>
 <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <!-- Assigned Shops Dropdowns -->
<div class="w-full" id="emp-shops-wrapper">
    <label class="block text-gray-700 font-semibold mb-1">Assigned Shops</label>

    @foreach ($empShop as $index => $assigned)
        <div class="flex items-center gap-2 mb-2 shop-group">
            <select name="assigned_shops[]" class="w-full px-3 py-2 border border-gray-300 rounded bg-yellow-300">
                @foreach ($shop as $s)
                    <option value="{{ $s->id }}" {{ $assigned->shopId == $s->id ? 'selected' : '' }}>
                        {{ $s->ShopName }}
                    </option>
                @endforeach
            </select>
            <button type="button" class="remove-shop-btn text-red-500 hover:text-red-700">&times;</button>
        </div>
    @endforeach
</div>

<!-- Add More Button -->
<div class="mt-2">
    <button type="button" id="add-shop-btn" class="text-blue-500 hover:underline">
        + Add Shop
    </button>
</div>
</div>  

                <!-- EmpShop Dropdown -->



                <!-- Submit Button -->
                <div class="mt-6 text-right">
                    <button type="submit"
                            class="bg-primary text-white px-4 py-2 rounded hover:bg-primary-dark transition">
                        Update User
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const wrapper = document.getElementById('emp-shops-wrapper');
        const addBtn = document.getElementById('add-shop-btn');

        // Shop options from PHP passed as a string
        const shopOptions = `{!! collect($shop)->map(fn($s) => "<option value='{$s->id}'>{$s->ShopName}</option>")->implode('') !!}`;

        // Add new dropdown
        addBtn.addEventListener('click', function () {
            const div = document.createElement('div');
            div.classList.add('flex', 'items-center', 'gap-2', 'mb-2', 'shop-group');
            div.innerHTML = `
                <select name="assigned_shops[]" class="w-full px-3 py-2 border border-gray-300 rounded">
                    ${shopOptions}
                </select>
                <button type="button" class="remove-shop-btn text-red-500 hover:text-red-700 text-xl">&times;</button>
            `;
            wrapper.appendChild(div);
        });

        // Remove a dropdown
        wrapper.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-shop-btn')) {
                e.target.closest('.shop-group').remove();
            }
        });
    });
</script>
@endpush
