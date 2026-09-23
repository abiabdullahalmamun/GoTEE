<?php

use Illuminate\Support\Facades\Auth;

if (!function_exists('authUser')) {
    /**
     * Get the authenticated user from web or sanctum guard.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable
     */
    function authUser() {
        return Auth::guard('web')->user() ?? Auth::guard('sanctum')->user();
    }
}
