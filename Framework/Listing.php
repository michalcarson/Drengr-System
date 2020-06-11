<?php

namespace Drengr\Framework;

use Drengr\Model\ListableModel;
use \InvalidArgumentException;
use WP_List_Table;

/*
 * An adapter or facade for WP_List_Table.
 *
 * There is a lot of outdated documentation out there for how to use WP_List_Table. The following
 * few may have errors but are still good points of reference.
 * @see https://codex.wordpress.org/Function_Reference/WP_List_Table
 * @see https://www.smashingmagazine.com/2011/11/native-admin-tables-wordpress/
 * @see https://plugins.trac.wordpress.org/browser/custom-list-table-example/trunk
 */

class Listing extends WP_List_Table
{
    const PERPAGE_DEFAULT = 12;

    /** @var ListableModel */
    protected $class;

    /** @var \wpdb */
    protected $wpdb;

    // validated array from `get_columns`
    private $listingColumns;

    /**
     * @param string $class The fully qualified name of a class that implements ListableModel
     * @param \wpdb $wpdb
     */
    public function __construct(string $class, \wpdb $wpdb)
    {
        $this->class = $class;
        $this->wpdb = $wpdb;

        parent::__construct([
            'plural' => $class::PLURAL,
            'singular' => $class::SINGULAR,
        ]);
    }

    /**
     * @return array
     */
    public function get_columns()
    {
        if (isset($this->listingColumns)) {
            return $this->listingColumns;
        }

        $columns = [];

        foreach ($this->class::getColumns() as $key => $value) {
            if (!is_string($key) || !is_string($value)) {
                throw new InvalidArgumentException('Column name (key) and heading (value) must be strings.');
            }

            if (1 === preg_match('/[^-_A-Za-z0-9]/', $key)) {
                // the key will be used as a CSS class name so we must limit the characters used
                throw new InvalidArgumentException('Column name (key) has illegal characters.');
            }

            if (is_numeric(substr($key, 0, 1))
                || substr($key, 0, 2) === '--'
                || (substr($key, 0, 1) === '-' && is_numeric(substr($key, 1, 1))))
            {
                // more rules for CSS class names
                throw new InvalidArgumentException('Column name (key) must not start with number or with two hyphens or with a hyphen and a number.');
            }

            $columns[$key] = __($value);
        }

        return $this->listingColumns = $columns;
    }

    /**
     * @return array
     */
    public function get_sortable_columns()
    {
        return $this->class::getSortableColumns();
    }

    /**
     * @return array
     */
    public function get_hidden_columns()
    {
        return $this->class::getHiddenColumns();
    }

    /**
     * Set up data and pagination parameters for the table.
     *
     * <soapbox>
     * This method has unavoidable side-effects. In order to prepare the parent class to
     * display the table, we must build the query, set the values needed in the parent's
     * `_column_headers` variable and set the query data in the parent's `items` variable.
     * The column header data requires particular structure, so it would be better to have
     * a method in the parent that created the structure for us.
     * </soapbox>
     *
     * @return $this
     */
    public function prepare_items()
    {
        $perPage = $this->getPerPage();

        $query = $this->class::getQuery($this->wpdb->prefix) . $this->orderByClause();

        $this->setPaginationArgs($query, $perPage);

        $query .= $this->offsetClause($perPage);

        $this->_column_headers = [
            $this->get_columns(),
            $this->get_hidden_columns(),
            $this->get_sortable_columns(),
            $this->get_primary_column_name(),
        ];

        $this->items = $this->wpdb->get_results($query);

        return $this;
    }

    protected function getPerPage()
    {
        //todo: look into $this->get_items_per_page();
        return self::PERPAGE_DEFAULT;
    }

    /**
     * Returns a MySql-compatible ORDER BY clause.
     *
     * @return string
     */
    protected function orderByClause()
    {
        // todo: get the globals out of this method
        $orderby = ! empty($_GET['orderby']) ? $_GET['orderby'] : null;

        if (empty($orderby)) {
            return '';
        }

        $order = ! empty($_GET['order']) ? $_GET['order'] : 'ASC';

        return ' ORDER BY ' . $orderby . ' ' . $order;
    }

    /**
     * Returns a MySql-compatible LIMIT clause.
     *
     * @param int $perPage
     * @return string
     */
    protected function offsetClause(int $perPage)
    {
        if (empty($perPage)) {
            return '';
        }

        $pageNum = $this->get_pagenum();
        $offset = ($pageNum - 1) * $perPage;

        return ' LIMIT ' . $offset . ',' . $perPage;
    }

    /**
     * Sets values that will be used by the parent to write the page controls.
     *
     * @param string $query
     * @param int $perPage
     */
    protected function setPaginationArgs(string $query, int $perPage)
    {
        $totalItems = $this->wpdb->query($query);

        $this->set_pagination_args([
            'total_items' => $totalItems,
            'per_page' => $perPage,
        ]);
    }

    /**
     * If there is not a method named `column_{column name}` then this one will be called
     * to present the value of the column.
     *
     * @param object $item
     * @param string $column_name
     * @return string|void
     */
    protected function column_default($item, $column_name)
    {
        $actions = '';

        if ($column_name === $this->get_primary_column()) {
            $actions = $this->row_actions($this->class::getRowActions($item));
        }

        return $item->{$column_name} . $actions;
    }

    /**
     * This is a special pseudo-column handler for the initial checkbox. To invoke it,
     * use "cb" for the column name. The checkbox is then available for bulk actions.
     * Naturally, you have to also set up the bulk actions selector and surround the table
     * with a form tag.
     *
     * @param object $item
     * @return string|void
     */
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

    /**
     * Add the form tag and security field to the page. This is required for bulk actions to work.
     * Be sure to close the form tag after the table is displayed.
     *
     * @return $this
     */
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

    /**
     * Close the form tag and also write a "clear" tag.
     *
     * @return $this
     */
    public function endForm()
    {
        echo '</form><br class="clear" />';

        return $this;
    }
}
