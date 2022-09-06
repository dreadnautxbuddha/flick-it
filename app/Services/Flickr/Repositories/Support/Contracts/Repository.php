<?php

namespace App\Services\Flickr\Repositories\Support\Contracts;

use App\Services\Flickr\Entities\Collection;
use App\Services\Flickr\Entities\Support\Entity;

/**
 * @package App\Services\Flickr\Repositories\Support\Contracts
 *
 * @author  Peter Cortez <innov.petercortez@gmail.com>
 */
interface Repository
{
    /**
     * Returns a collection of records from Flickr.
     *
     * The offset is set to a string
     * because is based off of Flickr's `continuation` query string parameter, which
     * looks like this: `7b2276616c7565223a7b226b223a22636f6e74696e756174696f6e5f67616c6c65726965736c6973743463373663613430666561343137613966643564316330396264353336366163222c2270223a3130307d7d`.
     * They use this for pagination as the offset
     *
     * @param int|null $limit
     * @param string   $offset
     *
     * @return \App\Services\Flickr\Entities\Collection
     */
    public function get(?int $limit = 100, string $offset = '0'): Collection;

    /**
     * Returns a single entity from Flickr.
     *
     * @param string $id
     *
     * @return \App\Services\Flickr\Entities\Support\Entity|null
     */
    public function find(string $id): ?Entity;
}
