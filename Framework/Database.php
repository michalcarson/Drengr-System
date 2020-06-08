<?php

namespace Drengr\Framework;

class Database
{
    const INSTALLEDVERSION = 'installed_db_version';

    /** @var array */
    protected $config;

    /** @var \wpdb */
    protected $wpdb;

    /** @var Option */
    protected $option;

    public function __construct(array $config, \wpdb $wpdb, Option $option)
    {
        $this->config = $config;
        $this->wpdb = $wpdb;
        $this->option = $option;
    }

    public function updateTablesIfNeeded()
    {
        if ($this->config['version'] > $this->option->get(self::INSTALLEDVERSION)) {
            $this->updateTables();
            $this->option->set(self::INSTALLEDVERSION, $this->config['version']);
        }
    }

    public function updateTables()
    {
        $prefix = $this->wpdb->prefix;
        $charset = $this->wpdb->get_charset_collate();

        foreach ($this->config['tables'] as $table => $createStatement) {
            $sql = str_replace(
                ['{prefix}', '{charset}'],
                [$prefix, $charset],
                $createStatement
            );
            dbDelta($sql);
        }
    }
}
