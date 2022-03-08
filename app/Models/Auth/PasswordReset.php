<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    use HasFactory;

    protected $table = 'snyn_password_resets';

    public $timestamps = false;

    protected $fillable = [
        'username',
        'token',
        'completed',
        'created_at',
        'expires_at'
    ];
}
