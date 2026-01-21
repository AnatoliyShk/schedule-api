<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReqisterRequest;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Hash;


class RegisterController extends Controller
{
    public function __invoke(ReqisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('laravel_api_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user
        ], Response::HTTP_CREATED);
    }
}
