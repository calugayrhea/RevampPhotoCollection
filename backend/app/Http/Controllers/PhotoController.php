<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\PhotoRequest;
use App\Models\Photo;
use App\Repositories\PhotoRepository;
use Illuminate\Support\Facades\Storage;
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
        $uploadedPhotos = [];
        $maxPhotos = 5;
    
        $existingPhotosCount = Photo::where('collection_id', $collectionId)->count();
        $remainingSlots = $maxPhotos - $existingPhotosCount;
    
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
                        'file_path' => $photo->getClientOriginalName(), // Example: save the file name
                    ]);
    
                    $uploadedPhotos[] = $newPhoto;
                }
            }
        }
    
        return ApiEcho::response(HttpStatusCode::Created, 'Successfully uploaded photos', $uploadedPhotos);
    }
    

    public function index(Request $request, $collectionId)
{
    $photos = $this->photoRepository->getFilePathsByCollectionId($collectionId);

    return ApiEcho::response(HttpStatusCode::OK, 'List of Photo Paths', $photos);
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
