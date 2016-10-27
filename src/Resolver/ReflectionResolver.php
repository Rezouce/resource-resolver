<?php
namespace ResourceResolver\Resolver;

use ReflectionClass;
use ReflectionParameter;

class ReflectionResolver implements ResolverInterface
{

    private $firstResolver;

    private $nextResolver;

    public function __construct(ResolverInterface $firstResolver, ResolverInterface $nextResolver)
    {
        $this->firstResolver = $firstResolver;

        $this->nextResolver = $nextResolver;
    }

    public function resolve(string $id)
    {
        if (class_exists($id)) {
            $class = new ReflectionClass($id);

            return $class->newInstanceArgs(
                $this->resolveParameters($this->getParametersType($class))
            );
        }

        return $this->nextResolver->resolve($id);
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
            $resolvedParameters[] = $this->firstResolver->resolve($parameter);
        }
        
        return $resolvedParameters;
    }
}
