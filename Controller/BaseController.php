<?php

namespace Drengr\Controller;

abstract class BaseController
{
    public function __call($name, $args)
    {
        return [
            'respond_to' => [
                'json' => [
                    'error' => sprintf('`%s` method is not defined.', $name)
                ],
                'html' => function () {
                    include get_404_template();
                }
            ]
        ];
    }
}
