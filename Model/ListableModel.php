<?php

namespace Drengr\Model;

interface ListableModel
{
    public static function getPlural();

    public static function getSingular();

    public static function getColumns();

    public static function getSortableColumns();
}
