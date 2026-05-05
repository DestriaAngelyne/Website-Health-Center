<?php

namespace App\Providers;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
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
