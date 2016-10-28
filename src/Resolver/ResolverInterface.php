<?php
namespace ResourceResolver\Resolver;

use ResourceResolver\Exception\UnresolvableException;

interface ResolverInterface
{

    public function isResolvable(string $id) : bool;

    /**
     * @param string $id
     * 
     * @return mixed
     * @throws UnresolvableException
     */
    public function resolve(string $id);
}
