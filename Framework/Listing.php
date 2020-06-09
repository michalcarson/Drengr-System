<?php

namespace Drengr\Framework;

use Drengr\Model\Group;
use Drengr\Model\ListableModel;
use WP_List_Table;

/*
 * There is a lot of outdated documentation out there for how to use WP_List_Table. The following
 * few may have errors but are still good points of reference.
 * @see https://codex.wordpress.org/Function_Reference/WP_List_Table
 * @see https://www.smashingmagazine.com/2011/11/native-admin-tables-wordpress/
 * @see https://plugins.trac.wordpress.org/browser/custom-list-table-example/trunk
 */

class Listing extends WP_List_Table
{
    /** @var ListableModel */
    protected $class;
    /** @var \wpdb */
    protected $wpdb;

    public function __construct(string $class, \wpdb $wpdb)
    {
        $this->class = $class;
        $this->wpdb = $wpdb;

        parent::__construct([
            'plural' => $class::PLURAL,
            'singular' => $class::SINGULAR,
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
        $query = $this->class::getQuery($this->wpdb->prefix);

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

    public function pageHeading($title)
    {
        // todo: action buttons (like Add) go between h1 and hr
        echo '<h1 class="wp-heading-inline">' . $title . '</h1><hr class="wp-header-end" />';

        return $this;
    }

    public function formTag()
    {
//        echo sprintf(
//            '<form id="%s" action="%s" method="post" novalidate="novalidate">',
//            'listing',
//            plugins_url('', __FILE__)
//        );
        echo '<form method="get">';
        wp_nonce_field('action', 'listing');

        return $this;
    }

    public function endForm()
    {
        echo '</form><br class="clear" />';

        return $this;
    }
}
