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

    public static function boot()
    {
        parent::boot();

        // Otomatis isi url_hash saat membuat atau mengupdate artikel
        static::saving(function ($article) {
            if ($article->isDirty('url')) {
                $article->url_hash = md5($article->url);
            }
        });
    }

    public function publisher()
    {
        return $this->belongsTo(Publisher::class);
    }
}
