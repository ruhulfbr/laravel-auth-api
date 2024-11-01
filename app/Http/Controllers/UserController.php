<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct()
    {
    }

    public function index(Request $request)
    {
        $perPage = $request->input('results', 10);
        if (!is_numeric($perPage)) {
            $perPage = 10;
        }

        $users = User::paginate($perPage);

        return response()->json([
            'status' => 'success',
            'users'  => $users
        ]);
    }
}
