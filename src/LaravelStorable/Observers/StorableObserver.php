<?php

namespace LaravelStorable\Observers;

class StorableObserver
{
    public function created($model)
    {
        $model->saveStorableDocument();
    }

    public function updated($model)
    {
        $model->saveStorableDocument();
    }

    public function deleted($model)
    {
        $model->deleteStorableDocument();
    }
}
