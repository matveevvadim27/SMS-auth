<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'code',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
