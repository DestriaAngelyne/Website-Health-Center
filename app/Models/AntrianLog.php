<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AntrianLog extends Model
{
    protected $table = 'antrian_log';

    public $timestamps = false;

    protected $fillable = [
        'antrian_id',
        'user_id',
        'status_sebelum',
        'status_sesudah',
        'keterangan',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function antrian()
    {
        return $this->belongsTo(Antrian::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
