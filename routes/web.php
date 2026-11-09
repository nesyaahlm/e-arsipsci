<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/uji-layout-user', function () {
    abort_unless(app()->isLocal(), 404);

    Auth::login(User::where('email', 'siti.r@sucofindo.co.id')->firstOrFail());

    return view('user.layout-test');
});