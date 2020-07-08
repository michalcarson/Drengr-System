<?php

namespace Drengr\Framework;

class Database
{
    const INSTALLEDVERSION = 'installed_db_version';

    /** the last error encountered by this class */
    protected $lastError;

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
     * Execute a MySQL query and return the result.
     *
     * @param string $sql
     * @return array|object|null
     */
    public function query(string $sql)
    {
        return $this->wpdb->get_results($sql, ARRAY_A);
    }

    /**
     * Execute a MySQL query that will result in one row and return the result.
     *
     * @param string $sql
     * @return array|object|void|null
     */
    public function queryOne(string $sql)
    {
        return $this->wpdb->get_row($sql, ARRAY_A);
    }

    /**
     * Perform and INSERT operation on the database. Returns the last inserted id on success.
     *
     * @param string $table
     * @param array $data
     * @return false|int
     */
    public function insert(string $table, array $data)
    {
        if ($this->wpdb->insert($table, $data) !== false) {
            return $this->wpdb->insert_id;
        }

        return false;
    }

    /**
     * Perform an UPDATE on a row in the database.
     *
     * @param string $table
     * @param array $data must not be empty
     * @param array $where must not be empty
     * @return bool|int
     */
    public function update(string $table, array $data, array $where)
    {
        $this->clearError();

        if (empty($data)) {
            return $this->setError('No data was provided to update.');
        }

        if (empty($where)) {
            return $this->setError('Where criteria must be supplied.');
        }

        return $this->wpdb->update($table, $data, $where);
    }

    /**
     * Delete a row from the database.
     *
     * @param string $table
     * @param array $where must not be empty
     * @return bool|int
     */
    public function delete(string $table, array $where)
    {
        if (empty($where)) {
            return $this->setError('Where criteria must be supplied.');
        }

        return $this->wpdb->delete($table, $where);
    }

    /**
     * Clear the internal error buffer.
     */
    protected function clearError()
    {
        $this->lastError = null;
    }

    /**
     * Save a message into the internal error buffer. Always returns false as a convenience.
     *
     * @param $message
     * @return false
     */
    protected function setError($message)
    {
        $this->lastError = $message;
        return false;
    }

    /**
     * Return whatever is in the internal error buffer. If this class has no error, it will
     * check the error buffer in the WP Database class.
     *
     * @return string
     */
    public function getError()
    {
        if (!empty($this->lastError)) {
            return $this->lastError;
        }

        return $this->wpdb->last_error;
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
        $prefix = $this->getPrefix();
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

    /**
     * Return the table prefix, including any namespace set in our own config.
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->wpdb->prefix . $this->config['namespace'];
    }
}
