<?php
namespace ResourceResolver\Resolver;

use ResourceResolver\Exception\InvalidArgumentException;
use ResourceResolver\Exception\UnresolvableException;

/**
 * This resolver is useful if you want to execute multiple resolvers one after the other
 * until one is able to resolve the id.
 * You can provide as many resolvers as you want during its construction. They'll be
 * used to resolve the id using the order in which they're given. You can also
 * provide the resolvers afterwards using the methods add() and/or addBefore().
 */
class ChainedResolver implements ResolverInterface
{

    private $resolvers;

    public function __construct(ResolverInterface ...$resolvers)
    {
        $this->resolvers = $resolvers;
    }

    public function isResolvable(string $id) : bool
    {
        foreach ($this->resolvers as $resolver) {
            if ($resolver->isResolvable($id)) {
                return true;
            }
        }

        return false;
    }

    public function resolve(string $id)
    {
        foreach ($this->resolvers as $resolver) {
            if ($resolver->isResolvable($id)) {
                return $resolver->resolve($id);
            }
        }

        throw new UnresolvableException(
            sprintf('The resource %s could not be resolved by any resolver in the chain.', $id)
        );
    }

    public function add(ResolverInterface $newResolver)
    {
        $this->resolvers[] = $newResolver;

        return $this;
    }

    public function addBefore(ResolverInterface $beforeResolver, ResolverInterface $newResolver)
    {
        $offset = array_search($beforeResolver, $this->resolvers);

        if (false === $offset) {
            throw new InvalidArgumentException(sprintf(
                'Cannot add a new resolver (class: %s). The resolver provided '
                . '(class %s) was not found in the ChainedResolver.',
                get_class($newResolver),
                get_class($beforeResolver)
            ));
        }

        array_splice($this->resolvers, $offset, 0, [$newResolver]);

        return $this;
    }
}
