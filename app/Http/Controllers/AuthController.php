<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // REGISTER
    public function register(Request $request)
    {
        if ($request->role === 'admin') {
            $request->validate([
                'name' => 'required|string',
                'campus' => 'required|in:Echague,Angadanan,Jones,Ilagan',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:6',
                'role' => 'required|in:admin,student',
            ]);

            $user = User::create([
                'name' => $request->name,
                'campus' => $request->campus,
                'email' => $request->email,
                'role' => 'admin',
                'password' => bcrypt($request->password),
            ]);
        }

        if ($request->role === 'student') {
            $request->validate([
    'name' => 'required|string|max:255',
    'student_id' => 'required|unique:users,student_id',
    'lrn' => 'required|unique:users,lrn',
    'campus' => 'required|in:Echague,Angadanan,Jones,Ilagan',
    'password' => 'required|string|min:6|confirmed',
]);

User::create([
    'name' => $request->name,
    'student_id' => $request->student_id,
    'lrn' => $request->lrn,
    'campus' => $request->campus,
    'role' => 'student',
    'password' => bcrypt($request->password),
]);

        }

        return response()->json(['message' => 'User registered', 'user' => $user], 201);
    }

    // LOGIN
    public function login(Request $request)
    {
        $request->validate([
            'role' => 'required|in:admin,student',
            'email' => $request->role === 'admin' ? 'required|email' : 'nullable',
            'student_id' => $request->role === 'student' ? 'required' : 'nullable',
            'password' => 'required',
        ]);

        $credentials = $request->role === 'admin'
            ? ['email' => $request->email, 'password' => $request->password]
            : ['student_id' => $request->student_id, 'password' => $request->password];

        $user = User::where($request->role === 'admin' ? 'email' : 'student_id', $request->role === 'admin' ? $request->email : $request->student_id)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json(['message' => 'Logged in', 'token' => $token, 'user' => $user]);
    }
}
