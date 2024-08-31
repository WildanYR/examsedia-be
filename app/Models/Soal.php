<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Soal extends Model
{
    use Uuids;
    use HasFactory;

    protected $table = 'soal';
    protected $fillable = ['nomor', 'jenis_soal', 'teks', 'opsi_soal', 'kelompok_tes_id'];

    public function kelompokTes() {
        return $this->belongsTo(KelompokTes::class, 'kelompok_tes_id');
    }
}
