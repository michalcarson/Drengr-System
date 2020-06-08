<?php

namespace Drengr\Model;

class Group implements ListableModel
{
    public static function getPlural()
    {
        return 'Groups';
    }

    public static function getSingular()
    {
        return 'Group';
    }

    public static function getColumns()
    {
        return [
            'cb' => 'cb',
            'created_at' => 'Created',
            'id' => 'Id',
            'name' => 'Name',
            'url' => 'URL',
            'row_actions' => 'Actions',
        ];
    }

    public static function getSortableColumns()
    {
        return [
            'name' => ['name', false]
        ];
    }
}
