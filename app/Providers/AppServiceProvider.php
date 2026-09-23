<?php

namespace App\Providers;

use App\Models\CompanyInfo;
use App\Models\FooterLink;
use App\Models\Menu;
use App\Models\RolePermission;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {

        View::composer('*', function ($view) {
            $user = Auth::user();

            $masterMenu = [
                [
                    'id' => 0,
                    'name' => 'Dashboard',
                    'title' => 'Dashboard',
                    'icon' => 'fa fa-dashboard',
                    'url' => '/dashboard',
                ]
            ];
            $child = [];

            $menuList = [
                'master' => $masterMenu,
                'subMenu' => $child
            ];

            if ($user) {

                if ($user && $user->role && $user->role->is_access == 1) {
                    $permittedMenuIds = Menu::pluck('id')->toArray();
                }else{
                    $permittedMenuIds = RolePermission::where('role_id', $user->role_id)
                        ->where('can_view', 1)
                        ->pluck('menu_id')
                        ->toArray();
                }



                // Fetch permitted top-level menus
                $permittedMenus = Menu::whereIn('id', $permittedMenuIds)
                    ->whereNull('parent_id')
                    ->orderBy('order', 'asc')
                    ->get();


                foreach ($permittedMenus as $menu) {
                    $routeExists = !empty($menu->route) && Route::has($menu->route);
                    $url = $routeExists ? route($menu->route) : '#';

                    // Get Submenus (First Level Children)
                    $subMenuList = Menu::whereIn('id', $permittedMenuIds)
                        ->where('parent_id', $menu->id)
                        ->orderBy('order', 'asc')
                        ->get();

                    $masterMenu[] = [
                        'id' => $menu->id,
                        'name' => $menu->name,
                        'title' => $menu->title,
                        'icon' => $menu->icon,
                        'url' => $subMenuList->count() > 0 ? '#' : $url, // If submenus exist, keep #
                    ];

                    foreach ($subMenuList as $subMenu) {
                        $subMenuRouteExists = !empty($subMenu->route) && Route::has($subMenu->route);
                        $subMenuUrl = $subMenuRouteExists ? route($subMenu->route) : '#';

                        // Get Child Menus (Second Level Children)
                        $childMenuList = Menu::whereIn('id', $permittedMenuIds)
                            ->where('parent_id', $subMenu->id)
                            ->orderBy('order', 'asc')
                            ->get();

                        $childMenus = [];
                        foreach ($childMenuList as $childItem) {
                            $childRouteExists = !empty($childItem->route) && Route::has($childItem->route);
                            $childUrl = $childRouteExists ? route($childItem->route) : '#';

                            $childMenus[] = [
                                'name' => $childItem->name,
                                'icon' => $childItem->icon,
                                'url' => $childUrl
                            ];
                        }

                        $child[$menu->id][] = [
                            'name' => $subMenu->name,
                            'icon' => $subMenu->icon,
                            'url' => $subMenuUrl,
                            'child' => $childMenus // Nested children array
                        ];
                    }
                }

                // Final Menu List
                $menuList = [
                    'master' => $masterMenu,
                    'subMenu' => $child
                ];
            }
            $metaInfo = CompanyInfo::select('title','company_name as comName','about_us as aboutUs','address','phone','email','logo_url as logoUrl','signature_url as signatureURL','signature_url2 as signatureURL2')->orderby('id', 'desc')->first();
            if(!$metaInfo){
                CompanyInfo::create([
                    'title' => 'MMS',
                    'company_name' => 'MMS',
                    'about_us' => 'test description',
                    'logo_url' => null,
                    'created_at' => now(),
                    'created_by'=>1
                ]);
                $metaInfo = CompanyInfo::select('title','company_name as comName','about_us as aboutUs','address','phone','email','logo_url as logoUrl','signature_url as signatureURL','signature_url2 as signatureURL2')->orderby('id', 'desc')->first();
            }
            $menuList['metaInfo'] = $metaInfo;

            $menuList['linkList'] = FooterLink::select('id','title as title','link_url as linkUrl')->latest()->get();

            // Share the permittedMenus variable with all Blade views
            $view->with('menuList', $menuList);
        });
    }
}
