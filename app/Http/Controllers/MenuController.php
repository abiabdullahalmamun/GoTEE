<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;

class MenuController extends Controller
{
    /**
     * Display the menu management page.
     */
    public function index()
    {
        $data['parents'] = Menu::whereNull('parent_id')->get();

        $data['menus'] = Menu::select('id','name','title')->orderBy('order')->get();

        // 2️⃣ Submenus (Have a parent AND have child menus)
        $data['submenus'] = Menu::whereNotNull('parent_id')->whereHas('children')->get();

        // 3️⃣ Child Menus (Have a parent AND have no children)
        $data['childMenus'] = Menu::whereNotNull('parent_id')->whereDoesntHave('children')->get();

//dd($data);

        return view('pages.menus.index', $data);
    }

    /**
     * Store a new menu based on type.
     */
    public function store(Request $request)
    {
        // Validate input fields
        $request->validate([
            'title'     => 'nullable|string|max:255',
            'name'      => 'required|string|max:255',
            'icon'      => 'nullable|string|max:255',
            'route'     => 'nullable|string|max:255',
            'order'     => 'nullable|integer',
            'parent_id' => 'nullable|exists:menus,id', // Parent menu must exist in DB or be null
        ]);

        // Create new menu
        Menu::create([
            'title'     => $request->title,
            'name'      => $request->name,
            'icon'      => $request->icon,
            'route'     => $request->route,
            'order'     => $request->order ?? 0, // Default to 0 if not provided
            'parent_id' => $request->parent_id, // Can be null
            'is_active'=>1
        ]);

        // Redirect with success message
        return redirect()->route('menus.index')->with('success', 'Menu created successfully.');
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'route' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
            'parent_id' => 'nullable|exists:menus,id'
        ]);

        $menu = Menu::findOrFail($id);
        $menu->update([
            'title' => $request->title,
            'name' => $request->name,
            'icon' => $request->icon,
            'route' => $request->route,
            'order' => $request->order,
            'parent_id' => $request->parent_id,
        ]);

        return redirect()->back()->with('success', 'Menu updated successfully!');
    }

    public function destroy($id)
    {
        $menu = Menu::findOrFail($id);
        $menu->delete();

        return redirect()->back()->with('success', 'Menu deleted successfully.');
    }


}
