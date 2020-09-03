<?php

namespace Drengr\Framework;

use Drengr\Exception\ModelNotFoundException;

abstract class Repository
{
    /** @var Database */
    protected $database;

    /** Base name for this table (no prefix) */
    protected $table;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    /**
     * Return all rows from the table, potentially limited by `page` and `perPage` parameters
     * and filtered by entries in the $where array.
     *
     * @param array $params
     * @param array $where
     * @return array|object|null
     */
    public function getAll(array $params, array $where = [])
    {
        return $this->database->query(
            $this->getAllSql(
                $this->getWhereClause($where),
                $this->getLimitClause($params)
            )
        );
    }

    /**
     * Return the MySQL SELECT statement used to select all rows.
     *
     * @param string $where
     * @param string $limit
     * @return string
     */
    protected function getAllSql($where = '', $limit = '')
    {
        return sprintf(
            "select * from %s %s %s",
            $this->getTableName(),
            $where,
            $limit
        );
    }

    /**
     * @param $id
     * @return array|object|void
     * @throws ModelNotFoundException
     */
    public function findOrFail($id)
    {
        $item = $this->find($id);
        if (!$item) {
            throw new ModelNotFoundException();
        }

        return $item;
    }

    /**
     * Return one row from the table.
     *
     * @param $id
     * @return array|object|void|null
     */
    public function find($id)
    {
        return $this->database->queryOne(
            $this->getAllSql(
                $this->getWhereClause(['id' => $id])
            )
        );
    }

    /**
     * Insert a row into the table.
     *
     * @param $data
     * @return array|object|void|null
     */
    public function create($data)
    {
        if (($id = $this->database->insert($this->getTableName(), $data)) !== false) {
            return $this->find($id);
        }
    }

    /**
     * Update the row with id = $id in the table.
     *
     * @param $id
     * @param $data
     * @return array|object|void|null
     * @throws \Exception
     */
    public function update($id, $data)
    {
        if ($this->database->update($this->getTableName(), $data, ['id' => $id]) !== false) {
            return $this->find($id);
        }

        throw new \Exception($this->database->getError());
    }

    /**
     * Delete the row with id = $id from the table.
     *
     * @param $id
     * @return bool|int
     */
    public function delete($id)
    {
        return $this->database->delete($this->getTableName(), ['id' => $id]);
    }

    /**
     * Return the table name with proper prefixing.
     *
     * @return string
     */
    protected function getTableName()
    {
        return $this->database->getPrefix() . $this->table;
    }

    /**
     * Build a WHERE clause from the array entries.
     *
     * @param array $where
     * @return string
     */
    protected function getWhereClause(array $where)
    {
        if (empty($where)) {
            return '';
        }

        $conditions = [];
        foreach ($where as $field => $value) {
            if (is_null($value)) {
                $conditions[] = "`$field` IS NULL";
                continue;
            }

            $conditions[] = "$field = '$value'";
        }

        return 'WHERE ' . implode(' AND ', $conditions);
    }

    /**
     * Build a LIMIT clause from the pagination parameters.
     *
     * @param array $params
     * @return string
     */
    protected function getLimitClause(array $params)
    {
        $offset = isset($params['page']) ? sprintf('OFFSET %d', $params['page']) : '';
        $limit = isset($params['perPage']) ? sprintf('LIMIT %d', $params['perPage']) : '';
        return $limit . ' ' . $offset;
    }
}
