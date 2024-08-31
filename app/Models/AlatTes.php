<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlatTes extends Model
{
    use Uuids;
    use HasFactory;
    protected $table = 'alat_tes';
    protected $fillable = [
        'nama',
        'aktif',
        'sort_index'
    ];

    public function kelompokTes() {
        return $this->hasMany(KelompokTes::class, 'alat_tes_id');
    }
}
