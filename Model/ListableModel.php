<?php

namespace Drengr\Model;

interface ListableModel
{
    /**
     * Return an array of columns to be displayed in the list.
     *
     * Each entry is
     *      'column_name' => 'column heading'
     *
     * @return array
     */
    public static function getColumns();

    /**
     * Return an array of columns that should be sortable in the list.
     *
     * Each entry is
     *      'column_name' => [
     *          'database_field_name',
     *          false // or true if the query is already sorted by this field
     *      ]
     *
     * @return array
     */
    public static function getSortableColumns();

    /**
     * Return the SQL query for listing the entity.
     *
     * @param string $prefix The WP database table prefix.
     * @return string A SQL query
     */
    public static function getQuery($prefix);

    public static function getHiddenColumns();

    public static function getRowActions($item);
}
