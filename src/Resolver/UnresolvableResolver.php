<?php
namespace ResourceResolver\Resolver;

use ResourceResolver\Exception\UnresolvableException;

class UnresolvableResolver implements ResolverInterface
{

    public function resolve(string $id)
    {
        throw new UnresolvableException(sprintf('The resource %s could not be resolved.', $id));
    }

    public function isResolvable(string $id) : bool
    {
        return false;
    }
}
