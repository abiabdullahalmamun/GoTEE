<tr class="border {{ $level == 1 ? 'parent' : ($level == 2 ? 'submenu' : 'child') }}"
    data-menu-id="{{ $menu->id }}"
    data-level="{{ $level }}"
    data-parent-id="{{ $menu->parent_id ?? '' }}"
>
    <td class="text-left" style="padding-left: {{ $level * 20 }}px;">
        @if(count($menu->childrenRecursive) > 0)
            <button type="button" class="toggle-children font-bold text-blue-600" data-menu-id="{{ $menu->id }}">+</button>
            <span class="font-bold text-primary">
                {{ $menu->name }}
            </span>
        @else
            <button type="button" class="text-gray-700">-</button>
            <span class="">
                {{ $menu->name }}
            </span>
        @endif
    </td>

    @foreach(['can_view', 'can_create', 'can_edit', 'can_delete'] as $perm)
        <td class="px-2 py-2">
            <input type="checkbox" name="permissions[{{ $menu->id }}][{{ $perm }}]" class="{{ $perm }}" data-menu-id="{{ $menu->id }}">
        </td>
    @endforeach
</tr>

@foreach($menu->childrenRecursive as $child)
    @include('pages.role_permissions.menu_item', ['menu' => $child, 'level' => $level + 1])
@endforeach
