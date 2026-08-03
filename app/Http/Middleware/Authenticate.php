<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if (! $request->expectsJson()) {
            session()->flash('swal', [
                'title' => 'Akses Ditolak',
                'text' => 'Anda harus login terlebih dahulu untuk mengakses menu ini.',
                'icon' => 'warning'
            ]);
            return route('login');
        }
        return null;
    }
}