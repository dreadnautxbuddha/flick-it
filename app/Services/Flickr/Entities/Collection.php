<?php

namespace App\Services\Flickr\Entities;

/**
 * Represents a collection of data returned from a repository.
 *
 * @package App\Services\Flickr\Entities
 *
 * @author  Peter Cortez <innov.petercortez@gmail.com>
 */
class Collection
{
    /**
     * The current page we're at.
     *
     * @var int|null
     */
    protected $page;

    /**
     * The total number of pages for a specific entity
     *
     * @var int|null
     */
    protected $pages;

    /**
     * The total number of pages per entity.
     *
     * @var int|null
     */
    protected $perPage;

    /**
     * The total number of records of an entity.
     *
     * @var int|null
     */
    protected $total;

    /**
     * The records in this collection
     *
     * @var \Illuminate\Support\Collection
     */
    protected $records;

    /**
     * @param \Illuminate\Support\Collection $records
     * @param int|null                       $page
     * @param int|null                       $pages
     * @param int|null                       $perPage
     * @param int|null                       $total
     */
    public function __construct(
        \Illuminate\Support\Collection $records,
        ?int $page = null,
        ?int $pages = null,
        ?int $perPage = null,
        ?int $total = null
    ) {
        $this->records = $records;
        $this->page = $page;
        $this->pages = $pages;
        $this->perPage = $perPage;
        $this->total = $total;
    }

    /**
     * Returns the collection of records associated with this collection
     *
     * @return \Illuminate\Support\Collection
     */
    public function getRecords(): \Illuminate\Support\Collection
    {
        return $this->records;
    }
}
