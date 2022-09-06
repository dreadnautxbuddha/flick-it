<?php

namespace Tests\Unit\app\Http\Controllers;

use App\Models\User;
use App\Services\Flickr\Entities\Collection;
use App\Services\Flickr\Entities\Photo;
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
    }

    /**
     * Verifies that when accessing gallery photos while not authenticated, the user
     * is redirected back to the login page.
     *
     * @return void
     */
    public function testIndex_whenNotLoggedIn_shouldRedirectToLoginPage()
    {
        $response = $this->get(route('gallery.photos', ['galleryId' => 1234]));

        $response->assertRedirect(route('auth.login'));
    }

    /**
     * Verifies that when requesting for the photos, only the first 100 photos
     * are returned unless the appropriate GET parameters are set to modify it.
     *
     * @return void
     */
    public function testIndex_byDefault_shouldLimitReturnedPhotosTo100()
    {
        $this->be($this->user);
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
        $this->be($this->user);
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
        $this->be($this->user);
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
        $this->be($this->user);
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

    /**
     * Verifies that when accessing gallery photos' infor while not authenticated,
     * the user is redirected back to the login page.
     *
     * @return void
     */
    public function testShow_whenNotLoggedIn_shouldRedirectToLoginPage()
    {
        $response = $this->get(
            route('gallery.photos.info', ['galleryId' => 1234, 'photoId' => 1234])
        );

        $response->assertRedirect(route('auth.login'));
    }

    /**
     * Verifies that when getting a photo's information, it returns the data from our
     * Photos repository
     *
     * @return void
     */
    public function testShow_shouldReturnPhotoFromRepository()
    {
        $this->be($this->user);
        $photo = new Photo(['id' => 'photo-id']);
        $this->app->bind(
            PhotoRepository::class,
            function () use ($photo) {
                return $this->partialMock(
                    PhotoRepository::class,
                    function (Mockery\MockInterface $mock) use ($photo) {
                        $mock
                            ->shouldReceive('find')
                            ->with('photo-id')
                            ->once()
                            ->andReturn($photo);
                    }
                );
            }
        );

        $request = $this->get(
            route('gallery.photos.info', ['galleryId' => 12345, 'photoId' => 'photo-id'])
        );

        $this->assertEquals(
            ['data' => $photo->toArray()],
            $request->getOriginalContent()
        );
    }
}
