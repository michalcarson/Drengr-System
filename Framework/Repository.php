<?php

namespace Drengr\Framework;

abstract class Repository
{
    /** @var Database */
    protected $database;

    protected $table;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function getAll(array $params, array $where = [])
    {
        return $this->database->query(
            $this->getAllSql(
                $this->getWhereClause($where),
                $this->getLimitClause($params)
            )
        );
    }

    protected function getAllSql($where = '', $limit = '')
    {
        return sprintf(
            "select * from %s %s %s",
            $this->getTableName(),
            $where,
            $limit
        );
    }

    public function find($id)
    {
        return $this->database->queryOne(
            $this->getAllSql(
                $this->getWhereClause(['id' => $id])
            )
        );
    }

    protected function getTableName()
    {
        return $this->database->getPrefix() . $this->table;
    }

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

    protected function getLimitClause(array $params)
    {
        $offset = isset($params['page']) ? sprintf('OFFSET %d', $params['page']) : '';
        $limit = isset($params['perPage']) ? sprintf('LIMIT %d', $params['perPage']) : '';
        return $limit . ' ' . $offset;
    }
}
