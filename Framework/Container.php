<?php

namespace Drengr\Framework;

use Drengr\Exception\UnknownIdentifierException;
use Psr\Container\ContainerInterface;

/**
 * This is a VERY simple injection container for WordPress plugins.
 *
 * This class is configured through an array of bindings which might resemble the following.
 * [
 *     Database::class => function (Container $container) {
 *         $wpdb = $container->get('wpdb');
 *         $container->require('upgrade'); // includes the wp-admin/upgrade.php file
 *
 *         $option = $container->get(Option::class);
 *
 *         $config = $container->get('config')->get('database');
 *
 *         return new Database(
 *             $config,
 *             $wpdb,
 *             $option
 *         );
 *     },
 *
 *     'wpdb' => function (Container $container) {
 *         global $wpdb;
 *         return $wpdb;
 *     },
 * ]
 */
class Container implements ContainerInterface
{
    /**
     * Array of values which may or may not be callables. If it is a callable,
     * it should be a factory to create a class instance.
     *
     * @var array
     */
    protected $values = [];

    /**
     * Array of classes we have already instantiated.
     *
     * @var array
     */
    protected $instances = [];

    /**
     * Load all of the bindings. Generally, $bindings is of the form
     *
     *      [
     *          MyClass::class => function (Container $container) {
     *              return new MyClass();
     *          }
     *      ]
     *
     * But the values could be scalars or arrays or object instances.
     *
     * @param array $bindings
     */
    public function load($bindings)
    {
        foreach ($bindings as $key => $value) {
            $this->values[$key] = $value;
        }
    }

    /**
     * Get an instance of a class.
     *
     * NB: When we call the factory methods below, we need to pass this class--
     * this container. I was unable to do that in Pimple. The Pimple container
     * would only pass itself and didn't allow for changing that container. Since
     * Pimple is no longer open for pull requests, I had to abandon that approach
     * and write my own container. Too bad; Pimple has more features.
     *
     * @param string $id
     * @return mixed
     */
    public function get($id)
    {
        if (isset($this->instances[$id])) {
            // return the instance we have already made
            return $this->instances[$id];
        }

        if (isset($this->values[$id])
        && is_callable($this->values[$id])) {
            // create a new instance. pass this container to the factory.
            $this->instances[$id] = $this->values[$id]($this);
            return $this->instances[$id];
        }

        if (isset($this->values[$id])) {
            // we have something but we don't know what it is
            return $this->values[$id];
        }

        throw new UnknownIdentifierException($id);
    }

    /**
     * Set a single binding.
     *
     * @param string $id
     * @param mixed $value
     */
    public function set($id, $value)
    {
        $this->values[$id] = $value;
    }

    /**
     * Set or override an instance. Usually for testing.
     *
     * @param string $id
     * @param mixed $value
     */
    public function instance($id, $value)
    {
        $this->instances[$id] = $value;
    }

    /**
     * Check whether we have a particular key.
     *
     * @param string $id
     * @return bool
     */
    public function has($id)
    {
        return isset($this->values[$id]) || isset($this->instances[$id]);
    }

    /**
     * Include a WP admin module.
     *
     * <soapbox>This is so much cleaner than scattering `include` all over the code.</soapbox>
     *
     * @param string $name
     */
    public function require($name)
    {
        require_once(ABSPATH . 'wp-admin/includes/' . $name . '.php');
    }
}
