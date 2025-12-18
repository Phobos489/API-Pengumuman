<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pengumuman extends Model
{
    use HasFactory, SoftDeletes;

    // Tabel default hasil pluralisasi menjadi "pengumumen", set manual ke "pengumumans"
    protected $table = 'pengumumans';

    protected $fillable = [
        'judul',
        'deskripsi',
        'gambar',
        'kategori',
        'status',
        'tanggal_mulai',
        'tanggal_selesai',
        'penerbit',
        'views',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'views' => 'integer',
    ];

    protected $appends = ['gambar_url'];

    public function getGambarUrlAttribute()
    {
        if ($this->gambar) {
            return url('/assets/' . $this->gambar);
        }
        return null;
    }

    public function incrementViews()
    {
        $this->increment('views');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeActive($query)
    {
        return $query->where('tanggal_mulai', '<=', now())
                     ->where(function($q) {
                         $q->whereNull('tanggal_selesai')
                           ->orWhere('tanggal_selesai', '>=', now());
                     });
    }
}