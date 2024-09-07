<?php

namespace App\Http\Controllers;

use App\Models\AlatTes;
use App\Models\KelompokTes;
use App\Models\RiwayatPsikotes;
use App\Models\Sesi;
use App\Models\Soal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PsikotesController extends ApiController
{
    public function getPsikotes(Request $request, $alat_tes_id)
    {
        // cek mengerjakan tes
        $sesi = Sesi::where('aktif', true)->first();
        $user_id = $request->user()->id;
        $mengerjakan_tes = RiwayatPsikotes::where('user_id', $user_id)
            ->where('sesi_id', $sesi['id'])
            ->where('alat_tes_id', $alat_tes_id)
            ->first();
        if ($mengerjakan_tes && isset($mengerjakan_tes['waktu_selesai'])) {
            return $this->ErrorResponse("Psikotes sudah dikerjakan", 400);
        }
        // get soal
        $alat_tes = AlatTes::where('id', $alat_tes_id)->first()->toArray();
        if (!$alat_tes) {
            return $this->ErrorResponse("Alat Tes tidak ditemukan", 400);
        }
        if (!$alat_tes['aktif']) {
            return $this->ErrorResponse("Alat Tes tidak bisa dikerjakan", 400);
        }
        $kelompok_tes = KelompokTes::where('alat_tes_id', $alat_tes_id)->orderBy('sort_index', 'asc')->get()->toArray();
        $kelompokTes_ids = [];
        foreach ($kelompok_tes as $keltes) {
            array_push($kelompokTes_ids, $keltes['id']);
        }
        $soals = Soal::whereIn('kelompok_tes_id', $kelompokTes_ids)->orderBy('kelompok_tes_id', 'asc')->orderBy('nomor', 'asc')->get()->toArray();
        for ($i = 0; $i < count($kelompok_tes); $i++) {
            if (empty($kelompok_tes[$i]['soal'])) {
                $kelompok_tes[$i]['soal'] = [];
            }
            for ($j = 0; $j < count($soals); $j++) {
                if ($kelompok_tes[$i]['id'] == $soals[$j]['kelompok_tes_id']) {
                    array_push($kelompok_tes[$i]['soal'], $soals[$j]);
                }
            }
        }
        $alat_tes['kelompok_tes'] = $kelompok_tes;
        // set status mengerjakan
        if (!$mengerjakan_tes) {
            $now = Carbon::now()->toDateTimeString();
            RiwayatPsikotes::create([
                "waktu_mulai" => $now,
                "user_id" => $user_id,
                "sesi_id" => $sesi['id'],
                "alat_tes_id" => $alat_tes_id
            ]);
        }
        return $this->successResponse($alat_tes, "Selamat Mengerjakan");
    }

    public function submitPsikotes(Request $request, $alat_tes_id)
    {
        $validator = Validator::make($request->all(), [
            "jawaban" => "string"
        ], $this->generateCustomMessage());
        if ($validator->fails()) {
            return $this->DataInvalidresponse($validator->errors());
        }
        $fields = $validator->validated();
        $sesi = Sesi::where('aktif', true)->first();
        $user_id = $request->user()->id;
        $mengerjakan_tes = RiwayatPsikotes::where('user_id', $user_id)
            ->where('sesi_id', $sesi['id'])
            ->where('alat_tes_id', $alat_tes_id)
            ->first();
        if (isset($mengerjakan_tes['waktu_selesai']) && isset($mengerjakan_tes['jawaban'])) {
            return $this->PsikotesDoneResponse();
        }
        $now = Carbon::now()->toDateTimeString();
        RiwayatPsikotes::where('user_id', $user_id)
            ->where('sesi_id', $sesi['id'])
            ->where('alat_tes_id', $alat_tes_id)
            ->update([
                'waktu_selesai' => $now,
                'jawaban' => $fields['jawaban']
            ]);
        return $this->successResponse(null, "Selamat anda telah selesai mengerjakan psikotes");
    }

    public function getStatusPsikotes(Request $request)
    {
        $sesi_aktif = Sesi::where('aktif', true)->first(['id', 'nama']);
        if (!$sesi_aktif) {
            return $this->successResponse([
                "alat_tes" => null,
                "sesi" => null
            ], "tidak ada sesi psikotes saat ini");
        }
        $alat_tes_aktif = AlatTes::where('aktif', true)->orderBy('sort_index', 'asc')->get(['id', 'nama']);
        $riwayat_tes = RiwayatPsikotes::where([
            "user_id" => $request->user()->id,
            "sesi_id" => $sesi_aktif['id']
        ])->get();
        if ($riwayat_tes) {
            for ($i = 0; $i < count($alat_tes_aktif); $i++) {
                $alat_tes_aktif[$i]['selesai'] = false;
                for ($j = 0; $j < count($riwayat_tes); $j++) {
                    if ($riwayat_tes[$j]['alat_tes_id'] == $alat_tes_aktif[$i]['id']) {
                        if (isset($riwayat_tes[$j]['waktu_selesai'])) {
                            $alat_tes_aktif[$i]['selesai'] = true;
                        }
                    }
                }
            }
        }
        return $this->successResponse([
            "alat_tes" => $alat_tes_aktif,
            "sesi" => $sesi_aktif
        ]);
    }

    public function getUserPsikotes(Request $request, $sesi_id, $alat_tes_id)
    {
        $riwayat_tes = RiwayatPsikotes::where('sesi_id', $sesi_id)->where('alat_tes_id', $alat_tes_id)->whereNotNull('waktu_selesai')->get();
        $riwayatTes_user_ids = [];
        foreach ($riwayat_tes as $riwayat) {
            array_push($riwayatTes_user_ids, $riwayat['user_id']);
        }
        $users = User::whereIn('id', $riwayatTes_user_ids)->get(['id', 'email', 'nama_lengkap', 'jenis_kelamin']);
        return $this->successResponse($users);
    }

    public function getJawabanUser(Request $request, $sesi_id, $alat_tes_id, $user_id)
    {
        $jawaban_user = RiwayatPsikotes::where('sesi_id', $sesi_id)->where('alat_tes_id', $alat_tes_id)->where('user_id', $user_id)->whereNotNull('waktu_selesai')->first();
        $kelompok_tes = KelompokTes::where('alat_tes_id', $alat_tes_id)->orderBy('sort_index', 'asc')->get(['id', 'nama', 'alat_tes_id'])->toArray();
        $kelompokTes_ids = [];
        foreach ($kelompok_tes as $keltes) {
            array_push($kelompokTes_ids, $keltes['id']);
        }
        $soals = Soal::whereIn('kelompok_tes_id', $kelompokTes_ids)->orderBy('kelompok_tes_id', 'asc')->orderBy('nomor', 'asc')->get(['id', 'nomor', 'jenis_soal', 'kelompok_tes_id', 'opsi_soal'])->toArray();
        for ($i = 0; $i < count($kelompok_tes); $i++) {
            if (empty($kelompok_tes[$i]['soal'])) {
                $kelompok_tes[$i]['soal'] = [];
            }
            for ($j = 0; $j < count($soals); $j++) {
                if ($kelompok_tes[$i]['id'] == $soals[$j]['kelompok_tes_id']) {
                    // proses opsi + skor, filter data soal
                    $opsi_data = [];
                    $opsi_ex = explode(';=;', $soals[$j]['opsi_soal']);
                    for ($k = 0; $k < count($opsi_ex); $k++) {
                        $ops = explode(';-;', $opsi_ex[$k]);

                        array_push($opsi_data, [
                            'value' => $ops[0],
                            'score' => isset($ops[3]) ? (int)$ops[3] : 0
                        ]);
                    }
                    $soal_data = [
                        'id' => $soals[$j]['id'],
                        'nomor' => $soals[$j]['nomor'],
                        'jenis_soal' => $soals[$j]['jenis_soal'],
                        'kelompok_tes_id' => $soals[$j]['kelompok_tes_id'],
                        'opsi' => $opsi_data
                    ];
                    array_push($kelompok_tes[$i]['soal'], $soal_data);
                }
            }
        }
        return $this->successResponse([
            "jawaban" => $jawaban_user['jawaban'],
            "kelompok_tes" => $kelompok_tes
        ]);
    }
}
