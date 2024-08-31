<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatPsikotes extends Model
{
    use Uuids;
    use HasFactory;

    public $timestamps = false;
    protected $table = 'riwayat_psikotes';
    protected $fillable = [
        'waktu_mulai',
        'waktu_selesai',
        'jawaban',
        'user_id',
        'sesi_id',
        'alat_tes_id'
    ];

    public function sesi() {
        return $this->belongsTo(Sesi::class, 'sesi_id');
    }
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
