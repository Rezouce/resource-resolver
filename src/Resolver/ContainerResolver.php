<?php
namespace ResourceResolver\Resolver;

use ResourceResolver\ContainerInterface;
use ResourceResolver\Exception\UnresolvableException;

class ContainerResolver implements ResolverInterface
{

    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function isResolvable(string $id) : bool
    {
        return $this->container->has($id);
    }

    public function resolve(string $id)
    {
        if (!$this->isResolvable($id)) {
            throw new UnresolvableException(sprintf('The resource %s is not in the container', $id));
        }

        return $this->container->get($id);
    }
}
