<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sesi extends Model
{
    use Uuids;
    use HasFactory;

    protected $table = 'sesi';
    protected $fillable = ['nama', 'aktif'];
}
