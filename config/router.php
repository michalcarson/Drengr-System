<?php

use Drengr\Framework\View;

return [
    'config' => [
        'namespace' => 'drengr',
    ],
    'routes' => function ($router, $container) {
        $router->get('group/read/{id}', 'read_group', function ($id) use ($container) {

            $view = $container->get(View::class);
            $view->display(
                'Group/Edit',
                [
                    'id' => $id,
                ]
            );
        });
    }
];
