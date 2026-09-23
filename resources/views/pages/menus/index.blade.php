@extends('layouts.app')
@push('style')

@endpush
@section('content')
    <div class="mx-auto">
        <x-page-header title="Manage Menus" />

        <div class="bg-white shadow rounded p-6 mt-1 mx-auto">

            <!-- Top Section: Add Menu & Search -->
            <div class="flex justify-between items-center mb-4">
                <button id="openModal" class="bg-primary text-white px-4 py-2 rounded hover:bg-primary-dark">
                    + Add New Menu
                </button>

                <input type="text" id="searchInput" placeholder="Search..."
                       class="px-3 py-2 border border-gray-300 rounded w-1/3 focus:outline-none focus:ring-2 focus:ring-primary">
            </div>

            <!-- Tabs -->
            <div class="flex space-x-4 border-b">
                <button class="tab-button active bg-primary text-white px-4 py-2 rounded-t" data-type="parent">Parent Menus</button>
                <button class="tab-button bg-gray-200 text-gray-700 px-4 py-2 rounded-t" data-type="submenu">Submenus</button>
                <button class="tab-button bg-gray-200 text-gray-700 px-4 py-2 rounded-t" data-type="child">Child Menus</button>
            </div>

            <!-- Menu Lists -->
            <div class="mt-4">
                <!-- Parent Menus -->
                <div id="parent-menu-list" class="menu-list overflow-y-hidden overflow-x-auto ">
                    <table class="w-full border border-gray-200 text-sm">
                        <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-4 py-2 text-left">ID</th>
                            <th class="border px-4 py-2 text-left">Name</th>
                            <th class="border px-4 py-2 text-right">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($parents as $menu)
                            <tr class="border hover:bg-gray-50 transform transition-all duration-500 ease-out row-animation">
                                <td class="border px-4 py-1">{{ $menu->id }}</td>
                                <td class="border px-4 py-1">{{ $menu->name }}</td>
                                <td class="border px-4 py-1 text-right">
                                    <button onclick="viewMenu({{ $menu }})" class="bg-primary text-white px-2 py-1  rounded">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button onclick="editMenu({{ $menu }})" class="bg-gray-300 hover:bg-yellow-500 text-white px-2 py-1 mx-1 rounded">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form id="deleteForm-{{ $menu->id }}" action="{{ route('menus.destroy', $menu->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete({{ $menu->id }})" class="bg-gray-300 hover:bg-red-500 text-white px-2 py-1 rounded">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Submenus -->
                <div id="submenu-menu-list" class="menu-list hidden overflow-y-hidden overflow-x-auto ">
                    <table class="w-full border border-gray-200 text-sm">
                        <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-4 py-2 text-left">ID</th>
                            <th class="border px-4 py-2 text-left">Name</th>
                            <th class="border px-4 py-2 text-left">Parent</th>
                            <th class="border px-4 py-2 text-right">Action</th>

                        </tr>
                        </thead>
                        <tbody>
                        @foreach($submenus as $menu)
                            <tr class="border hover:bg-gray-50 transform  transition-all duration-500 ease-out row-animation">
                                <td class="border px-4 py-1">{{ $menu->id }}</td>
                                <td class="border px-4 py-1">{{ $menu->name }}</td>
                                <td class="border px-4 py-1">{{ optional($menu->parent)->name ?? '-' }}</td>
                                <td class="border px-4 py-1 text-right">
                                    <button onclick="viewMenu({{ $menu }})" class="bg-primary text-white px-2 py-1  rounded">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button onclick="editMenu({{ $menu }})" class="bg-gray-300 hover:bg-yellow-500 text-white px-2 py-1 mx-1 rounded">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form id="deleteForm-{{ $menu->id }}" action="{{ route('menus.destroy', $menu->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete({{ $menu->id }})" class="bg-gray-300 hover:bg-red-500 text-white px-2 py-1 rounded">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Child Menus -->
                <div id="child-menu-list" class="menu-list hidden overflow-y-hidden overflow-x-auto ">
                    <table class="w-full border border-gray-200 text-sm">
                        <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-4 py-2 text-left">ID</th>
                            <th class="border px-4 py-2 text-left">Name</th>
                            <th class="border px-4 py-2 text-left">Parent</th>
                            <th class="border px-4 py-2 text-right ">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($childMenus as $menu)
                            <tr class="border hover:bg-gray-50 transform  transition-all duration-500 ease-out row-animation">
                                <td class="border px-4 py-1">{{ $menu->id }}</td>
                                <td class="border px-4 py-1">{{ $menu->name }}</td>
                                <td class="border px-4 py-1">{{ optional($menu->parent)->name ?? '-' }}</td>
                                <td class="border px-4 py-1 text-right">
                                    <button onclick="viewMenu({{ $menu }})" class="bg-primary text-white px-2 py-1  rounded">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button onclick="editMenu({{ $menu }})" class="bg-gray-300 hover:bg-yellow-500 text-white px-2 py-1 mx-1 rounded">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form id="deleteForm-{{ $menu->id }}" action="{{ route('menus.destroy', $menu->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete({{ $menu->id }})" class="bg-gray-300 hover:bg-red-500 text-white px-2 py-1 rounded">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>

                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Menu Modal -->
    <div id="menuModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden z-[1100]">
        <div class="bg-white p-6 rounded w-96 relative">
            <h2 class="text-lg font-bold mb-4" id="modalTitle">Create Menu</h2>
            <form action="{{ route('menus.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label for="menu_title" class="block text-gray-700">Menu Title</label>
                    <input type="text" name="title" id="menu_title" class="w-full px-3 py-2 border rounded" required>
                </div>

                <div class="mb-4">
                    <label for="menu_name" class="block text-gray-700">Menu Name</label>
                    <input type="text" name="name" id="menu_name" class="w-full px-3 py-2 border rounded" required>
                </div>

                <div class="mb-4">
                    <label for="menu_icon" class="block text-gray-700">Menu Icon</label>
                    <input type="text" name="icon" id="menu_icon" class="w-full px-3 py-2 border rounded" required>
                </div>

                <div class="mb-4">
                    <label for="menu_route" class="block text-gray-700">Menu Route</label>
                    <input type="text" name="route" id="menu_route" class="w-full px-3 py-2 border rounded" >
                </div>

                <div class="mb-4">
                    <label for="menu_order" class="block text-gray-700">Menu Order</label>
                    <input type="number" name="order" id="menu_order" class="w-full px-3 py-2 border rounded" required>
                </div>

                <div class="mb-4">
                    <label for="parent_id" class="block text-gray-700">Select Parent (Optional)</label>
                    <select name="parent_id" id="parent_id" class="w-full px-3 py-2 border rounded">
                        <option value="">-- No Parent --</option>
                        @foreach($menus as $dd)
                            <option value="{{ $dd->id }}">{{ $dd->name }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="bg-primary text-white px-4 py-2 rounded hover:bg-blue-600">Save</button>
                <button type="button" class="ml-2 bg-gray-400 text-white px-4 py-2 rounded" id="closeModal">Cancel</button>
            </form>
        </div>
    </div>


    <!-- Edit Menu Modal -->
    <div id="editModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden z-[1100]">
        <div class="bg-white p-6 rounded w-96">
            <h2 class="text-lg font-bold mb-4">Edit Menu</h2>
            <form id="editMenuForm" method="POST">
                @csrf
                @method('PUT')

                <input type="hidden" id="edit_menu_id">

                <div class="mb-4">
                    <label for="edit_menu_title" class="block text-gray-700">Menu Title</label>
                    <input type="text" id="edit_menu_title" name="title" class="w-full px-3 py-2 border rounded" required>
                </div>

                <div class="mb-4">
                    <label for="edit_menu_name" class="block text-gray-700">Menu Name</label>
                    <input type="text" id="edit_menu_name" name="name" class="w-full px-3 py-2 border rounded" required>
                </div>

                <div class="mb-4">
                    <label for="edit_menu_icon" class="block text-gray-700">Menu Icon</label>
                    <input type="text" id="edit_menu_icon" name="icon" class="w-full px-3 py-2 border rounded">
                </div>

                <div class="mb-4">
                    <label for="edit_menu_route" class="block text-gray-700">Menu Route</label>
                    <input type="text" id="edit_menu_route" name="route" class="w-full px-3 py-2 border rounded">
                </div>

                <div class="mb-4">
                    <label for="edit_menu_order" class="block text-gray-700">Menu Order</label>
                    <input type="number" id="edit_menu_order" name="order" class="w-full px-3 py-2 border rounded" required>
                </div>

                <div class="mb-4">
                    <label for="edit_parent_id" class="block text-gray-700">Select Parent (Optional)</label>
                    <select id="edit_parent_id" name="parent_id" class="w-full px-3 py-2 border rounded">
                        <option value="">-- No Parent --</option>
                        @foreach($menus as $dd)
                            <option value="{{ $dd->id }}">{{ $dd->name }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="bg-primary text-white px-4 py-2 rounded hover:bg-primary-dark">Update</button>
                <button type="button" class="ml-2 bg-gray-400 text-white px-4 py-2 rounded" id="closeEditModal">Cancel</button>
            </form>
        </div>
    </div>

    <!-- View Menu Modal -->
    <div id="viewModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden z-[1100]">
        <div class="bg-white p-6 rounded w-96 relative">
            <h2 class="text-lg font-bold mb-4">View Menu</h2>
            <form>
                <div class="mb-4">
                    <label class="block text-gray-700">Menu Title</label>
                    <input type="text" id="view_title" class="w-full px-3 py-2 border rounded bg-gray-100" readonly>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700">Menu Name</label>
                    <input type="text" id="view_name" class="w-full px-3 py-2 border rounded bg-gray-100" readonly>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700">Menu Icon</label>
                    <input type="text" id="view_icon" class="w-full px-3 py-2 border rounded bg-gray-100" readonly>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700">Menu Route</label>
                    <input type="text" id="view_route" class="w-full px-3 py-2 border rounded bg-gray-100" readonly>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700">Menu Order</label>
                    <input type="text" id="view_order" class="w-full px-3 py-2 border rounded bg-gray-100" readonly>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700">Parent Menu</label>
                    <input type="text" id="view_parent" class="w-full px-3 py-2 border rounded bg-gray-100" readonly>
                </div>

                <button type="button" class="mt-4 bg-gray-400 text-white px-4 py-2 rounded" id="closeViewModal">Close</button>
            </form>
        </div>
    </div>




@endsection

@push('scripts')
    @vite(['resources/js/scripts/menu.js'])
      @vite(['resources/js/scripts/index_script.js'])
@endpush
