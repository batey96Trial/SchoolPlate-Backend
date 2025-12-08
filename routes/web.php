<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/user', function (Request $request) {
    // $donor = Child::create([
    //     'name'=>'aMINA',
    //     'school'=>'UB',
    //     'telephone'=>'681248724',
    //     'company'=>'Batcal'
    // ]);
    // dump($donor);
    $admin =  new Admin();

});

Route::get('/login', function (Request $request) {
    $user = User::findOrFail(1);
    Auth::guard('auth')->login($user);
    
    dump($user);
})->middleware('guest');



