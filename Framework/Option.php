<?php

namespace Drengr\Framework;

class Option
{
    /**
     * @param string $option
     * @param false $default
     * @return mixed
     */
    public function get($option, $default = false)
    {
        return get_option($option, $default);
    }

    /**
     * @param string $option
     * @param mixed $value
     * @return bool false if the value was not updated
     */
    public function set($option, $value)
    {
        return update_option($option, $value);
    }
}
