<?php
namespace ResourceResolverTest\Utility;

use ResourceResolver\ContainerInterface;

class Container implements ContainerInterface
{

    private $data = [];

    public function get(string $id)
    {
        return $this->data[$id];
    }

    public function add(string $id, $resource)
    {
        $this->data[$id] = $resource;
    }

    public function has(string $id): bool
    {
        return isset($this->data[$id]);
    }
}
