@extends('layouts.app')

@section('content')
    <div class="mx-auto ">
        <div class="max-w-full mx-auto">
            <x-page-header title="Edit Form Data" />
        </div>

        <div class="bg-white shadow rounded p-6 mt-1 max-w-full mx-auto">
            <form action="{{ route('form-fill.update', $data->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Name Field -->
                    <div class="w-full">
                        <label for="name" class="block text-gray-700 font-semibold mb-1">Webfile</label>
                         <input type="text" id="recId" name="recId" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring focus:ring-blue-300 focus:border-blue-500" value="{{ $data->id }}" readonly required hidden>
                        <input type="text" id="Webfile" name="Webfile" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring focus:ring-blue-300 focus:border-blue-500" value="{{ $data->webfile }}" readonly required>

                        @error('')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email Field (Readonly) -->
                    <div class="w-full">
                        <label for="email" class="block text-gray-700 font-semibold mb-1">Name</label>
                         <input type="text" id="name" name="name" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring focus:ring-blue-300 focus:border-blue-500" value="{{ $data->Name }}"   required>
                        
                    </div>
                    <div class="w-full">
                        <label for="email" class="block text-gray-700 font-semibold mb-1">Passport</label>
                         <input type="text" id="passport" name="passport" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring focus:ring-blue-300 focus:border-blue-500" value="{{ $data->passport }}"   required>
                        
                    </div>
                   <div class="w-full">
                        <label for="email" class="block text-gray-700 font-semibold mb-1">Contact</label>
                         <input type="text" id="contact" name="contact" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring focus:ring-blue-300 focus:border-blue-500" value="{{ $data->contact }}"   required>
                        
                    </div>
        
 
                <div class="w-full">
                    <label for="role" class="block text-gray-700 font-semibold mb-1"> Fee</label>
                    
                  <input type="number" id="corrfee" name="corrfee" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring focus:ring-blue-300 focus:border-blue-500" value="{{ $data->fee ?? 0 }}" required>

                </div>
                <!-- Submit Button -->
              
              <div class="mt-6 flex justify-end">
                <button type="submit"
                    class="bg-primary text-white px-4 py-2 rounded hover:bg-primary-dark transition">
                    Update File
                </button>
            </div>
            </form>
        </div>
    </div>
@endsection
