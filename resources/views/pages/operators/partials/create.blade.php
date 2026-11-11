<form method="POST" id="createShopForm" action="{{ route('operators.store') }}">
    @csrf
    @method('POST')
    <div class="row">
 
<div class="flex flex-row gap-3 mb-3 w-full">

    {{-- Operator Type --}}
    <div class="flex-1 min-w-0">
        <div class="form-group">
            <label for="optype">Operator Type</label>
            <select class="form-control w-full px-3 py-2 border"
                    name="optyppe" required id="optype">
                <option value="1">IT</option>
                <option value="2">Operator</option>
                <option value="3">IE</option>
                <option value="4">Technician</option>
                <option value="5">QC Finish</option>
                <option value="6">QC End</option>
                <option value="7">QC Roaming</option>
            </select>
            @error('optyppe')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    {{-- Operator ID --}}
    <div class="flex-1 min-w-0">
        <div class="form-group">
            <label for="emp_id">Operator ID</label>
            <input type="text"
                   name="emp_id"
                   id="emp_id"
                   class="form-control w-full px-3 py-2 border rounded @error('emp_id') is-invalid @enderror"
                   value="{{ old('emp_id') }}"
                   placeholder="Enter Operator ID..."
                   required>
            @error('emp_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

</div>

        <div class="mb-3 col-12 col-md-4 col-lg-6">
            <div class="form-group">
                <label for="name">Operator Name</label><br><input type="text" name="emp_name"
                    class="form-control @error('emp_name') is-invalid @enderror w-full px-3 py-2 border rounded"
                    value="{{ old('emp_name') }}" placeholder="Enter Operator Name..." required>
                @error('emp_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

<div class="flex flex-row gap-3 mb-3 w-full">

    {{-- Designation --}}
    <div class="flex-1 min-w-0">
        <div class="form-group">
            <label for="designation">Designation</label>
            <select class="form-control w-full px-3 py-2 border"
                    name="designation" required id="designation">
                @foreach($designation as $item)
                    <option value="{{ $item['id'] }}">
                        {{ $item['designation'] }}
                    </option>
                @endforeach
            </select>
            @error('designation')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    {{-- Card ID --}}
    <div class="flex-1 min-w-0">
        <div class="form-group">
            <label for="card_id">Card ID</label>
            <input type="text"
                   name="card_id"
                   id="card_id"
                   class="form-control w-full px-3 py-2 border rounded @error('card_id') is-invalid @enderror"
                   value="{{ old('card_id') }}"
                   placeholder="Scan Card..."
                   required>
            @error('card_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

</div>

<div class="flex flex-row gap-3 mb-3 w-full">

    <div class="flex-1 min-w-0">
        <label for="grade">Grade</label>
        <select class="form-control w-full px-3 py-2 border"
                name="grade" required id="grade">
            @foreach($grade as $item)
                <option value="{{ $item['id'] }}">
                    {{ $item['gradeName'] }}
                </option>
            @endforeach
        </select>
        @error('grade')
            <div class="text-red-500 text-sm">{{ $message }}</div>
        @enderror
    </div>

    <div class="flex-1 min-w-0">
        <label for="gender1">Gender</label>
        <select class="form-control w-full px-3 py-2 border"
                name="gender1" required id="gender1">
            <option value="1">Male</option>
            <option value="2">Female</option>
            <option value="3">Others</option>
        </select>
        @error('gender1')
            <div class="text-red-500 text-sm">{{ $message }}</div>
        @enderror
    </div>

</div>

<div class="flex flex-row gap-3 mb-3 w-full">

    <div class="flex-1 min-w-0">
        <div class="form-group">
            <label for="dateofjoin">Date of Join</label>
            <input type="date"
                   name="dateofjoin"
                   id="dateofjoin"
                   class="form-control w-full px-3 py-2 border rounded @error('dateofjoin') is-invalid @enderror"
                   value="{{ old('dateofjoin', now()->format('Y-m-d')) }}"
                   required>
            @error('dateofjoin')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="flex-1 min-w-0">
        <div class="form-group">
            <label for="evaluation">Date of Evaluation</label>
            <input type="date"
                   name="evaluation"
                   id="evaluation"
                   class="form-control w-full px-3 py-2 border rounded @error('evaluation') is-invalid @enderror"
                   value="{{ old('evaluation', now()->format('Y-m-d')) }}"
                   required>
            @error('evaluation')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

</div>

<div class="flex flex-row gap-3 mb-3 w-full">

    {{-- Present Salary --}}
    <div class="flex-1 min-w-0">
        <div class="form-group">
            <label for="salary">Present Salary</label>
            <input type="number"
                   name="salary"
                   id="salary"
                   class="form-control w-full px-3 py-2 border rounded @error('salary') is-invalid @enderror"
                   value="{{ old('salary') }}"
                   >
            @error('salary')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    {{-- Remarks --}}
    <div class="flex-1 min-w-0">
        <div class="form-group">
            <label for="remarks">Remarks</label>
            <input type="text"
                   name="remarks"
                   id="remarks"
                   class="form-control w-full px-3 py-2 border rounded @error('remarks') is-invalid @enderror"
                   value="{{ old('remarks') }}" >
            @error('remarks')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

</div>

<div class="flex flex-row items-end justify-between gap-3 mb-4 w-full">

    {{-- Status --}}
    <div class="flex-1 min-w-0">
        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
            Status
        </label>

        <div class="flex items-center space-x-3">
            <label class="inline-flex items-center">
                <input type="radio" name="status" value="1"
                    class="form-radio text-green-600 focus:ring-green-500 @error('status') border-red-500 @enderror"
                    checked>
                <span class="ml-2 text-sm text-gray-700">Active</span>
            </label>

            <label class="inline-flex items-center">
                <input type="radio" name="status" value="0"
                    class="form-radio text-red-600 focus:ring-red-500 @error('status') border-red-500 @enderror">
                <span class="ml-2 text-sm text-gray-700">Inactive</span>
            </label>
        </div>

        @error('status')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Buttons --}}
    <div class="flex items-center gap-2">
        <button type="submit"
            class="bg-primary text-white px-4 py-2 rounded hover:bg-primary-dark">
            Save
        </button>

        <button type="button"
            class="bg-gray-400 text-white px-4 py-2 rounded"
            id="closeModal">
            Cancel
        </button>
    </div>

</div>
    </div>
</form>
