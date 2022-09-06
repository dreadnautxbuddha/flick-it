<?php

namespace App\Http\Controllers;

use App\Services\Flickr\Repositories\Support\Contracts\PhotoRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use function app;
use function is_numeric;

/**
 * @package App\Http\Controllers
 *
 * @author  Peter Cortez <innov.petercortez@gmail.com>
 */
class PhotosController extends Controller
{
    /**
     * Returns all photos under a specific gallery available to a user
     *
     * @param string                   $galleryId
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(string $galleryId, Request $request): JsonResponse
    {
        $perPage = $request->get('per_page');
        $continuation = $request->get('continuation', '0');
        /** @var \App\Services\Flickr\Repositories\Support\Contracts\PhotoRepository $photos */
        $photos = app(PhotoRepository::class, ['galleryId' => $galleryId]);

        $data = $photos
            ->get(is_numeric($perPage) ? $perPage : 100, $continuation)
            ->toArray();

        return new JsonResponse(['data' => $data]);
    }

    /**
     * Returns all information about a photo
     *
     * @param string $galleryId
     * @param string $photoId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $galleryId, string $photoId): JsonResponse
    {
        /** @var \App\Services\Flickr\Repositories\Support\Contracts\PhotoRepository $photos */
        $photos = app(PhotoRepository::class, ['galleryId' => $galleryId]);

        return new JsonResponse(['data' => $photos->find($photoId)->toArray()]);
    }
}
