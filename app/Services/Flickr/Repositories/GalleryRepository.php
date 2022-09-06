<?php

namespace App\Services\Flickr\Repositories;

use App\Services\Flickr\Entities\Collection;
use App\Services\Flickr\Entities\Gallery;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use function collect;
use function is_null;

/**
 * Represents a repository that fetches galleries from Flickr using their REST API
 *
 * @package App\Services\Flickr\Repositories
 *
 * @author  Peter Cortez <innov.petercortez@gmail.com>
 */
class GalleryRepository implements Support\Contracts\GalleryRepository
{
    /**
     * @inheritDoc
     */
    public function get(?int $limit = 100, string $offset = '0'): Collection
    {
        $params = ['method' => 'flickr.galleries.getList'];

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
            collect(Arr::get($request, 'galleries.gallery'))->mapInto(
                Gallery::class
            ),
            Arr::get($request, 'galleries.page'),
            Arr::get($request, 'galleries.pages'),
            Arr::get($request, 'galleries.per_page'),
            Arr::get($request, 'galleries.total')
        );
    }
}
