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

        $photos = $this->repo->get();

        $photo = new Photo(['id' => 'my-photo']);
        $collection = new Collection(collect([$photo]), 1, 1, 20, 20);
        $this->assertEquals($collection, $photos);
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

        $photos = $this->repo->get(null, '12345');

        $photo = new Photo(['id' => 'my-photo']);
        $collection = new Collection(collect([$photo]), 1, 1, 20, 20);
        $this->assertEquals($collection, $photos);
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

        $photos = $this->repo->get();

        $collection = new Collection(collect([]));
        $this->assertEquals($collection, $photos);
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

        $photos = $this->repo->get();

        $collection = new Collection(collect([]));
        $this->assertEquals($collection, $photos);
    }

    /**
     * Verifies that Photo::find returns the photo returned by Flickr
     *
     * @return void
     */
    public function testFind_shouldReturnPhotoInformationFromFlickrApi()
    {
        $url = 'https://www.flickr.com/services/rest/?method=flickr.photos.getInfo&photo_id=photo-id';
        $response = Http::response([
            'photo' => [
                'id' => 'photo-id',
            ],
        ]);
        Http::fake([$url => $response]);

        $photo = $this->repo->find('photo-id');

        $this->assertEquals(new Photo(['id' => 'photo-id']), $photo);
    }

    /**
     * Verifies that Photo::find returns null when the API request throws an
     * exception
     *
     * @return void
     */
    public function testFind_whenApiRequestThrowsException_shouldReturnNull()
    {
        Http::shouldReceive('flickr->get')->andThrow(new Exception());

        $photo = $this->repo->find('');

        $this->assertNull($photo);
    }

    /**
     * Verifies that even if when the `photo` key doesn't exist in the API's response,
     * we're still able to return an empty Photo object.
     *
     * @return void
     */
    public function testFind_whenApiReturnsMissingData_shouldUseFallbackValues()
    {
        $url = 'https://www.flickr.com/services/rest/?method=flickr.photos.getInfo&photo_id=photo-id';
        $response = Http::response([]);
        Http::fake([$url => $response]);

        $photo = $this->repo->find('');

        $this->assertEquals(new Photo([]), $photo);
    }
}
