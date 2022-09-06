<?php

namespace Tests\Unit\app\Services\Flickr\Entities;

use App\Models\User;
use App\Services\Flickr\Entities\Collection;
use App\Services\Flickr\Entities\Gallery;
use App\Services\Flickr\Entities\Photo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use function collect;

class GalleryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->be($this->user);
    }

    /**
     * Verifies that the gallery returns photos from the Flickr Photos API
     *
     * @return void
     */
    public function testPhotos_makesRequestToFlickrPhotosApi()
    {
        $url = 'https://www.flickr.com/services/rest/?method=flickr.galleries.getPhotos&gallery_id=my-gallery-id&per_page=100&continuation=0';
        $response = Http::response([
            'photos' => [
                'total' => 20,
                'perpage' => 20,
                'page' => 1,
                'pages' => 1,
                'photo' => [
                    ['id' => 'my-photo'],
                ],
            ],
        ]);
        Http::fake([$url => $response]);

        $gallery = new Gallery(['gallery_id' => 'my-gallery-id']);
        $photos = $gallery->photos();

        $photo = new Photo(['id' => 'my-photo']);
        $collection = new Collection(collect([$photo]), 1, 1, 20, 20);
        $this->assertEquals($collection, $photos);
    }
}
