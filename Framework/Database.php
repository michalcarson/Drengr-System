<?php

namespace Drengr\Framework;

class Database
{
    protected $config;
    protected $wpdb;

    public function __construct(array $config, $wpdb)
    {
        $this->config = $config;
        $this->wpdb = $wpdb;
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
