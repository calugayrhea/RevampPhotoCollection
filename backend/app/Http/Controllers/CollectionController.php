<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateCollectionRequest;
use App\Http\Requests\StoreCollectionRequest;
use App\Repositories\CollectionRepository;
use App\Models\Collection;
use App\Support\ApiEcho;
use Illuminate\Http\Request;
use PrinsFrank\Standards\Http\HttpStatusCode;


class CollectionController extends Controller
{
    private CollectionRepository $repository;

    public function __construct(CollectionRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of collections.
     */
    public function index(Request $request)
    {
        $collections = $this->repository->getAllCollections();

        return ApiEcho::response(HttpStatusCode::OK, 'List of Collections', $collections);
    }

    /**
     * Store a newly created collection.
     */
    public function store(StoreCollectionRequest $request)
    {
        $data = $request->validated();
        $collection = $this->repository->createCollection($data);

        return ApiEcho::response(HttpStatusCode::Created, 'Successfully created a collection', $collection);
    }

    /**
     * Display the specified collection.
     */
    public function show($id)
    {
        $collection = $this->repository->getCollectionById($id);

        if (!$collection) {
            return ApiEcho::response(HttpStatusCode::Not_Found, 'Collection not found');
        }

        return ApiEcho::response(HttpStatusCode::OK, 'Show Collection', $collection);
    }

    /**
     * Update the specified collection.
     */
    public function update(UpdateCollectionRequest $request, $id)
    {
        $data = $request->validated();
        $collection = $this->repository->getCollectionById($id);

        if (!$collection) {
            return ApiEcho::response(HttpStatusCode::Not_Found, 'Collection not found');
        }

        if ($collection->owner_email !== $data['owner_email']) {
            return ApiEcho::response(HttpStatusCode::Forbidden, 'Only the owner can edit this collection');
        }

        $result = $this->repository->updateCollection($collection, $data);

        if ($result) {
            return ApiEcho::response(HttpStatusCode::OK, 'Successfully updated the collection', $result);
        } else {
            return ApiEcho::response(HttpStatusCode::Forbidden, 'Failed to update the collection');
        }
    }

    /**
     * Remove the specified collection.
     */
    public function destroy($id)
    {
        $collection = $this->repository->getCollectionById($id);
    
        if (!$collection) {
            return ApiEcho::response(HttpStatusCode::Not_Found, 'Collection not found');
        }
    
        $result = $this->repository->deleteCollection($collection);
    
        if ($result) {
            return ApiEcho::response(HttpStatusCode::No_Content, 'Successfully deleted the collection');
        } else {
            return ApiEcho::response(HttpStatusCode::Internal_Server_Error, 'Failed to delete the collection');
        }
    }
}
