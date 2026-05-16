<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plant extends Model
{
    use HasFactory;

    // Tambahkan baris ini biar nama_plant bisa diisi lewat form
    protected $fillable = ['nama_plant'];
}
