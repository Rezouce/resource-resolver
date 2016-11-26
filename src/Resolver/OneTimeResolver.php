<?php
namespace ResourceResolver\Resolver;

use ResourceResolver\Exception\UnresolvableException;

class OneTimeResolver implements ResolverInterface
{

    private $delegatedResolver;

    private $alreadyChecked = [];

    private $alreadyResolved = [];

    public function __construct(ResolverInterface $delegatedResolver)
    {
        $this->delegatedResolver = $delegatedResolver;
    }

    public function isResolvable(string $id) : bool
    {
        if (in_array($id, $this->alreadyChecked)) {
            return false;
        }

        $this->alreadyChecked[] = $id;

        return $this->delegatedResolver->isResolvable($id);
    }

    public function resolve(string $id)
    {
        if (in_array($id, $this->alreadyResolved)) {
            throw new UnresolvableException(sprintf('The resource $s cannot be resolved.', $id));
        }

        $this->alreadyResolved[] = $id;

        return $this->delegatedResolver->resolve($id);
    }
}
