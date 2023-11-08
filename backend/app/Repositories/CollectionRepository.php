<?php

namespace App\Repositories;
use App\Models\Collection;

class CollectionRepository
{
    public function __construct()
    {
        //
    }

    public function createCollection($data)
    {
        return Collection::create($data);
    }

    public function getAllCollections()
    {
        return Collection::all();
    }

    public function getCollectionById($id)
    {
        return Collection::find($id);
    }

    public function updateCollection($collection, $data)
    {
        return $collection->update($data);
    }

    public function deleteCollection($collection)
    {
        return $collection->delete();
    }
}

