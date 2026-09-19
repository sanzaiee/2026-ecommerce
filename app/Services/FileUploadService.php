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

    /**
     * @param  array<int, UploadedFile>  $files
     */
    public function appendToCollection(HasMedia $model, string $collection, array $files): void
    {
        foreach ($files as $file) {
            $this->addToCollection($model, $collection, $file);
        }
    }

    public function addToCollection(HasMedia $model, string $collection, UploadedFile $file): Media
    {
        return $model->addMedia($file)->toMediaCollection($collection);
    }

    /**
     * Replace the first media item while keeping the rest of the gallery.
     */
    public function replacePrimary(HasMedia $model, string $collection, UploadedFile $file): Media
    {
        $existing = $model->getMedia($collection);
        $otherIds = $existing->skip(1)->pluck('id')->map(fn ($id) => (int) $id)->all();
        $existing->first()?->delete();

        $primary = $this->addToCollection($model, $collection, $file);

        if ($otherIds !== []) {
            Media::setNewOrder(array_merge([$primary->id], $otherIds));
        }

        return $primary;
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
