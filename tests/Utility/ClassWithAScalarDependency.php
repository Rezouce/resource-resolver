<?php
namespace ResourceResolverTest\Utility;

class ClassWithAScalarDependency
{

    public $dependency;

    public function __construct($dependency)
    {
        $this->dependency = $dependency;
    }
}
