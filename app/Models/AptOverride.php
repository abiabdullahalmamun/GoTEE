<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AptOverride extends Model
{
    use Loggable;
    use HasFactory;

    protected $table = 'tbl_apt_override';
    protected $fillable = ['Date', 'WebFile_no', 'visatypeId', 'centerId', 'remarks','created_by','in_ipadd','kioskId','checked_at','ipadd','type','phone','svcId','active','approvedBy','approvedAt'];

    /**
     * Get the parent menu.
     */

    public function userApp()
    {
        return $this->belongsTo(User::class, 'approvedBy');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function visa()
    {
        return $this->belongsTo(VisaTypeApt::class, 'visatypeId');
    }
    public function center()
    {
        return $this->belongsTo(Center::class, 'centerId');
    }
    public function kiosk()
    {
        return $this->belongsTo(Device::class, 'kioskId');
    }
    // public function parent()
    // {
    //     return $this->belongsTo(Menu::class, 'parent_id');
    // }

    // /**
    //  * Get all child menus (submenus or child menus).
    //  */
    // public function children()
    // {
    //     return $this->hasMany(Menu::class, 'parent_id');
    // }

    // /**
    //  * Scope to get only Parent Menus (menus that have no parent).
    //  */
    // public function scopeParents($query)
    // {
    //     return $query->whereNull('parent_id');
    // }

    // /**
    //  * Scope to get only Submenus (menus that have children).
    //  */
    // public function scopeSubmenus($query)
    // {
    //     return $query->whereHas('children');
    // }

    /**
    //  * Scope to get only Child Menus (menus that have a parent but are not parents themselves).
    //  */
    // public function scopeChildMenus($query)
    // {
    //     return $query->whereNotNull('parent_id')->whereDoesntHave('children');
    // }


    // // Recursive relationship for nested menus
    // public function childrenRecursive()
    // {
    //     return $this->hasMany(Menu::class, 'parent_id')
    //         ->with('childrenRecursive') // Ensure children are loaded recursively
    //         ->orderBy('order', 'asc');
    // }

    // // Relationship: Permissions
    // public function permissions()
    // {
    //     return $this->hasMany(RolePermission::class, 'menu_id');
    // }
}
