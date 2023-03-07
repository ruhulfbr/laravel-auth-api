<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('jwt.verify', ['except' => ['login','register']]);
    }

    public function login(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            $response['response'] = $validator->messages();
            return response()->json([
                'status'        => 'error',
                'message'       => $validator->messages()
            ]);
        }


        $credentials = $request->only('email', 'password');

        $token = Auth::attempt($credentials);
        if (!$token) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = Auth::user();
        return response()->json([
            'status'        => 'success',
            'user'          => $user,
            'authorization' => [
                'token' => $token,
                'type'  => 'bearer',
            ]
        ]);

    }

    public function register(Request $request){

        $validator = \Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);

        if ($validator->fails()) {
            $response['response'] = $validator->messages();
            return response()->json([
                'status'        => 'error',
                'message'       => $validator->messages()
            ]);
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);


        $token = Auth::login($user);
        return response()->json([
            'status'        => 'success',
            'message'       => 'User created successfully',
            'user'          => $user,
            'authorization' => [
                'token' => $token,
                'type'  => 'bearer',
            ]
        ]);
    }

    public function logout()
    {
        //Auth::logout();

        $forever = true;
        \JWTAuth::getToken(); // Ensures token is already loaded.
        \JWTAuth::invalidate($forever);

        return response()->json([
            'status'  => 'success',
            'message' => 'Successfully logged out',
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'status'        => 'success',
            'user'          => Auth::user(),
            'authorization' => [
                'token' => Auth::refresh(),
                'type'  => 'bearer',
            ]
        ]);
    }

}