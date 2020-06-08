<?php

namespace Drengr\Framework;

class ListingFactory
{
    /** @var \wpdb */
    private $wpdb;
    private $wp_col_headers;

    public function __construct(\wpdb $wpdb, $wp_col_headers)
    {
        $this->wpdb = $wpdb;
        $this->wp_col_headers = $wp_col_headers;
    }

    public function create(string $class)
    {
        return new Listing($class, $this->wpdb, $this->wp_col_headers);
    }
}
