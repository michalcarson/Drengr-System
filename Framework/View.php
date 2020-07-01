<?php

namespace Drengr\Framework;

class View
{
    /** @var array */
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function display($template, $params)
    {
        $this->startAdminPage();

        $html = include $this->config['templateDir'] . $template . '.php';

        echo $html;

        require_once ABSPATH . 'wp-admin/admin-footer.php';
    }

    protected function startAdminPage()
    {
        global $wp_db_version;
        $wp_db_version = get_option('db_version');

        require_once ABSPATH . 'wp-admin/admin.php';
        require_once ABSPATH . 'wp-admin/admin-header.php';
    }
}
