<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Article extends Model

{
    use HasApiTokens, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'qr_code',
        'status',
        'visibility',
    ];
}
