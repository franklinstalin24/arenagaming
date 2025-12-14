<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        // Adjust the view as needed; ensure resources/views/auth/login.blade.php exists
        return view('auth.login');
    }
}