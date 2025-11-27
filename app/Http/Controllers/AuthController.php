<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\LoginHistory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name'=>'required|string|max:255',
            'email'=>'required|email|unique:users,email',
            'password'=>'required|string|min:8',
            'role'=>'required|in:admin,student'
        ]);

        $user = User::create($request->all());
        $token = $user->createToken('api-token')->plainTextToken;

        LoginHistory::create(['user_id'=>$user->id,'ip_address'=>$request->ip()]);

        return response()->json(['user'=>$user,'token'=>$token],201);
    }

    public function login(Request $request)
    {
        $request->validate(['email'=>'required|email','password'=>'required']);
        $user = User::where('email',$request->email)->first();

        if(!$user || !Hash::check($request->password,$user->password)){
            throw ValidationException::withMessages(['email'=>['Invalid credentials']]);
        }

        $token = $user->createToken('api-token')->plainTextToken;
        LoginHistory::create(['user_id'=>$user->id,'ip_address'=>$request->ip()]);

        return response()->json(['user'=>$user,'token'=>$token]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message'=>'Logged out']);
    }
}
