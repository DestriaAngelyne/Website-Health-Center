<?php

namespace App\Providers;

use App\Events\AntrianDilewati;
use App\Events\AntrianDipanggil;
use App\Events\AntrianSelesai;
use App\Listeners\KirimNotifikasiAntrian;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Event::listen(AntrianDipanggil::class, [KirimNotifikasiAntrian::class, 'handleDipanggil']);
        Event::listen(AntrianSelesai::class, [KirimNotifikasiAntrian::class, 'handleSelesai']);
        Event::listen(AntrianDilewati::class, [KirimNotifikasiAntrian::class, 'handleDilewati']);

        Auth::provider('eloquent-with-trashed', function ($app, array $config) {
            return new class($app['hash'], $config['model']) extends EloquentUserProvider {
                public function retrieveByCredentials(array $credentials)
                {
                    $query = $this->createModel()->newQuery()->withTrashed();
                    foreach ($credentials as $key => $value) {
                        if (!str_contains($key, 'password')) {
                            $query->where($key, $value);
                        }
                    }
                    return $query->first();
                }
            };
        });
    }
}
