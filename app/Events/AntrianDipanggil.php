<?php

namespace App\Events;

use App\Models\Antrian;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AntrianDipanggil
{
    use Dispatchable, SerializesModels;

    public function __construct(public Antrian $antrian)
    {
    }
}
