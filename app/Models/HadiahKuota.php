<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HadiahKuota extends Model
{
    use HasFactory;

    // Menyesuaikan dengan nama tabel fisik rincian kuota di HeidiSQL
    protected $table = 'hadiah_kuota';

    protected $fillable = [
        'hadiah_id',
        'target_plant',   // Nama kolom fisik di DB lu (Fix error target_plant)
        'label_tampilan',
        'jumlah_pemenang'
    ];

    // Kebalikan relasi menghubungkan detail kuota kembali ke data hadiah utama
    public function hadiah()
    {
        return $this->belongsTo(Hadiah::class, 'hadiah_id', 'id');
    }
}
