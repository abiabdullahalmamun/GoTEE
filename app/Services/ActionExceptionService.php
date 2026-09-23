<?php

namespace App\Services;

use App\Models\ActionExcept;  

class ActionExceptionService
{
    public static function store(array $data): ActionExcept
    {
        return ActionExcept::create([
            'Date'  => date('Y-m-d'),
            'module'      => $data['module'] ?? null,
            'action'      => $data['action'] ?? null,
            'remarks' => $data['remarks'] ?? null,
            'userId'     => $data['user_id'] ?? auth()->id(),
            'centerId'    => $data['centerId'],
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
            'ip_address'  => request()->ip(),
        ]);
    }
}

