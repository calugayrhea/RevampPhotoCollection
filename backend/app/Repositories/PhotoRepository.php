<?php

namespace App\Repositories;

use App\Models\Photo;
use Illuminate\Support\Facades\Storage;

class PhotoRepository
{
    public function createPhoto(array $data)
    {
        $path = $data['file_path'];

        $photo = new Photo([
            'collection_id' => $data['collection_id'],
            'file_path' => $path,
        ]);
        $photo->save();

        return $photo;
    }

    public function getFilePathsByCollectionId($collectionId)
    {
        return Photo::where('collection_id', $collectionId)->pluck('file_path');
    }

  

    public function deletePhoto($photoId)
    {
        $photo = Photo::find($photoId);

        if (!$photo) {
            return false;
        }

        Storage::delete($photo->file_path);
        $photo->delete();

        return true;
    }
}
