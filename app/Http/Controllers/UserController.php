<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function deleteAccount(): RedirectResponse
    {
        $user = User::find(Auth::user()->id);

        Auth::logout();

        $user->delete();

        return redirect('/login')->with('status', 'Account deleted successfully.');
    }
}
