<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();
        logger($user);

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Provided credentials are incorrect',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Logged in successfully',
            'token' => $user->createToken($request->device_name)->plainTextToken,
            'datum' => $user,
        ]);

    }
}
