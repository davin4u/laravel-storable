<?php

namespace LaravelStorable\ServiceProviders;

use LaravelStorable\Commands\InitCommand;
use LaravelStorable\Contracts\Storage;
use LaravelStorable\Drivers\MongoDB\Contracts\MongoDBClient;
use LaravelStorable\Drivers\MongoDB\Mongo;
use LaravelStorable\Observers\StorableObserver;
use LaravelStorable\MongoDBStorage;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;

class LaravelStorableServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(MongoDBClient::class, function ($app) {
            return Mongo::client();
        });

        $this->app->singleton(Storage::class, function ($app) {
            switch (config('laravel-storable.driver', 'mongodb')) {
                case 'mongodb':
                    return new MongoDBStorage($app->make(MongoDBClient::class));
            }

            return null;
        });
    }

    public function boot()
    {
        $parts = explode(DIRECTORY_SEPARATOR, __DIR__);

        unset($parts[count($parts) - 1]);

        // publish config
        $this->publishes([
            implode(DIRECTORY_SEPARATOR, $parts) . '/configs/laravel-storable.php' => config_path('laravel-storable.php'),
        ]);

        // register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                InitCommand::class
            ]);
        }

        // register observers
        $observables = config('laravel-storable.observable', []);

        if (!empty($observables)) {
            foreach ($observables as $entityClass) {
                if ((new $entityClass) instanceof Model) {
                    $entityClass::observe(StorableObserver::class);
                }
            }
        }
    }
}
