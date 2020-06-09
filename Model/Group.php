<?php

namespace Drengr\Model;

class Group implements ListableModel
{
    public const PLURAL = 'Groups';
    public const SINGULAR = 'Group';

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

    public static function getQuery($prefix)
    {
        return "SELECT * FROM {$prefix}drengr_group";
    }
}
