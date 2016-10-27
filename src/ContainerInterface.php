<?php
namespace ResourceResolver;

interface ContainerInterface
{

    public function get(string $id);

    public function add(string $id, $resource);

    public function has(string $id): bool;
}
