<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peserta extends Model
{
    use HasFactory;

    // Ganti ke nama tabel yang lu mau tadi Bang
    protected $table = 'peserta_undians';

    // Kolom ini sudah bener sesuai format Excel lu
    protected $fillable = [
        'npk',
        'nama_karyawan',
        'seksi',
        'plant',
        'is_winner'
    ];

    // Opsional: Biar is_winner otomatis dibaca angka 0/1 atau true/false
    protected $casts = [
        'is_winner' => 'integer',
    ];
}
