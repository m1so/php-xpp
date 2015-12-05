<?php

namespace Baumgartner\Xpp\ODE;

class Bag {
    protected $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public function add($key, $value)
    {
        $this->params[$key] = $value;
    }

    public function get($key)
    {
        return isset($this->params[$key]) ? $this->params[$key] : null;
    }

    public function keys()
    {
        return array_keys($this->params);
    }
}