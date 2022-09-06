<?php

namespace App\Services\Flickr\Entities;

use function sprintf;

/**
 * Represents a Flickr Photo
 *
 * @package App\Services\Flickr\Entities
 *
 * @author  Peter Cortez <innov.petercortez@gmail.com>
 */
class Photo extends Support\Entity
{
    /**
     * The image url format to a custom size
     *
     * Find more about it {@link https://www.flickr.com/services/api/misc.urls.html here}
     *
     * @const string
     */
    const URL_FORMAT_WITH_SIZE = 'https://live.staticflickr.com/%s/%s_%s_%s.jpg';

    /**
     * Returns a URL to the full-sized version of the photo.
     *
     * @return string
     */
    public function originalUrl(): string
    {
        return sprintf(
            self::URL_FORMAT_WITH_SIZE,
            $this->get('server'),
            $this->get('id'),
            $this->get('secret'),
            'b'
        );
    }

    /**
     * Returns a URL to the thumbnail version of the photo.
     *
     * @return string
     */
    public function thumbnailUrl(): string
    {
        return sprintf(
            self::URL_FORMAT_WITH_SIZE,
            $this->get('server'),
            $this->get('id'),
            $this->get('secret'),
            'm'
        );
    }
}
