<?php

namespace Drengr\Framework;

use Drengr\Exception\UnknownIdentifierException;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    protected $values = [];
    protected $instances = [];

    public function load($bindings)
    {
        foreach ($bindings as $key => $value) {
            $this->values[$key] = $value;
        }
    }

    public function get($id)
    {
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        if (isset($this->values[$id])
        && is_callable($this->values[$id])) {
            $this->instances[$id] = $this->values[$id]($this);
            return $this->instances[$id];
        }

        if (isset($this->values[$id])) {
            return $this->values[$id];
        }

        throw new UnknownIdentifierException($id);
    }

    public function set($id, $value)
    {
        $this->values[$id] = $value;
    }

    public function instance($id, $value)
    {
        $this->instances[$id] = $value;
    }

    public function has($id)
    {
        return isset($this->values[$id]) || isset($this->instances[$id]);
    }

    public function require($name)
    {
        require_once(ABSPATH . 'wp-admin/includes/' . $name . '.php');
    }
}
