<?php

namespace LaravelStorable\ServiceProviders;

use LaravelStorable\Contracts\Storage;
use LaravelStorable\Drivers\MongoDB\Contracts\MongoDBClient;
use LaravelStorable\Drivers\MongoDB\Mongo;
use LaravelStorable\MongoDBStorage;
use Illuminate\Support\ServiceProvider;

class LaravelStorableServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(MongoDBClient::class, function ($app) {
            return Mongo::client();
        });

        $this->app->singleton(Storage::class, function ($app) {
            switch (config('laravel-storable.driver')) {
                case 'mongodb':
                    return new MongoDBStorage($app->make(MongoDBClient::class));
            }

            throw new \RuntimeException("Storable driver is not set or not supported.");
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '../configs/laravel-storable.php' => config_path('laravel-storable.php'),
        ]);
    }
}
