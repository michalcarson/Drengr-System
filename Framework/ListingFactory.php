<?php

namespace Drengr\Framework;

class ListingFactory
{
    /** @var \wpdb */
    private $wpdb;

    public function __construct(\wpdb $wpdb)
    {
        $this->wpdb = $wpdb;
    }

    public function create(string $class)
    {
        return new Listing($class, $this->wpdb);
    }
}
