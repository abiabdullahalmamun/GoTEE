@extends('layouts.app')

@section('content')
    <div class="mx-auto ">
        <div class="max-w-full mx-auto">
            <x-page-header title="Manual  Receive" />
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
        </div>

        <div class="bg-white shadow rounded p-6 mt-1 max-w-full mx-auto">
            <form action="{{ route('manual-receive.store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Name Field -->
                    <div class="w-full">
                        <label for="name" class="block text-gray-700 font-semibold mb-1">Receive Date</label>
                       
                        <input type="date" class="form-control datepicker w-full px-3 py-2 border" name="rec_date" id="rec_date" data-date-format="yyyy/mm/dd" required autocomplete="off" value="{{ date('Y-m-d') }}" required>

                        @error('')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="w-full">
                        <label for="name" class="block text-gray-700 font-semibold mb-1">Webfile</label>
                       
                        <input type="text" id="Webfile" name="Webfile" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring focus:ring-blue-300 focus:border-blue-500" value=""  required>

                        @error('')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email Field (Readonly) -->
                    <div class="w-full">
                        <label for="email" class="block text-gray-700 font-semibold mb-1">Name</label>
                         <input type="text" id="name" name="name" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring focus:ring-blue-300 focus:border-blue-500" value=""   required>
                        
                    </div>
                    <div class="w-full">
                        <label for="email" class="block text-gray-700 font-semibold mb-1">Passport</label>
                         <input type="text" id="passport" name="passport" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring focus:ring-blue-300 focus:border-blue-500" value=""   required>
                        
                    </div>
                   <div class="w-full">
                        <label for="email" class="block text-gray-700 font-semibold mb-1">Contact</label>
                         <input type="text" id="contact" name="contact" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring focus:ring-blue-300 focus:border-blue-500" value=""   required>
                        
                    </div>
                     <div class="w-full">
                        <label for="role" class="block text-gray-700 font-semibold mb-1">Sticker Type</label>
                    
                    <select name="stickertype" id="stickertype"
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:ring focus:ring-blue-300 focus:border-blue-500" required>
                    <option value="">All</option>
                    @foreach($stickerList as $itm)
                        <option value="{{ $itm->id }}"> {{ $itm->sticker }}
                        </option>
                    @endforeach
                </select>

                </div>

                     <div class="w-full">
                        <label for="email" class="block text-gray-700 font-semibold mb-1">Sticker No.</label>
                         <input type="text" id="stickerNo" name="stickerNo" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring focus:ring-blue-300 focus:border-blue-500" value=""   required>
                        
                    </div>
       

                    <!-- Confirm Password Field -->
                    
                    
                    <div class="w-full">
                        <label for="role" class="block text-gray-700 font-semibold mb-1">VisaType</label>
                    
                    <select name="visatype" id="visatype"
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:ring focus:ring-blue-300 focus:border-blue-500" required>
                    <option value="">All</option>
                    @foreach($VisaType as $itm)
                        <option value="{{ $itm->id }}"  > {{ $itm->visa_type }}
                        </option>
                    @endforeach
                </select>

                </div>

                <!-- Submit Button -->
              
              <div class="mt-6 flex justify-end">
                <button type="submit"
                    class="bg-primary text-white px-4 py-2 rounded hover:bg-primary-dark transition">
                    Save File
                </button>
            </div>
            </form>
        </div>
    </div>
@endsection
