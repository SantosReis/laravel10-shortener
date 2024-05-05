<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UrlShortener extends Model
{
    use HasFactory;
    // protected $fillable = [
    //     'user_id', 'long', 'short', 'counter'
    // ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
