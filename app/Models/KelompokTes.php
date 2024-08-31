<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KelompokTes extends Model
{
    use Uuids;
    use HasFactory;
    protected $table = "kelompok_tes";
    protected $fillable = ["nama", "petunjuk", "waktu", "alat_tes_id", "sort_index"];

    public function alatTes() {
        return $this->belongsTo(AlatTes::class, 'alat_tes_id');
    }

    public function soal() {
        return $this->hasMany(Soal::class, 'kelompok_tes_id');
    }
}
