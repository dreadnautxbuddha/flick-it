<?php

namespace Tests\Unit\app\Services\Flickr\Entities;

use App\Services\Flickr\Entities\Photo;
use Tests\TestCase;

class PhotoTest extends TestCase
{
    /**
     * Verifies that the thumbnail url of a photo has the correct format.
     *
     * @return void
     */
    public function testThumbnailUrl_shouldReturnCorrectValue()
    {
        $attributes = [
            'server' => 'server-id',
            'id' => 'id',
            'secret' => 'secret',
        ];

        $photo = new Photo($attributes);

        $this->assertEquals('https://live.staticflickr.com/server-id/id_secret_m.jpg', $photo->thumbnailUrl());
    }

    /**
     * Verifies that the thumbnail url of a photo will be broken if it has missing
     * url attributes
     *
     * @return void
     */
    public function testThumbnailUrl_whenAtleastOneOfRequiredFieldsAreMissing_shouldUseNull()
    {
        $attributes = [];

        $photo = new Photo($attributes);

        $this->assertEquals('https://live.staticflickr.com//__m.jpg', $photo->thumbnailUrl());
    }

    /**
     * Verifies that the original url of a photo has the correct format.
     *
     * @return void
     */
    public function testOriginalUrl_shouldReturnCorrectValue()
    {
        $attributes = [
            'server' => 'server-id',
            'id' => 'id',
            'secret' => 'secret',
        ];

        $photo = new Photo($attributes);

        $this->assertEquals('https://live.staticflickr.com/server-id/id_secret_b.jpg', $photo->originalUrl());
    }

    /**
     * Verifies that the original url of a photo will be broken if it has missing
     * url attributes
     *
     * @return void
     */
    public function testOriginalUrl_whenAtleastOneOfRequiredFieldsAreMissing_shouldUseNull()
    {
        $attributes = [];

        $photo = new Photo($attributes);

        $this->assertEquals('https://live.staticflickr.com//__b.jpg', $photo->originalUrl());
    }

    /**
     * Verifies that the `thumbnail_url` and `original_url` is being returned when
     * casting to an array
     *
     * @return void
     */
    public function testToArray_shouldReturnThumbnailUrlAndOriginalUrl()
    {
        $attributes = [
            'server' => 'server-id',
            'id' => 'id',
            'secret' => 'secret',
        ];

        $photo = new Photo($attributes);
        $array = $photo->toArray();

        $this->assertEquals('https://live.staticflickr.com/server-id/id_secret_b.jpg', $array['original_url']);
        $this->assertEquals('https://live.staticflickr.com/server-id/id_secret_m.jpg', $array['thumbnail_url']);
    }
}
