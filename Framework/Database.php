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

    /**
     * Check the current version (in config) against the installed version (in WP options)
     * and update the database schema only if needed.
     */
    public function updateTablesIfNeeded()
    {
        if ($this->config['version'] > $this->option->get(self::INSTALLEDVERSION)) {
            $this->updateTables();
            $this->option->set(self::INSTALLEDVERSION, $this->config['version']);
        }
    }

    /**
     * Run through all the tables in the configuration file and update all table schemas.
     */
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
