<?php
namespace ResourceResolverTest\Utility;

class ClassWithDefaultValue
{

    public $dependency;

    public function __construct($dependency = 'default')
    {
        $this->dependency = $dependency;
    }
}
