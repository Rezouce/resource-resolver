<?php
namespace ResourceResolver\Resolver;

use ResourceResolver\Exception\UnresolvableException;

class ChainedResolver implements ResolverInterface
{

    private $resolver;

    private $nextResolver;

    public function __construct(ResolverInterface $resolver, ResolverInterface $nextResolver)
    {
        $this->nextResolver = $nextResolver;
        $this->resolver = $resolver;
    }

    public function isResolvable(string $id) : bool
    {
        return $this->resolver->isResolvable($id) || $this->nextResolver->isResolvable($id);
    }

    public function resolve(string $id)
    {
        if ($this->resolver->isResolvable($id)) {
            return $this->resolver->resolve($id);
        }

        if ($this->nextResolver->isResolvable($id)) {
            return $this->nextResolver->resolve($id);
        }

        throw new UnresolvableException(
            sprintf('The resource %s could not be resolved by any resolver in the chain.', $id)
        );
    }
}
