<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    // REGISTER USER
    public function register(Request $request)
    {
        if ($request->role === 'admin') {
            // Admin registration
            $request->validate([
                'name'      => 'required|string|max:255',
                'email'     => 'required|email|unique:users,email',
                'password'  => 'required|string|min:6|confirmed', // expects password_confirmation
                'campus'    => 'required|in:echague,jones,angadanan,santiago',
                'role'      => 'required|in:admin',
            ]);

            $user = User::create([
                'name'      => $request->name,
                'email'     => $request->email,
                'password'  => Hash::make($request->password),
                'campus'    => $request->campus,
                'role'      => 'admin',
            ]);

        } else {
            // Student registration
            $request->validate([
                'name'      => 'required|string|max:255',
                'id_number' => 'required|string|unique:users',
                'lrn'       => 'required|string',
                'campus'    => 'required|in:echague,jones,angadanan,santiago',
                'role'      => 'required|in:student',
            ]);

            $user = User::create([
                'name'      => $request->name,
                'id_number' => $request->id_number,
                'lrn'       => Hash::make($request->lrn),
                'campus'    => $request->campus,
                'role'      => 'student',
            ]);
        }

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user
        ]);
    }

    // LOGIN
    public function login(Request $request)
    {
        $request->validate([
            'role' => 'required|in:admin,student',
            'email' => 'required_if:role,admin|email',
            'password' => 'required_if:role,admin|string',
            'id_number' => 'required_if:role,student|string',
            'lrn' => 'required_if:role,student|string',
            'campus' => 'required|string',
        ]);

        if ($request->role === 'admin') {
            $user = User::where([
                ['email', $request->email],
                ['role', 'admin'],
                ['campus', $request->campus],
            ])->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json(['message' => 'Invalid admin credentials'], 401);
            }

        } else {
            $user = User::where([
                ['id_number', $request->id_number],
                ['role', 'student'],
                ['campus', $request->campus],
            ])->first();

            if (!$user || !Hash::check($request->lrn, $user->lrn)) {
                return response()->json(['message' => 'Invalid student credentials'], 401);
            }
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'username' => $request->role === 'admin' ? $user->email : $user->id_number,
                'full_name' => $user->name,
                'role' => $user->role,
                'campus' => $user->campus,
            ]
        ]);
    }

    // LOGOUT
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }

    // GET CURRENT USER
    public function user(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'id'           => $user->id,
            'username'     => $user->id_number ?? $user->email,
            'full_name'    => $user->name,
            'role'         => $user->role,
            'campus'       => $user->campus,
            'profile_image'=> $user->profile_image ? asset('storage/' . $user->profile_image) : null,
        ]);
    }

    // UPLOAD PROFILE IMAGE
    public function uploadProfileImage(Request $request)
    {
        $request->validate([
            'profile_image' => 'required|image|max:2048', // max 2MB
        ]);

        $user = $request->user();

        if ($user->profile_image) {
            Storage::delete($user->profile_image);
        }

        $path = $request->file('profile_image')->store('profile_images');
        $user->profile_image = $path;
        $user->save();

        return response()->json([
            'message' => 'Profile image uploaded successfully',
            'profile_image' => asset('storage/' . $path)
        ]);
    }
}
