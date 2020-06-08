<?php

namespace Drengr\Framework;

use Drengr\Model\Group;
use WP_List_Table;

class Listing extends WP_List_Table
{
    /** @var Group */
    protected $class;
    /** @var \wpdb */
    protected $wpdb;
    protected $wp_column_headers;

    public function __construct(string $class, \wpdb $wpdb, $wp_column_headers)
    {
        $this->class = $class;
        $this->wpdb = $wpdb;
        $this->wp_column_headers = $wp_column_headers;

        parent::__construct([
            'plural' => $class::getPlural(),
            'singular' => $class::getSingular(),
        ]);
    }

    public function get_columns()
    {
        return $this->class::getColumns();
    }

    public function get_sortable_columns()
    {
        return $this->class::getSortableColumns();
    }

    public function prepare_items()
    {
        $query = "SELECT * FROM wo_drengr_group";

        $orderby = ! empty($_GET['orderby']) ? $_GET['orderby'] : null;
        if ( ! empty($orderby)) {
            $order = ! empty($_GET['order']) ? $_GET['order'] : 'ASC';
            $query .= ' ORDER BY ' . $orderby . ' ' . $order;
        }

        $perpage = 5;
        $paged = ! empty($_GET['paged']) ? $_GET['paged'] : 1;
        if (empty($paged) || ! is_numeric($paged) || $paged <= 0) {
            $paged = 1;
        }

        $totalitems = $this->wpdb->query($query);
        $totalpages = ceil($totalitems / $perpage);
        if ( ! empty($paged) && ! empty($perpage)) {
            $offset = ($paged - 1) * $perpage;
            $query .= ' LIMIT ' . $offset . ',' . $perpage;
        }

        $this->set_pagination_args([
            'total_items' => $totalitems,
            'total_pages' => $totalpages,
            'per_page' => $perpage,
        ]);

        $this->_column_headers = [
            $this->get_columns(),
            [],
            $this->get_sortable_columns(),
        ];

        $this->items = $this->wpdb->get_results($query);

        return $this;
    }

    protected function column_default($item, $column_name)
    {
        if ($column_name === 'row_actions') {
            return $this->row_actions(['edit' => 'Edit']);
        }

        return $item->{$column_name};
    }

    protected function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="%s[]" value="%s" />',
            $this->_args['singular'],
            $item->id
        );
    }
}
