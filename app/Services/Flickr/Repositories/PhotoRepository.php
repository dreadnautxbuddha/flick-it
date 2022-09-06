<?php

namespace App\Services\Flickr\Repositories;

use App\Services\Flickr\Entities\Collection;
use App\Services\Flickr\Entities\Photo;
use App\Services\Flickr\Entities\Support\Entity;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use function collect;

/**
 * Represents a repository that fetches photos from Flickr using their REST API
 *
 * @package App\Services\Flickr\Repositories
 *
 * @author  Peter Cortez <innov.petercortez@gmail.com>
 */
class PhotoRepository implements Support\Contracts\PhotoRepository
{
    /**
     * @var string
     */
    protected $galleryId;

    public function __construct(string $galleryId)
    {
        $this->galleryId = $galleryId;
    }

    /**
     * @inheritDoc
     */
    public function get(?int $limit = 100, string $offset = '0'): Collection
    {
        $params = [
            'method' => 'flickr.galleries.getPhotos',
            'gallery_id' => $this->galleryId,
        ];

        if ($limit !== null) {
            // Flickr will ignore the null `per_page` unless you remove
            // `continuation` as well so here, we're removing both.
            $params['per_page'] = $limit;
            $params['continuation'] = $offset;
        }

        try {
            $request = Http::flickr()->get('', $params)->json();
        } catch (Exception $e) {
            Log::error($e->getMessage());

            $request = [];
        }

        return new Collection(
            collect(Arr::get($request, 'photos.photo'))->mapInto(
                Photo::class
            ),
            Arr::get($request, 'photos.page'),
            Arr::get($request, 'photos.pages'),
            // Not sure why, but the Photos api return `perpage` instead of
            // `per_page` like in the gallery repository
            Arr::get($request, 'photos.perpage'),
            Arr::get($request, 'photos.total')
        );
    }

    /**
     * @inheritDoc
     */
    public function find(string $id): ?Entity
    {
        try {
            $request = Http::flickr()
                ->get('', ['method' => 'flickr.photos.getInfo', 'photo_id' => $id])
                ->json();
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return null;
        }

        return new Photo(Arr::get($request, 'photo', []));
    }
}
