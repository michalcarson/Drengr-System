<?php

namespace Drengr\App;

use Drengr\Framework\Plugin;

class Client extends Plugin
{
    public function register()
    {
        require_once(__DIR__ . '/../react/enqueue.php');
        require_once(__DIR__ . '/../react/shortcode.php');
    }
}
