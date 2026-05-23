<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class FileUploadService
{
    /**
     * @param  array<int, UploadedFile>  $files
     */
    public function syncCollection(HasMedia $model, string $collection, array $files): void
    {
        if ($files === []) {
            return;
        }

        $model->clearMediaCollection($collection);

        foreach ($files as $file) {
            $this->addToCollection($model, $collection, $file);
        }
    }

    public function addToCollection(HasMedia $model, string $collection, UploadedFile $file): Media
    {
        return $model->addMedia($file)->toMediaCollection($collection);
    }

    public function addSingle(HasMedia $model, string $collection, ?UploadedFile $file): void
    {
        if ($file === null) {
            return;
        }

        $model->clearMediaCollection($collection);
        $this->addToCollection($model, $collection, $file);
    }

    public function clearCollection(HasMedia $model, string $collection): void
    {
        $model->clearMediaCollection($collection);
    }

    public function deleteMedia(Media $media): void
    {
        $media->delete();
    }
}
