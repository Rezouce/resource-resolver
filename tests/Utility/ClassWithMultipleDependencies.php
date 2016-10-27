<?php
namespace ResourceResolverTest\Utility;

class ClassWithMultipleDependencies
{

    public $dependency1;

    public $dependency2;

    public $dependency3;

    public function __construct(
        ClassWithoutAnyDependency $dependency1,
        ClassWithAScalarDependency $dependency2,
        ClassWithAnObjectDependency $dependency3
    ) {
        $this->dependency1 = $dependency1;
        $this->dependency2 = $dependency2;
        $this->dependency3 = $dependency3;
    }
}
