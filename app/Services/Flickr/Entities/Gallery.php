<?php

namespace App\Services\Flickr\Entities;

use function app;
use App\Services\Flickr\Repositories\Support\Contracts\PhotoRepository;

/**
 * Represents a Flickr Gallery
 *
 * @package App\Services\Flickr\Entities
 *
 * @author  Peter Cortez <innov.petercortez@gmail.com>
 */
class Gallery extends Support\Entity
{
    /**
     * Returns the photos of this gallery.
     *
     * @param int|null $limit
     * @param string   $offset
     *
     * @return \App\Services\Flickr\Entities\Collection
     */
    public function photos(?int $limit = 100, string $offset = '0'): Collection
    {
        $params = ['galleryId' => $this->get('gallery_id')];
        /** @var \App\Services\Flickr\Repositories\Support\Contracts\PhotoRepository $photos */
        $photos = app(PhotoRepository::class, $params);

        return $photos->get($limit, $offset);
    }
}
