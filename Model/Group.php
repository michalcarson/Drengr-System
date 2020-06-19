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
            'name' => 'Name',
            'url' => 'URL',
            'sturaesman' => 'Sturaesman',
            'authenticity_officer' => 'Authenticity Officer',
            'training_officer' => 'Training Officer',
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
            'edit-action-class' => '<a href="/drengr/group/edit/' . $item->id . '">Edit</a>',
        ];
    }

    public static function getQuery($prefix)
    {
        return "select 
                g.*, 
                s.name sturaesman, 
                ao.name authenticity_officer, 
                t.name training_officer 
            from {$prefix}drengr_group g
            left outer join {$prefix}drengr_member s on g.sturaesman = s.id 
            left outer join {$prefix}drengr_member ao on g.authenticity_officer = ao.id 
            left outer join {$prefix}drengr_member t on g.training_officer = t.id 
        ";
    }
}
