<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\PhotoRequest;
use App\Repositories\PhotoRepository;
use App\Support\ApiEcho;
use Illuminate\Http\Request;
use PrinsFrank\Standards\Http\HttpStatusCode;

class PhotoController extends Controller
{
    private PhotoRepository $photoRepository;

    public function __construct(PhotoRepository $photoRepository)
    {
        $this->photoRepository = $photoRepository;
    }

    public function store(PhotoRequest $request, $collectionId)
    {
        $maxPhotos = 5;

        $existingPhotosCount = $this->photoRepository->getPhotoCountByCollectionId($collectionId);
        $remainingSlots = $maxPhotos - $existingPhotosCount;

        if ($remainingSlots <= 0) {
            return ApiEcho::response(
                HttpStatusCode::Bad_Request,
                'Max photos exceeded. You cannot upload more photos to this collection.'
            );
        }

        $uploadedPhotos = [];

        if ($request->hasFile('photos')) {
            $uploadedFiles = $request->file('photos');

            if (!is_array($uploadedFiles)) {
                $uploadedFiles = [$uploadedFiles];
            }

            $uploadedFileCount = count($uploadedFiles);

            if ($uploadedFileCount > $remainingSlots) {
                return ApiEcho::response(
                    HttpStatusCode::Bad_Request,
                    'Max photos exceeded. You can only upload ' . $remainingSlots . ' more photos.'
                );
            }

            foreach ($uploadedFiles as $photo) {
                if ($photo->isValid()) {
                    $newPhoto = $this->photoRepository->createPhoto([
                        'collection_id' => $collectionId,
                        'file_path' => $photo->getClientOriginalName(),
                    ]);

                    $uploadedPhotos[] = $newPhoto;
                }
            }
        }

        return ApiEcho::response(HttpStatusCode::Created, 'Successfully uploaded photos', $uploadedPhotos);
    }


    public function index(Request $request, $collectionId)
    {
        $photos = $this->photoRepository->getPhotosByCollectionId($collectionId);
    
        if ($photos->isEmpty()) {
            return ApiEcho::response(HttpStatusCode::OK, 'No Photos available for the specified collection', []);
        }
    
        return ApiEcho::response(HttpStatusCode::OK, 'List of Photos', $photos);
    }
    

    public function show(Request $request, $collectionId, $photoId)
    {
        $photo = $this->photoRepository->getPhotoById($photoId);
    
        if (!$photo || $photo->collection_id != $collectionId) {
            return ApiEcho::response(HttpStatusCode::Not_Found, 'Photo not found in the specified collection');
        }
        \Log::info("Retrieved photo details for ID $photoId in collection $collectionId: " . json_encode([
            'id' => $photo->id,
            'file_path' => $photo->file_path,
        ]));
    
        $photoContent = base64_decode($photo->file_path);
    
        return response($photoContent)->header('Content-Type', 'image/png');
    }
    
    public function getPhotoById(Request $request, $collectionId, $photoId)
    {
        $photo = $this->photoRepository->getPhotoById($photoId);

        if (!$photo || $photo->collection_id != $collectionId) {
            return ApiEcho::response(HttpStatusCode::Not_Found, 'Photo not found in the specified collection');
        }

        return ApiEcho::response(HttpStatusCode::OK, 'Photo details', $photo);
    }

    public function destroy(Request $request, $collectionId, $photoId)
    {
        $result = $this->photoRepository->deletePhoto($photoId);

        if ($result === false) {
            return ApiEcho::response(HttpStatusCode::Not_Found, 'Photo not found');
        }

        return ApiEcho::response(HttpStatusCode::OK, 'Photo deleted successfully');
    }
}
