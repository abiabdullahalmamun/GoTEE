@extends('layouts.app')

@section('content')
    <div class="mx-auto ">
        <div class="max-w-full mx-auto">
            <x-page-header title="Edit User" />
        </div>

        <div class="bg-white shadow rounded p-6 mt-1 max-w-full mx-auto">
            <form action="{{ route('app-receive.update', $data->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Name Field -->
                    <div class="w-full">
                        <label for="name" class="block text-gray-700 font-semibold mb-1">Webfile</label>
                         <input type="text" id="recId" name="recId" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring focus:ring-blue-300 focus:border-blue-500" value="{{ $data->id }}" readonly required hidden>
                        <input type="text" id="Webfile" name="Webfile" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring focus:ring-blue-300 focus:border-blue-500" value="{{ $data->Webfile }}" readonly required>

                        @error('')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email Field (Readonly) -->
                    <div class="w-full">
                        <label for="email" class="block text-gray-700 font-semibold mb-1">Name</label>
                         <input type="text" id="name" name="name" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring focus:ring-blue-300 focus:border-blue-500" value="{{ $data->ApplicantName }}"   required>
                        
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
                        <label for="role" class="block text-gray-700 font-semibold mb-1">Sticker Type</label>
                    
                    <select name="stickertype" id="stickertype"
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:ring focus:ring-blue-300 focus:border-blue-500" required>
                    <option value="">All</option>
                    @foreach($stickers as $itm)
                        <option value="{{ $itm->id }}" {{ (int)$itm->id === (int)$data->stickertype ? 'selected' : '' }}  > {{ $itm->sticker }}
                        </option>
                    @endforeach
                </select>

                </div>

                     <div class="w-full">
                        <label for="email" class="block text-gray-700 font-semibold mb-1">stickerNo</label>
                         <input type="text" id="stickerNo" name="stickerNo" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring focus:ring-blue-300 focus:border-blue-500" value="{{ $data->stickerNo }}"   required>
                        
                    </div>
       

                    <!-- Confirm Password Field -->
                    
                    
                    <div class="w-full">
                        <label for="role" class="block text-gray-700 font-semibold mb-1">VisaType</label>
                    
                    <select name="visatype" id="visatype"
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:ring focus:ring-blue-300 focus:border-blue-500" required>
                    <option value="">All</option>
                    @foreach($visaType as $itm)
                        <option value="{{ $itm->id }}" {{ (int)$itm->id === (int)$data->visatype ? 'selected' : '' }}  > {{ $itm->visa_type }}
                        </option>
                    @endforeach
                </select>

                </div>
                <div class="w-full">
                    <label for="role" class="block text-gray-700 font-semibold mb-1">Correction Fee</label>
                    
                  <input type="number" id="corrfee" name="corrfee" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring focus:ring-blue-300 focus:border-blue-500" value="{{ $data->corrFee ?? 0 }}" required>

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
