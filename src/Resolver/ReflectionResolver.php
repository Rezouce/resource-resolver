<?php
namespace ResourceResolver\Resolver;

use ReflectionClass;
use ReflectionParameter;
use ResourceResolver\Exception\UnresolvableException;

class ReflectionResolver implements ResolverInterface
{

    private $initialResolver;

    private $scalarParameterIdFormat;

    public function __construct(
        ResolverInterface $initialResolver = null,
        string $scalarParameterIdFormat = '{parent}::{parameter}'
    ) {
        $this->initialResolver = $initialResolver ?: $this;
        $this->scalarParameterIdFormat = $scalarParameterIdFormat;
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
                : $this->createScalarParameterId($class->getName(), $parameter->getName());
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

    private function createScalarParameterId($parentName, $parameterName)
    {
        return str_replace(['{parent}', '{parameter}'], [$parentName, $parameterName], $this->scalarParameterIdFormat);
    }
}
