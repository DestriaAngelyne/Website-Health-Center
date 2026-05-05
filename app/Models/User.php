<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;  // ← tambahkan ini

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;  // ← tambahkan HasApiTokens

    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'role',
        'is_active',
        'foto',
        'no_hp',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'is_active'         => 'boolean',
    ];

    public function isAdmin(): bool   { return $this->role === 'admin'; }
    public function isLoket(): bool   { return $this->role === 'loket'; }
    public function isPerawat(): bool { return $this->role === 'perawat'; }
    public function isDokter(): bool  { return $this->role === 'dokter'; }
    public function isPasien(): bool  { return $this->role === 'pasien'; }

    public function dokter()
    {
        return $this->hasOne(Dokter::class);
    }

    public function pasien()
    {
        return $this->hasOne(Pasien::class);
    }

    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class);
    }
}
