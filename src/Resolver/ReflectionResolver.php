<?php
namespace ResourceResolver\Resolver;

use ReflectionClass;
use ReflectionParameter;
use ResourceResolver\Exception\UnresolvableException;

/**
 * This resolver can resolve ids which match a class name by using the reflection classes.
 * 
 * For scalar parameters, which can't be resolved by reflection, it'll try to resolve them
 * by calling a resolver which can be provided during the resolver construction.
 * You can, for example, provide a ContainerResolver which will be able to resolve the
 * parameter. The default format for matching these parameters is {parent}::{parameter},
 * format that you may want to change by providing another one at the ReflectionResolver's
 * second construct parameter.
 */
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

        return $class->newInstanceArgs($this->resolveParameters($class));
    }

    private function resolveParameters(ReflectionClass $class)
    {
        $resolvedParameters = [];
        
        foreach ($this->getListReflectionParameters($class) as $parameter) {
            $resolvedParameters[] = $this->resolveParameter($parameter, $class->name);
        }
        
        return $resolvedParameters;
    }

    private function getListReflectionParameters(ReflectionClass $class)
    {
        return $class->getConstructor() ? $class->getConstructor()->getParameters() : [];
    }

    private function resolveParameter(ReflectionParameter $parameter, $className)
    {
        $id = $this->getParameterId($parameter, $className);

        if ($this->initialResolver->isResolvable($id)) {
            return $this->initialResolver->resolve($id);
        }

        if ($parameter->isOptional()) {
            return $parameter->getDefaultValue();
        }

        throw new UnresolvableException(sprintf('Unable to resolve parameter with id %s', $id));
    }

    private function getParameterId(ReflectionParameter $parameter, $className)
    {
        return $parameter->getClass()
            ? $parameter->getClass()->name
            : $this->createScalarParameterId($className, $parameter->name);
    }

    private function createScalarParameterId($parentName, $parameterName)
    {
        return str_replace(['{parent}', '{parameter}'], [$parentName, $parameterName], $this->scalarParameterIdFormat);
    }
}
