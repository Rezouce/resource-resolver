<?php
namespace ResourceResolverTest\Utility;

use Interop\Container\ContainerInterface;

class Container implements ContainerInterface
{

    private $data = [];

    public function get($id)
    {
        return $this->data[$id];
    }

    public function add($id, $resource)
    {
        $this->data[$id] = $resource;
    }

    public function has($id)
    {
        return isset($this->data[$id]);
    }
}
