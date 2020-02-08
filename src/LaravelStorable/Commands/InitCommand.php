<?php

namespace LaravelStorable\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use LaravelStorable\Contracts\Storage;

class InitCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravel-storable:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize laravel storable';

    /**
     * @var Storage
     */
    protected $storage = null;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        /** @var Storage storage */
        $this->storage = app()->make(Storage::class);
    }

    public function handle()
    {
        $storable = config('laravel-storable.storable');

        if ($this->checkStorable($storable)) {
            $bar = $this->output->createProgressBar(count($storable));

            $bar->start();

            foreach ($storable as $class) {
                $entity = new $class();

                $this->processEntity($entity);

                $bar->advance();
            }

            $bar->finish();
        }
    }

    /**
     * @param array $storable
     * @return bool
     */
    private function checkStorable($storable = [])
    {
        if (!empty($storable)) {
            foreach ($storable as $class) {
                if (!class_exists($class)) {
                    $this->error("Class {$class} does not exists.");

                    return false;
                }
            }

            return true;
        }

        return false;
    }

    /**
     * @param $entity
     */
    private function processEntity($entity)
    {
        $driver = config('laravel-storable.driver');

        $storableCollection = property_exists($entity, 'storableCollection')
            ? $entity->storableCollection
            : config("laravel-storable.drivers.{$driver}.collection");

        $this->storage->createCollection($storableCollection);

        $storableKey = property_exists($entity, 'storableKey') ? $entity->storableKey : 'storable_id';

        Schema::table($entity->getTable(), function (Blueprint $table) use ($storableKey) {
            $table->string($storableKey)->nullable();
        });
    }
}
