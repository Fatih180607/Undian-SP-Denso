<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hadiah extends Model
{
    use HasFactory;

    protected $table = 'hadiah';

    protected $fillable = [
        'nama_hadiah',
        'foto_hadiah',
        'tipe_hadiah',
        'total_kuota_global',
        'is_active'
    ];

    // Hubungan ke tabel rincian kuota plant
    public function kuotaPerPlant()
    {
        return $this->hasMany(HadiahKuota::class, 'hadiah_id', 'id');
    }
}
