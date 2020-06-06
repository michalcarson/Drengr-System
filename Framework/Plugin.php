<?php

namespace Drengr\Framework;

abstract class Plugin
{
    abstract public function register();

    protected function slugify($title)
    {
        return strtolower(str_replace(' ', '-', $title));
    }
}
