<?php
namespace ResourceResolverTest;

use PHPUnit\Framework\TestCase;
use ResourceResolver\Exception\UnresolvableException;
use ResourceResolver\Resolver\ReflectionResolver;
use ResourceResolver\Resolver\ResolverInterface;
use ResourceResolverTest\Utility\ClassWithAnObjectDependency;
use ResourceResolverTest\Utility\ClassWithAScalarDependency;
use ResourceResolverTest\Utility\ClassWithMultipleDependencies;
use ResourceResolverTest\Utility\ClassWithoutAnyDependency;

class ReflectionResolverTest extends TestCase
{

    /** @var ReflectionResolver */
    private $subject;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $initialResolver;

    public function setUp()
    {
        parent::setUp();

        /** @var ResolverInterface $firstResolver */
        $this->initialResolver = $firstResolver = $this->createMock(ResolverInterface::class);
        
        $this->subject = new ReflectionResolver($firstResolver);
    }

    public function testResolveAClassWithoutDependencies()
    {
        $this->assertInstanceOf(
            ClassWithoutAnyDependency::class,
            $this->subject->resolve(ClassWithoutAnyDependency::class)
        );
    }

    public function testThrowsAnExceptionIfFailedToResolve()
    {
        $this->expectException(UnresolvableException::class);

        $this->subject->resolve('id');
    }

    public function testResolveAClassWithAScalarDependency()
    {
        $parameterName = sprintf('%s::dependency', ClassWithAScalarDependency::class);
        
        $this->initialResolver->expects($this->once())->method('resolve')->with($parameterName)->willReturn('test');

        /** @var ClassWithAScalarDependency $resolvedResource */
        $resolvedResource = $this->subject->resolve(ClassWithAScalarDependency::class);
        
        $this->assertInstanceOf(ClassWithAScalarDependency::class, $resolvedResource);
        $this->assertEquals('test', $resolvedResource->dependency);
    }

    public function testResolveAClassWithAnObjectDependency()
    {
        $dependency = new ClassWithoutAnyDependency;

        $this->initialResolver
            ->expects($this->once())
            ->method('resolve')
            ->with(ClassWithoutAnyDependency::class)
            ->willReturn($dependency);

        /** @var ClassWithAnObjectDependency $resolvedResource */
        $resolvedResource = $this->subject->resolve(ClassWithAnObjectDependency::class);

        $this->assertInstanceOf(ClassWithAnObjectDependency::class, $resolvedResource);
        $this->assertSame($dependency, $resolvedResource->dependency);
    }

    public function testResolveAClassWithMultipleDependencies()
    {
        $dependency1 = new ClassWithoutAnyDependency;
        $dependency2 = new ClassWithAScalarDependency('test');
        $dependency3 = new ClassWithAnObjectDependency($dependency1);

        $this->initialResolver
            ->expects($this->exactly(3))
            ->method('resolve')
            ->willReturn($dependency1, $dependency2, $dependency3);

        /** @var ClassWithMultipleDependencies $resolvedResource */
        $resolvedResource = $this->subject->resolve(ClassWithMultipleDependencies::class);

        $this->assertInstanceOf(ClassWithMultipleDependencies::class, $resolvedResource);
        $this->assertSame($dependency1, $resolvedResource->dependency1);
        $this->assertSame($dependency2, $resolvedResource->dependency2);
        $this->assertSame($dependency3, $resolvedResource->dependency3);
    }

    public function testIsResolvable()
    {
        $this->assertTrue($this->subject->isResolvable(ClassWithoutAnyDependency::class));
        $this->assertFalse($this->subject->isResolvable('not a class name'));
    }
}
