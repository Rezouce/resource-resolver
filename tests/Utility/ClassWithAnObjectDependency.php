<?php
namespace ResourceResolverTest\Utility;

class ClassWithAnObjectDependency
{

    public $dependency;

    public function __construct(ClassWithoutAnyDependency $dependency)
    {
        $this->dependency = $dependency;
    }
}
