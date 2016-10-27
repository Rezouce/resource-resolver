<?php
namespace ResourceResolver\Resolver;

use ResourceResolver\Exception\UnresolvableException;

interface ResolverInterface
{

    /**
     * @param string $id
     * @return mixed
     * @throws UnresolvableException
     */
    public function resolve(string $id);
}
