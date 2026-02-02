<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReqisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function __invoke(ReqisterRequest $request)
    {
        $user = User::create($request->only('name', 'email') + [
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        $request->session()->regenerate();
    }
}
