<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PesertaUndian extends Model
{
    // Karena nama tabel kita pake akhiran 's'
    protected $table = 'peserta_undians';

    // Daftarkan kolom yang boleh diisi manual
    protected $fillable = [
        'npk',
        'nama_karyawan',
        'seksi',
        'plant',
        'is_winner'
    ];
}
