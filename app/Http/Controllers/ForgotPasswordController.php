<?php

namespace App\Http\Controllers;

use App\Models\ResetCodePassword;
use App\Models\User;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    public function index(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'email' => 'required|email|exists:users',
        ]);

        if ($validator->fails()) {
            $response['response'] = $validator->messages();
            return response()->json([
                'status'  => 'error',
                'message' => $validator->messages()
            ], 411);
        }

        ResetCodePassword::where('email', $request->email)->delete();

        $codeData = ResetCodePassword::create([
            'email'      => request()->email,
            'code'       => mt_rand(100000, 999999),
            'created_at' => now()
        ]);

        //Mail::to($request->email)->send(new SendCodeResetPassword($codeData->code));

        return response()->json([
            'status'  => 'success',
            'message' => 'Reset Password Code Sent',
        ]);
    }

    public function checkCode(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'code' => 'required|string|exists:reset_code_passwords',
        ]);

        if ($validator->fails()) {
            $response['response'] = $validator->messages();
            return response()->json([
                'status'  => 'error',
                'message' => $validator->messages()
            ], 411);
        }

        return response()->json([
            'status'  => 'success',
            'message' => "Reset code is valid"
        ]);
    }

    public function resetPassword(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'code'     => 'required|string|exists:reset_code_passwords',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            $response['response'] = $validator->messages();
            return response()->json([
                'status'  => 'error',
                'message' => $validator->messages()
            ], 411);
        }

        $passwordReset = ResetCodePassword::firstWhere('code', $request->code);
        $user          = User::firstWhere('email', $passwordReset->email);

        $user->password = \Hash::make($request->password);
        $user->save();

        $passwordReset->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Successfully Reset',
        ]);
    }
}
