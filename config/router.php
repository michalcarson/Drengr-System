<?php

return [
    'config' => [
        'namespace' => 'drengr',
    ],
    'routes' => function ($router) {
        $router->get('group/edit/{id}', 'edit_group', function($id) {
            echo "edit group $id<br>";
        });
    }
];
