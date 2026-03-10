<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = ['publisher_id', 'url', 'url_hash', 'published_at'];

    protected $casts = [
        'published_at' => 'date',
    ];

    public function publisher()
    {
        return $this->belongsTo(Publisher::class);
    }
}
