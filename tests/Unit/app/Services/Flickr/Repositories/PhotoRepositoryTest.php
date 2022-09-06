<?php

namespace Tests\Unit\app\Services\Flickr\Repositories;

use App\Models\User;
use App\Services\Flickr\Entities\Collection;
use App\Services\Flickr\Entities\Photo;
use App\Services\Flickr\Repositories\PhotoRepository;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use function app;
use function collect;

class PhotoRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->be($this->user);
        $this->repo = app(PhotoRepository::class, ['galleryId' => 'gallery-id']);
    }

    /**
     * Verifies that the photo repository returns its records using Flickr's Photo
     * REST API
     *
     * @return void
     */
    public function testGet_shouldReturnPhotoCollectionFromFlickrApi()
    {
        $url = 'https://www.flickr.com/services/rest/?method=flickr.galleries.getPhotos&gallery_id=gallery-id&per_page=100&continuation=0';
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

        $galleries = $this->repo->get();

        $photo = new Photo(['id' => 'my-photo']);
        $collection = new Collection(collect([$photo]), 1, 1, 20, 20);
        $this->assertEquals($collection, $galleries);
    }

    /**
     * Verifies that when the `limit` is null, `per_page` and `continuation` will not
     * be included in the query string parameters to Flickr
     *
     * @return void
     */
    public function testGet_whenLimitIsNull_shouldNotHavePerPageAndContinuation()
    {
        $url = 'https://www.flickr.com/services/rest/?method=flickr.galleries.getPhotos&gallery_id=gallery-id';
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

        $galleries = $this->repo->get(null, '12345');

        $photo = new Photo(['id' => 'my-photo']);
        $collection = new Collection(collect([$photo]), 1, 1, 20, 20);
        $this->assertEquals($collection, $galleries);
    }

    /**
     * Verifies that when the REST API returns missing data, e.g., no `page`,
     * `pages`, etc., null will be instead used. When `photo` is missing, an empty
     * collection will be used.
     *
     * @return void
     */
    public function testGet_whenApiReturnsMissingData_shouldUseFallbackValues()
    {
        $url = 'https://www.flickr.com/services/rest/?method=flickr.galleries.getPhotos&gallery_id=gallery-id&per_page=100&continuation=0';
        $response = Http::response([]);
        Http::fake([$url => $response]);

        $galleries = $this->repo->get();

        $collection = new Collection(collect([]));
        $this->assertEquals($collection, $galleries);
    }

    /**
     * Verifies that if for some reason, the API request to Flickr results in an
     * error, a collection object will still be returned, by with empty data, and an
     * empty records collection
     *
     * @return void
     */
    public function testGet_whenApiRequestThrowsException_shouldUseEmptyCollection()
    {
        Http::shouldReceive('flickr->get')->andThrow(new Exception());

        $galleries = $this->repo->get();

        $collection = new Collection(collect([]));
        $this->assertEquals($collection, $galleries);
    }
}
