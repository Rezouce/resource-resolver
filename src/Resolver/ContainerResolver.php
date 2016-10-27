<?php
namespace ResourceResolver\Resolver;

use ResourceResolver\ContainerInterface;

class ContainerResolver implements ResolverInterface
{

    private $container;

    private $nextResolver;

    public function __construct(ContainerInterface $container, ResolverInterface $nextResolver)
    {
        $this->container = $container;

        $this->nextResolver = $nextResolver;
    }

    public function resolve(string $id)
    {
        if ($this->container->has($id)) {
            return $this->container->get($id);
        }

        return $this->nextResolver->resolve($id);
    }
}
