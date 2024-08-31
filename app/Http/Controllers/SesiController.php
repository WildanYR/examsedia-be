<?php

namespace App\Http\Controllers;

use App\Models\Sesi;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SesiController extends ApiController
{
    public function index(Request $request) {
        $filter_aktif = $request->query('aktif');
        $query = Sesi::query();
        if (gettype($filter_aktif) != "NULL") {
            $query->where('aktif', '=', $filter_aktif);
        }
        $sesi = $query->get();
        return $this->successResponse($sesi);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            "nama" => "required|string",
            "aktif" => "boolean"
        ], $this->generateCustomMessage());
        if ($validator->fails()) {
            return $this->DataInvalidresponse($validator->errors());
        }
        $fields = $validator->validated();

        $new_sesi = Sesi::create($fields);
        $sesi = Sesi::where('id', '=', $new_sesi['id'])->first();
        return $this->successResponse($sesi, 'Sesi berhasil dibuat', 201);
    }

    public function update(Request $request, $id) {
        $validator = Validator::make($request->all(), [
            "nama" => "required_without_all:aktif|string",
            "aktif" => "required_without_all:nama|boolean"
        ], $this->generateCustomMessage());
        if ($validator->fails()) {
            return $this->DataInvalidresponse($validator->errors());
        }
        $fields = $validator->validated();

        DB::beginTransaction();
        try {
            $sesi = Sesi::where('id', '=', $id)->first();
            if (!$sesi) {
                return $this->ErrorResponse('Sesi tidak ditemukan', 400);
            }
            if (isset($fields['aktif']) && $fields['aktif'] == true) {
                Sesi::where('aktif', true)->update(['aktif' => false]);
            }
            $sesi->update($fields);
            DB::commit();
            return $this->successResponse($sesi, 'Sesi berhasil diubah');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->ErrorResponse("Terjadi Error di Server", 500);
        }
    }

    public function remove(Request $request, $id) {
        $sesi = Sesi::where('id', '=', $id)->first();
        if (!$sesi) {
            return $this->ErrorResponse('Sesi tidak ditemukan', 400);
        }
        $sesi->delete();
        return $this->successResponse(null, 'Sesi berhasil dihapus');
    }

    public function disableAll() {
        Sesi::where('aktif', true)->update(['aktif' => false]);
        return $this->successResponse(null, 'Sesi Psikotes dimatikan');
    }
}
