@if ($paginator->lastPage() > 1)
    <div class="flex justify-between items-center mt-4 flex-wrap gap-2">
        <!-- Left Side: Total Users & Per Page Dropdown -->
        <div class="flex items-center space-x-4 min-w-[200px]">
            <select id="perPage" onchange="window.location.href='?per_page='+this.value"
                    class="border border-gray-300 rounded px-3 py-1 min-w-[80px] focus:ring focus:ring-label-primary">
                @foreach ($perPageOptions as $option)
                    <option value="{{ $option }}" {{ request('per_page') == $option ? 'selected' : '' }}>
                        {{ $option }}
                    </option>
                @endforeach
            </select>
            <span class="text-gray-700">Total: {{ $paginator->total() }}</span>
        </div>

        <!-- Right Side: Custom Pagination -->
        <div class="flex justify-end">
            <nav class="flex space-x-2">
                <!-- Previous Page -->
                @if ($paginator->previousPageUrl())
                    <a href="{{ $paginator->previousPageUrl() }}"
                       class="w-8 h-8 flex items-center justify-center border border-gray-300 rounded-full bg-gray-200 hover:bg-gray-400 text-gray-700 transition">
                        &laquo;
                    </a>
                @endif

                <!-- Page Numbers -->
                @for ($i = 1; $i <= $paginator->lastPage(); $i++)
                    <a href="{{ $paginator->url($i) }}"
                       class="w-8 h-8 flex items-center justify-center border border-gray-300 rounded-full transition
                              {{ $paginator->currentPage() == $i ? 'bg-primary text-white' : 'bg-gray-200 hover:bg-gray-400 text-gray-700' }}">
                        {{ $i }}
                    </a>
                @endfor

                <!-- Next Page -->
                @if ($paginator->nextPageUrl())
                    <a href="{{ $paginator->nextPageUrl() }}"
                       class="w-8 h-8 flex items-center justify-center border border-gray-300 rounded-full bg-gray-200 hover:bg-gray-400 text-gray-700 transition">
                        &raquo;
                    </a>
                @endif
            </nav>
        </div>
    </div>
@endif
