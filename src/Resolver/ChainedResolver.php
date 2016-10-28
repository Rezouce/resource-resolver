<?php
namespace ResourceResolver\Resolver;

class ChainedResolver implements ResolverInterface
{

    private $resolver;

    private $nextResolver;

    public function __construct(ResolverInterface $resolver, ResolverInterface $nextResolver)
    {
        $this->nextResolver = $nextResolver;
        $this->resolver = $resolver;
    }
    
    public function resolve(string $id)
    {
        $resolvedData = $this->resolver->resolve($id);

        return null === $resolvedData
            ? $this->nextResolver->resolve($id)
            : $resolvedData;
    }
}
