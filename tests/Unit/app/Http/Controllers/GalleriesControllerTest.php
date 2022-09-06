<?php

namespace Tests\Unit\app\Http\Controllers;

use App\Models\User;
use App\Services\Flickr\Entities\Collection;
use App\Services\Flickr\Repositories\Support\Contracts\GalleryRepository;
use App\Services\Flickr\Repositories\Support\Contracts\PhotoRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Mockery\MockInterface;
use Tests\TestCase;
use function collect;
use function route;

class GalleriesControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->be($this->user);
    }

    /**
     * Verifies that when requesting for the galleries, only the first 100 galleries
     * are returned unless the appropriate GET parameters are set to modify it.
     *
     * @return void
     */
    public function testGalleriesController_byDefault_shouldLimitReturnedGalleriesTo100()
    {
        $mock = $this->partialMock(
            GalleryRepository::class,
            function (MockInterface $mock) {
                $mock
                    ->shouldReceive('get')
                    ->with(100, '0')
                    ->once();
            }
        );

        $this->app->bind(GalleryRepository::class, function () use ($mock) {
            return $mock;
        });

        $this->get(route('gallery'));
    }

    /**
     * Verifies that the gallery categories that we're returning can be paginated
     * using the `per_page` and `continuation`.
     *
     * @return void
     */
    public function testGalleriesController_shouldReadPaginateUsingQueryParameters()
    {
        $mock = $this->partialMock(
            GalleryRepository::class,
            function (MockInterface $mock) {
                $mock
                    ->shouldReceive('get')
                    ->with(500, 'continuation-hash')
                    ->once();
            }
        );

        $this->app->bind(GalleryRepository::class, function () use ($mock) {
            return $mock;
        });

        $this->get(
            route('gallery', [
                'per_page' => 500,
                'continuation' => 'continuation-hash',
            ])
        );
    }

    /**
     * Verifies that when `per_page` is not an integer, the controller will
     * automatically limit the collection size to 100
     *
     * @return void
     */
    public function testGalleriesController_whenPerPageIsNotInteger_shouldLimitTo100()
    {
        $mock = $this->partialMock(
            GalleryRepository::class,
            function (MockInterface $mock) {
                $mock
                    ->shouldReceive('get')
                    ->with(100, '0')
                    ->once();
            }
        );

        $this->app->bind(GalleryRepository::class, function () use ($mock) {
            return $mock;
        });

        $this->get(
            route('gallery', [
                'per_page' => 'an-invalid-integer',
            ])
        );
    }

    /**
     * Verifies that the photos controller returns the photos returned by our photos
     * repository
     *
     * @return void
     */
    public function testPhotosController_shouldReturnPhotosFromRepository()
    {
        $collection = new Collection(collect(['id' => 'gallery-id']));
        $this->app->bind(
            GalleryRepository::class,
            function () use ($collection) {
                return $this->partialMock(
                    GalleryRepository::class,
                    function (MockInterface $mock) use ($collection) {
                        $mock
                            ->shouldReceive('get')
                            ->once()
                            ->andReturn($collection);
                    }
                );
            }
        );

        $request = $this->get(route('gallery'));

        $this->assertEquals(
            ['data' => $collection->toArray()],
            $request->getOriginalContent()
        );
    }
}
