<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{    
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }
    //
    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            "username"  => 'required|string',
            'password'  => 'required|string'
        ]);
        if($validator->fails()) {
            return response()->
            json([
                'status'    => 401,
                'message'   => $validator->errors()
            ], 401);
        }
        $credentials = $request->only('username','password');
        $token = Auth::attempt($credentials);
        if(!$token) {
            return response()->json([
                'message'   => 'Unauthorized'
            ], 401);
        }
        $token = encrypt($token);
        return response()->json([
            'token'=> $token
        ]);
    }
}
