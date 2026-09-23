@props(['id', 'title'])

<div id="{{ $id }}" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden z-[1100]">
    <div class="bg-white p-6 rounded-lg shadow-lg w-1/2">
        <!-- Modal Header -->
        <div class="flex justify-between items-center border-b pb-3">
            <h2 class="text-lg font-bold">{{ $title }}</h2>
            <button onclick="closeModal('{{ $id }}')" class="text-gray-600 hover:text-gray-900">&times;</button>
        </div>

        <!-- Modal Content -->
        <div class="mt-4">
            {{ $slot }}
        </div>

        <!-- Modal Footer (Optional) -->
        @isset($footer)
            <div class="mt-4 border-t pt-3 flex justify-end">
                {{ $footer }}
            </div>
        @endisset
    </div>
</div>
