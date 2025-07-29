<?php

// app/Http/Controllers/RegisterController.php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'name'         => 'required|string|max:100',
            'username'     => 'required|string|max:30|unique:users,username',
            'password'     => 'required|string|min:6',
            'number_phone' => 'nullable|string|max:20',
        ]);

        // Jika validasi gagal
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        // Simpan user
        $user = User::create([
            'name'         => $request->name,
            'username'     => $request->username,
            'password'     => Hash::make($request->password),
            'number_phone' => $request->number_phone,
        ]);

        // Response sukses
        return response()->json([
            'message' => 'Registrasi berhasil',
            'user'    => $user
        ], 201);
    }
}
