<?php

// app/Traits/Loggable.php
namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

trait Loggable
{
    public static function bootLoggable()
    {
        static::updated(function ($model) {
            $ignoredFields = [];

            if ($model instanceof \App\Models\User) {
                $ignoredFields = [
                    'last_login_at',
                    'remember_token',
                    'is_active',
                    'login_attempts',
                    'last_login_ip',
                    'updated_at',
                ];
            } elseif ($model instanceof \App\Models\AppReceive) {
                $ignoredFields = [
                    'stepId',
                    'updated_at',
                ];
            }

            $changed = array_keys($model->getChanges());

            if ($ignoredFields && count(array_diff($changed, $ignoredFields)) === 0) {
                return;
            }

            static::logChange($model, 'updated');
        });

        static::deleted(function ($model) {
            $excludedModels = [
                \App\Models\PlayAudio::class,
                \App\Models\CurrentQueue::class,
                \App\Models\TempImport::class,
            ];

            if (in_array(get_class($model), $excludedModels, true)) {
                return;
            }

            static::logChange($model, 'deleted');
        });
    }

    protected static function logChange($model, string $action)
    {
        if ($action === 'updated') {
            $newValues = $model->getChanges();
            $oldValues = array_intersect_key(
                $model->getOriginal(),
                $newValues
            );
        } elseif ($action === 'deleted') {
            $oldValues = $model->getOriginal();
            $newValues = null;
        } else {
            $oldValues = null;
            $newValues = $model->getAttributes();
        }

        AuditLog::create([
            'action' => $action,
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'user_id' => Auth::id() ?? null,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
        ]);
    }

    public static function logAuthEvent(string $eventName, array $data = [])
    {
        AuditLog::create([
            'action' => $eventName,
            'model_type' => \App\Models\User::class,
            'model_id' => Auth::id(),
            'old_values' => $data['old_values'] ?? null,
            'new_values' => $data['new_values'] ?? null,
            'user_id' => Auth::id() ?? null,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
        ]);
    }
}

