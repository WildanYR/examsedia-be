<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AlatTes;
use App\Models\KelompokTes;
use Illuminate\Support\Facades\Validator;

class AlatTesController extends ApiController
{
    public function index(Request $request) {
        $filter_aktif = $request->query('aktif');
        $query = AlatTes::query();
        if (gettype($filter_aktif) != "NULL") {
            $query->where('aktif', '=', $filter_aktif);
        }
        $alat_tes = $query->withCount('kelompokTes')->orderBy('sort_index', 'asc')->get();
        return $this->successResponse($alat_tes);
    }
    
    public function detail($id) {
        $alat_tes = AlatTes::where('id', $id)->first();
        $kelompok_tes = KelompokTes::select(['id', 'nama'])->where('alat_tes_id', $id)->withCount('soal')->orderBy('sort_index', 'asc')->get();
        $alat_tes['kelompok_tes'] = $kelompok_tes;
        return $this->successResponse($alat_tes);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            "nama" => "required|string",
            "aktif" => "boolean",
            "sort_index" => "integer"
        ], $this->generateCustomMessage());
        if ($validator->fails()) {
            return $this->DataInvalidresponse($validator->errors());
        }
        $fields = $validator->validated();

        $new_alat_tes = AlatTes::create($fields);
        $alat_tes = AlatTes::where('id', '=', $new_alat_tes['id'])->first();
        return $this->successResponse($alat_tes, 'Alat Tes berhasil dibuat', 201);
    }

    public function update(Request $request, $id) {
        $validator = Validator::make($request->all(), [
            "nama" => "required_without_all:aktif,sort_index|string",
            "aktif" => "required_without_all:nama,sort_index|boolean",
            "sort_index" => "required_without_all:nama,aktif|integer"
        ], $this->generateCustomMessage());
        if ($validator->fails()) {
            return $this->DataInvalidresponse($validator->errors());
        }
        $fields = $validator->validated();

        $alat_tes = AlatTes::where('id', '=', $id)->first();
        if (!$alat_tes) {
            return $this->ErrorResponse('Alat Tes tidak ditemukan', 400);
        }
        $alat_tes->update($fields);
        return $this->successResponse($alat_tes, 'Alat Tes berhasil diubah');
    }

    public function remove(Request $request, $id) {
        $alat_tes = AlatTes::where('id', '=', $id)->first();
        if (!$alat_tes) {
            return $this->ErrorResponse('Alat Tes tidak ditemukan', 400);
        }
        $alat_tes->delete();
        return $this->successResponse(null, 'Alat Tes berhasil dihapus');
    }
}
