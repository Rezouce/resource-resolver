<?php
namespace ResourceResolver\Resolver;

use ReflectionClass;
use ReflectionParameter;
use ResourceResolver\Exception\UnresolvableException;

class ReflectionResolver implements ResolverInterface
{

    private $initialResolver;

    public function __construct(ResolverInterface $initialResolver = null)
    {
        $this->initialResolver = $initialResolver ?: $this;
    }

    public function isResolvable(string $id) : bool
    {
        return class_exists($id) && (new ReflectionClass($id))->isInstantiable();
    }

    public function resolve(string $id)
    {
        if (!$this->isResolvable($id)) {
            throw new UnresolvableException(sprintf('The resource %s is not a class', $id));
        }

        $class = new ReflectionClass($id);

        return $class->newInstanceArgs(
            $this->resolveParameters($this->getParametersType($class))
        );
    }

    private function getParametersType(ReflectionClass $class)
    {
        return array_map(function (ReflectionParameter $parameter) use ($class) {
            return $parameter->getClass()
                ? $parameter->getClass()->getName()
                : sprintf('%s::%s', $class->getName(), $parameter->getName());
        }, $class->getConstructor() ? $class->getConstructor()->getParameters(): []);
    }

    private function resolveParameters(array $parameters)
    {
        $resolvedParameters = [];
        
        foreach ($parameters as $parameter) {
            $resolvedParameters[] = $this->initialResolver->resolve($parameter);
        }
        
        return $resolvedParameters;
    }
}
