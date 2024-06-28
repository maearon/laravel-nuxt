<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountActivationsController extends Controller
{
    public function update(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        // Check if user exists and activation conditions are met
        if (!$user || $user->activated || !$user->authenticated('activation', $request->id)) {
            return response()->json(['error' => 'Invalid activation link'], 401);
        }

        // Activate user and generate tokens
        $user->activate();
        $user->generateTokens();

        return response()->json(['message' => 'User activated successfully'], 200);
    }
}
