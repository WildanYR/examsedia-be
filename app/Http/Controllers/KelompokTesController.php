<?php

namespace App\Http\Controllers;

use App\Models\KelompokTes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class KelompokTesController extends ApiController
{
    public function index(Request $request) {
        $alat_tes_id = $request->query('alat_tes_id');
        $show_petunjuk = $request->query('show_petunjuk', false);
        $query = KelompokTes::query();
        if ($alat_tes_id) {
            $query->where('alat_tes_id', '=', $alat_tes_id);
        }
        $columnToGet = [];
        if (!$show_petunjuk) {
            foreach (Schema::getColumnListing('kelompok_tes') as $kelompokTesColumn) {
                if ($kelompokTesColumn != "petunjuk") {
                    array_push($columnToGet, $kelompokTesColumn);
                }
            }
        } else {
            $columnToGet = ['*'];
        }
        $kelompok_tes = $query->orderBy('sort_index', 'asc')->get($columnToGet);
        return $this->successResponse($kelompok_tes);
    }

    public function detail($id) {
        $kelompok_tes = KelompokTes::where('id', $id)->first();
        return $this->successResponse($kelompok_tes);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            "nama" => "required|string",
            "petunjuk" => "string",
            "waktu" => "required|integer",
            "alat_tes_id" => "required|uuid",
            "sort_index" => "integer"
        ], $this->generateCustomMessage());
        if ($validator->fails()) {
            return $this->DataInvalidresponse($validator->errors());
        }
        $fields = $validator->validated();

        $kelompok_tes = KelompokTes::create($fields);
        return $this->successResponse($kelompok_tes, "Kelompok Tes berhasil dibuat", 201);
    }

    public function update(Request $request, $id) {
        $validator = Validator::make($request->all(), [
            "nama" => "required_without_all:petunjuk,waktu,sort_index|string",
            "petunjuk" => "required_without_all:nama,waktu,sort_index|string",
            "waktu" => "required_without_all:nama,petunjuk,sort_index|integer",
            "sort_index" => "required_without_all:nama,petunjuk,waktu|integer"
        ], $this->generateCustomMessage());
        if ($validator->fails()) {
            return $this->DataInvalidresponse($validator->errors());
        }
        $fields = $validator->validated();

        $kelompok_tes = KelompokTes::where('id', '=', $id)->first();
        if (!$kelompok_tes) {
            return $this->ErrorResponse('Kelompok Tes tidak ditemukan', 400);
        }
        $kelompok_tes->update($fields);
        return $this->successResponse($kelompok_tes, 'Kelompok Tes berhasil diubah');
    }

    public function remove(Request $request, $id) {
        $kelompok_tes = KelompokTes::where('id', '=', $id)->first();
        if (!$kelompok_tes) {
            return $this->ErrorResponse('Kelompok Tes tidak ditemukan', 400);
        }
        $kelompok_tes->delete();
        return $this->successResponse(null, 'Kelompok Tes berhasil dihapus');
    }
}
