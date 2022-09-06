<?php

namespace Tests\Unit\app\Http\Controllers;

use App\Models\User;
use App\Services\Flickr\Entities\Collection;
use App\Services\Flickr\Repositories\Support\Contracts\PhotoRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;
use function collect;
use function route;

class PhotosControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->be($this->user);
    }

    /**
     * Verifies that when requesting for the photos, only the first 100 photos
     * are returned unless the appropriate GET parameters are set to modify it.
     *
     * @return void
     */
    public function testIndex_byDefault_shouldLimitReturnedPhotosTo100()
    {
        $mock = $this->partialMock(
            PhotoRepository::class,
            function (Mockery\MockInterface $mock) {
                $mock
                    ->shouldReceive('get')
                    ->with(100, '0')
                    ->once();
            }
        );

        $this->app->bind(PhotoRepository::class, function () use ($mock) {
            return $mock;
        });

        $this->get(route('gallery.photos', ['galleryId' => 12345]));
    }

    /**
     * Verifies that the photos that we're returning can be paginated using the
     * `per_page` and `continuation`.
     *
     * @return void
     */
    public function testIndex_shouldReadPaginateUsingQueryParameters()
    {
        $mock = $this->partialMock(
            PhotoRepository::class,
            function (Mockery\MockInterface $mock) {
                $mock
                    ->shouldReceive('get')
                    ->with(500, 'continuation-hash')
                    ->once();
            }
        );

        $this->app->bind(PhotoRepository::class, function () use ($mock) {
            return $mock;
        });

        $this->get(
            route('gallery.photos', [
                'galleryId' => 12345,
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
    public function testIndex_whenPerPageIsNotInteger_shouldLimitTo100()
    {
        $mock = $this->partialMock(
            PhotoRepository::class,
            function (Mockery\MockInterface $mock) {
                $mock
                    ->shouldReceive('get')
                    ->with(100, '0')
                    ->once();
            }
        );

        $this->app->bind(PhotoRepository::class, function () use ($mock) {
            return $mock;
        });

        $this->get(
            route('gallery.photos', [
                'galleryId' => 12345,
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
    public function testIndex_shouldReturnPhotosFromRepository()
    {
        $collection = new Collection(collect(['id' => 'my-photos-id']));
        $this->app->bind(
            PhotoRepository::class,
            function () use ($collection) {
                return $this->partialMock(
                    PhotoRepository::class,
                    function (Mockery\MockInterface $mock) use ($collection) {
                        $mock
                            ->shouldReceive('get')
                            ->once()
                            ->andReturn($collection);
                    }
                );
            }
        );

        $request = $this->get(route('gallery.photos', ['galleryId' => 12345]));

        $this->assertEquals(
            ['data' => $collection->toArray()],
            $request->getOriginalContent()
        );
    }
}
