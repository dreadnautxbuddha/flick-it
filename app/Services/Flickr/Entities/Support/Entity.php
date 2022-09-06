<?php

namespace App\Services\Flickr\Entities\Support;

use Illuminate\Contracts\Support\Arrayable;

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
     * @inheritDoc
     */
    public function toArray(): array
    {
        return $this->attributes;
    }
}
