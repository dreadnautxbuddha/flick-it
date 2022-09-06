<?php

namespace App\Http\Controllers;

use App\Services\Flickr\Repositories\Support\Contracts\GalleryRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use function is_numeric;

/**
 * Returns all gallery categories available to a user
 *
 * @package App\Http\Controllers
 *
 * @author  Peter Cortez <innov.petercortez@gmail.com>
 */
class GalleriesController extends Controller
{
    /**
     * @param \Illuminate\Http\Request                                              $request
     * @param \App\Services\Flickr\Repositories\Support\Contracts\GalleryRepository $gallery
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(
        Request $request,
        GalleryRepository $gallery
    ): JsonResponse {
        $perPage = $request->get('per_page', 100);
        $continuation = $request->get('continuation', '0');

        $data = $gallery
            ->get(is_numeric($perPage) ? $perPage : 100, $continuation)
            ->toArray();

        return new JsonResponse(['data' => $data]);
    }
}
