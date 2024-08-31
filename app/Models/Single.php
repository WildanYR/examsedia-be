<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Single extends Model
{
    use HasFactory;

    protected $table = 'single';
    protected $fillable = ['name', 'value'];
    public $timestamps = false;
}
