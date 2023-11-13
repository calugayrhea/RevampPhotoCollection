<?php

namespace App\Repositories;

use App\Models\Photo;

class PhotoRepository
{
    public function createPhoto(array $data)
    {
        return Photo::create($data);
    }

    public function getPhotoCountByCollectionId($collectionId)
    {
        return Photo::where('collection_id', $collectionId)->count();
    }

    public function getFilePathsByCollectionId($collectionId)
    {
        return Photo::where('collection_id', $collectionId)->pluck('file_path');
    }


    public function getPhotosByCollectionId($collectionId)
    {
        return Photo::where('collection_id', $collectionId)->with('collection')->get();
    }

    public function getPhotoById($photoId)
    {
        return Photo::with('collection')->find($photoId);
    }
    public function deletePhoto($photoId)
    {
        $photo = Photo::find($photoId);

        if (!$photo) {
            return false;
        }

        $photo->delete();

        return true;
    }
}
