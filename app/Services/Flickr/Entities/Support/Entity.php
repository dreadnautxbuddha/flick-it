<?php

namespace App\Services\Flickr\Entities\Support;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;

class Entity implements Arrayable
{
    /**
     * @var array
     */
    protected $attributes;

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * Returns an attribute identified by its index. If not found, the default value
     * will be returned instead.
     *
     * @param $index
     * @param $default
     *
     * @return array|\ArrayAccess|mixed
     */
    public function get($index, $default = null)
    {
        return Arr::get($this->attributes, $index, $default);
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return $this->attributes;
    }
}
