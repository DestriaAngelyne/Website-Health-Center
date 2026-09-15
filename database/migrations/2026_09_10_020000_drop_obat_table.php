<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('obat');
    }

    public function down(): void
    {
        // Tabel obat tidak direkonstruksi otomatis di rollback.
        // Jalankan ulang migration create_obat_table yang lama jika perlu dikembalikan.
    }
};
