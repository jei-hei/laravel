<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    // REGISTER
    public function register(Request $request)
    {
        $role = strtolower(trim((string) $request->role));

        if ($role === 'admin') {
            $request->validate([
                'name'     => 'required|string|max:255',
                'email'    => 'required|email|unique:users,email',
                'campus'   => 'required|in:Echague,Angadanan,Jones,Santiago',
                'password' => 'required|string|min:6|confirmed',
            ]);

            $user = User::create([
                'name'     => $request->name,
                'role'     => 'admin',
                'email'    => strtolower(trim($request->email)),
                'campus'   => trim($request->campus),
                'password' => $request->password, // model mutator will hash
            ]);
        } elseif ($role === 'student') {
            $request->validate([
                'name'       => 'required|string|max:255',
                'student_id' => ['required', 'string', 'regex:/^\d{2}-\d{4}$/', 'unique:users,student_id'],
                'lrn'        => ['required', 'digits:12'],
                'campus'     => 'required|in:Echague,Angadanan,Jones,Santiago',
                'password'   => 'nullable|string|min:6|confirmed',
                'email'      => 'nullable|email|unique:users,email',
            ]);

            // store hashed LRN in lrn_hash and optionally store raw lrn temporarily
            $user = User::create([
                'name'       => $request->name,
                'role'       => 'student',
                'student_id' => trim($request->student_id),
                'lrn'        => trim($request->lrn), // temporary: used during migration; remove later
                'lrn_hash'   => Hash::make($request->lrn),
                'email'      => strtolower(trim((string)($request->email ?? ''))),
                'campus'     => trim($request->campus),
                'password'   => $request->password ? $request->password : null, // optional
            ]);
        } else {
            return response()->json(['message' => 'Invalid role'], 400);
        }

        // sanitize user for response
        $safeUser = [
            'id' => $user->id,
            'username' => $user->student_id ?? $user->email ?? null,
            'full_name' => $user->name,
            'role' => $user->role,
            'campus' => $user->campus,
        ];

        return response()->json(['message' => 'User registered', 'user' => $safeUser], 201);
    }

    // LOGIN
    public function login(Request $request)
    {
        $role = strtolower(trim((string) $request->role));

        if ($role === 'admin') {
            $request->validate([
                'email'    => 'required|email',
                'campus'   => 'required|in:Echague,Angadanan,Jones,Santiago',
                'password' => 'required|string',
            ]);

            $email = strtolower(trim($request->email));

            $user = User::where('email', $email)
                        ->where('campus', trim($request->campus))
                        ->where('role', 'admin')
                        ->first();

            $secretValid = $user && Hash::check($request->password, $user->password);
        } elseif ($role === 'student') {
            $request->validate([
                'student_id' => ['required', 'string', 'regex:/^\d{2}-\d{4}$/'],
                'lrn'        => ['required', 'digits:12'],
                'campus'     => 'required|in:Echague,Angadanan,Jones,Santiago',
            ]);

            $user = User::where('student_id', trim($request->student_id))
                        ->where('campus', trim($request->campus))
                        ->where('role', 'student')
                        ->first();

            $secretValid = false;
            if ($user) {
                // Prefer hashed check if available
                if (!empty($user->lrn_hash)) {
                    $secretValid = Hash::check($request->lrn, $user->lrn_hash);
                } else {
                    // Fallback to plaintext check during migration only
                    // This keeps compatibility; remove after migration
                    if (config('app.env') === 'local') {
                        Log::warning('Using plaintext LRN fallback for user id ' . $user->id);
                    }
                    $secretValid = isset($user->lrn) && trim($user->lrn) === trim($request->lrn);
                }
            }
        } else {
            return response()->json(['message' => 'Invalid role'], 400);
        }

        // Generic failure message to avoid enumeration
        if (! $user || ! $secretValid) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Create token
        $token = $user->createToken('API Token')->plainTextToken;

        // Sanitize user payload
        $safeUser = [
            'id' => $user->id,
            'username' => $user->student_id ?? $user->email ?? null,
            'full_name' => $user->name,
            'role' => $user->role,
            'campus' => $user->campus,
        ];

        return response()->json([
            'status' => 'success',
            'access_token' => $token,
            'user' => $safeUser,
        ], 200);
    }

    // ME
    public function me(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $safeUser = [
            'id' => $user->id,
            'username' => $user->student_id ?? $user->email ?? null,
            'full_name' => $user->name,
            'role' => $user->role,
            'campus' => $user->campus,
        ];

        return response()->json(['user' => $safeUser], 200);
    }

    // LOGOUT
    public function logout(Request $request)
    {
        $user = $request->user();
        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }
        return response()->json(['message' => 'Logged out']);
    }
}
