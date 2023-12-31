<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        //return view('home');
        if (auth()->user()->hasRole('Admin')) {
            $user = User::find(Auth::user()->id);

        return view('Admin.profile.admin_profile', compact('user'));
        //return view('home');

    } else {
            $user = User::find(Auth::user()->id);
        return view('Admin.profile.user_profile', compact('user'));
    }
    }
}