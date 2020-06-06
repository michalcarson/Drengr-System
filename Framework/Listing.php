<?php

namespace Drengr\Group;

class Listing extends \WP_List_Table
{
    public function __construct($class)
    {
        parent::__construct([
            'plural' => $class::getPlural(),
            'singular' => $class::getSingular(),
        ]);
    }

}
