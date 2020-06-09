<?php

namespace Drengr\Framework;

class Option
{
    /**
     * Retrieve a value from WP options storage.
     *
     * @param string $option
     * @param mixed $default
     * @return mixed
     */
    public function get($option, $default = false)
    {
        if (func_num_args() > 1) {
            return get_option($option, $default);
        }
        return get_option($option);
    }

    /**
     * Store a value in WP options storage.
     *
     * @param string $option
     * @param mixed $value
     * @return bool false if the value was not updated
     */
    public function set($option, $value)
    {
        return update_option($option, $value);
    }
}
