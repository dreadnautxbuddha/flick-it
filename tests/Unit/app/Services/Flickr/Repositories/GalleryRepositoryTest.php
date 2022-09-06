<?php

namespace Tests\Unit\app\Services\Flickr\Repositories;

use App\Models\User;
use App\Services\Flickr\Entities\Collection;
use App\Services\Flickr\Entities\Gallery;
use App\Services\Flickr\Repositories\Support\Contracts\GalleryRepository;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use function app;
use function collect;
use function env;
use function route;

class GalleryRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->be($this->user);
    }

    /**
     * Verifies that the gallery repository returns its records using Flickr's
     * Gallery REST API
     *
     * @return void
     */
    public function testGet_shouldReturnGalleryCollectionFromFlickrApi()
    {
        $url = 'https://www.flickr.com/services/rest/?method=flickr.galleries.getList&per_page=100&continuation=0';
        $response = Http::response([
            'galleries' => [
                'total' => 967,
                'per_page' => 967,
                'user_id' => '66956608@N06',
                'page' => 1,
                'pages' => 1,
                'gallery' => [
                    ['id' => 'my-gallery'],
                ],
            ],
        ]);
        Http::fake([$url => $response]);

        /** @var GalleryRepository $repo */
        $repo = app(GalleryRepository::class);
        $galleries = $repo->get();

        $gallery = new Gallery(['id' => 'my-gallery']);
        $collection = new Collection(collect([$gallery]), 1, 1, 967, 967);
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
        $url = 'https://www.flickr.com/services/rest/?method=flickr.galleries.getList';
        $response = Http::response([
            'galleries' => [
                'total' => 967,
                'per_page' => 967,
                'user_id' => '66956608@N06',
                'page' => 1,
                'pages' => 1,
                'gallery' => [
                    ['id' => 'my-gallery'],
                ],
            ],
        ]);
        Http::fake([$url => $response]);

        /** @var GalleryRepository $repo */
        $repo = app(GalleryRepository::class);
        $galleries = $repo->get(null, '12345');

        $gallery = new Gallery(['id' => 'my-gallery']);
        $collection = new Collection(collect([$gallery]), 1, 1, 967, 967);
        $this->assertEquals($collection, $galleries);
    }

    /**
     * Verifies that when the REST API returns missing data, e.g., no `page`,
     * `pages`, etc., null will be instead used. When `gallery` is missing, an empty
     * collection will be used.
     *
     * @return void
     */
    public function testGet_whenApiReturnsMissingData_shouldUseFallbackValues()
    {
        $url = 'https://www.flickr.com/services/rest/?method=flickr.galleries.getList&per_page=100&continuation=0';
        $response = Http::response([]);
        Http::fake([$url => $response]);

        /** @var GalleryRepository $repo */
        $repo = app(GalleryRepository::class);
        $galleries = $repo->get();

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

        /** @var GalleryRepository $repo */
        $repo = app(GalleryRepository::class);
        $galleries = $repo->get();

        $collection = new Collection(collect([]));
        $this->assertEquals($collection, $galleries);
    }
}
