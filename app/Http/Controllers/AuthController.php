<?php

namespace App\Http\Controllers;

use App\Models\Single;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends ApiController
{
  public function register(Request $request)
  {
    $validator = Validator::make($request->all(), [
      "email" => "required|email",
      "password" => "required|string",
      "nama_lengkap" => "required|string",
      "jenis_kelamin" => "required|string",
    ], $this->generateCustomMessage());
    if ($validator->fails()) {
      return $this->DataInvalidresponse($validator->errors());
    }
    $fields = $validator->validated();

    $user = User::where('email', $fields['email'])->first();
    if ($user) {
      return $this->ErrorResponse('User sudah terdaftar', 400);
    }
    $fields['password'] = bcrypt($fields['password']);
    $create_user = User::create($fields);
    $new_user = User::where('id', $create_user['id'])->first();

    $token = $new_user->createToken('psikotesMetaToken')->plainTextToken;

    return $this->successResponse([
      "peserta" => $new_user,
      "token" => $token
    ], "Pendaftaran Peserta Baru Berhasil", 201);
  }

  public function login(Request $request)
  {
    $validator = Validator::make($request->all(), [
      "email" => "required|email",
      "password" => "required|string",
    ], $this->generateCustomMessage());
    if ($validator->fails()) {
      return $this->DataInvalidresponse($validator->errors());
    }
    $fields = $validator->validated();

    $user = User::where('email', $fields['email'])->first();

    if (!$user || !Hash::check($fields['password'], $user['password'])) {
      return $this->ErrorResponse("Email atau Password salah", 400);
    }

    $token = $user->createToken('psikotesMetaToken')->plainTextToken;

    return $this->successResponse([
      "peserta" => $user,
      "token" => $token
    ], "Login Berhasil");
  }

  public function logout(Request $request)
  {
    $request->user()->currentAccessToken()->delete();
    return $this->successResponse(null, "Logout Berhasil");
  }

  public function registerStatus()
  {
    $register_status = Single::where('name', 'registerStatus')->first();
    $status = false;
    if ($register_status) {
      $status = $register_status['value'] == '1' ? true : false;
    }
    return $this->successResponse([
      'registerStatus' => $status
    ], "Berhasil");
  }
}
