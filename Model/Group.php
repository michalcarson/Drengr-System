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
            'id' => 'Id',
            'name' => 'Name',
            'url' => 'URL',
            'created_at' => 'Created',
        ];
    }

    public static function getSortableColumns()
    {
        return [
            'name' => ['name', false]
        ];
    }

    public static function getHiddenColumns()
    {
        return [];
    }

    public static function getRowActions($item)
    {
        return [
            'edit-action-class' => '<a href="edit.php?id=' . $item->id . '">Edit</a>',
        ];
    }

    public static function getQuery($prefix)
    {
        return "SELECT * FROM {$prefix}drengr_group";
    }
}
