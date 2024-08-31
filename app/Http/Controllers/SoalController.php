<?php

namespace App\Http\Controllers;

use App\Models\Soal;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SoalController extends ApiController
{
    public function index(Request $request) {
        $kelompok_tes_id = $request->query('kelompok_tes_id');
        $query = Soal::query();
        if ($kelompok_tes_id) {
            $query->where('kelompok_tes_id', '=', $kelompok_tes_id);
        }
        $soal = $query->orderBy('nomor', 'asc')->get();
        return $this->successResponse($soal);
    }

    public function mutate(Request $request) {
        $validator = Validator::make($request->all(), [
            "insert" => "array",
            "update" => "array",
            "delete" => "array"
        ], $this->generateCustomMessage());
        if ($validator->fails()) {
            return $this->DataInvalidresponse($validator->errors());
        }
        $fields = $validator->validated();

        DB::beginTransaction();
        try {
            $soalIds = [];
            if (isset($fields['insert'])) {
                $insertSoalData = [];
                foreach ($fields['insert'] as $soal) {
                    $now = Carbon::now()->toDateTimeString();
                    $soal_id = (string) Str::uuid();
                    array_push($soalIds, $soal_id);
                    array_push($insertSoalData, [
                        "id" => $soal_id,
                        "nomor" => $soal['nomor'],
                        "jenis_soal" => $soal['jenis_soal'],
                        "teks" => $soal['teks'],
                        "opsi_soal" => $soal['opsi_soal'],
                        "kelompok_tes_id" => $soal['kelompok_tes_id'],
                        "created_at" => $now,
                        "updated_at" => $now
                    ]);
                }
                Soal::insert($insertSoalData);
            }
            if (isset($fields['update'])) {
                foreach($fields['update'] as $soal) {
                    array_push($soalIds, $soal['id']);
                    $updateSoalData = array();
                    if (isset($soal['nomor'])) {
                        $updateSoalData['nomor'] = $soal['nomor'];
                    }
                    if (isset($soal['jenis_soal'])) {
                        $updateSoalData['jenis_soal'] = $soal['jenis_soal'];
                    }
                    if (isset($soal['teks'])) {
                        $updateSoalData['teks'] = $soal['teks'];
                    }
                    if (isset($soal['opsi_soal'])) {
                        $updateSoalData['opsi_soal'] = $soal['opsi_soal'];
                    }
                    Soal::where('id', $soal['id'])->update($updateSoalData);
                }
            }
            if (isset($fields['delete'])) {
                Soal::destroy($fields['delete']);
            }
            $mutateSoal = Soal::whereIn('id', $soalIds)->get();
            DB::commit();
            return $this->successResponse($mutateSoal, 'Soal berhasil disimpan', 201);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->ErrorResponse($e->getMessage(), 500);
        }
    }
}
