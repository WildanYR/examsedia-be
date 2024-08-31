<?php
namespace App\Traits;

trait ApiResponder {
  protected function successResponse($data, $message = null, $status = 200) {
    return response()->json([
      "apiData" => $data,
      "message" => $message,
      "status" => "Success"
    ], $status);
  }

  protected function ErrorResponse($message, $status) {
    return response()->json([
      "apiData" => null,
      "message" => $message,
      "status" => "Error"
    ], $status);
  }
  
  protected function DataInvalidresponse($data) {
    return response()->json([
      "apiData" => $data,
      "message" => "Data tidak sesuai ketentuan",
      "status" => "Data Invalid"
    ], 400);
  }
  
  protected function PsikotesDoneResponse() {
    return response()->json([
      "apiData" => null,
      "message" => "Psikotes sudah dikerjakan",
      "status" => "Psikotes Done"
    ], 400);
  }
}