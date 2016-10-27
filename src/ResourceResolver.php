<?php
namespace ResourceResolver;

class ResourceResolver
{

    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function resolve(string $id)
    {
        if ($this->container->has($id))
        {
            return $this->container->get($id);
        }

        throw new UnresolvableException(sprintf('The resource %s could not be resolved.', $id));
    }
}
