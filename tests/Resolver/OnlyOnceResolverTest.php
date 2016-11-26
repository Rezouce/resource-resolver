<?php
namespace ResourceResolverTest;

use PHPUnit\Framework\TestCase;
use ResourceResolver\Exception\UnresolvableException;
use ResourceResolver\Resolver\OnlyOnceResolver;
use ResourceResolver\Resolver\ResolverInterface;

class OnlyOnceResolverTest extends TestCase
{

    /** @var ResolverInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $delegatedResolver;

    /** @var OnlyOnceResolver */
    private $subject;

    public function setUp()
    {
        parent::setUp();

        $this->delegatedResolver = $this->createMock(ResolverInterface::class);

        $this->subject = new OnlyOnceResolver($this->delegatedResolver);
    }

    public function testDelegateTheResourceResolvingTheFirstTime()
    {
        $this->delegatedResolver->method('resolve')->with('id')->willReturn('resolved');

        $this->assertEquals('resolved', $this->subject->resolve('id'));
    }

    public function testThrowsAnExceptionWhenTryingToResolveMoreThanOnceAResource()
    {
        $this->delegatedResolver->method('resolve');
        $this->subject->resolve('id');

        $this->expectException(UnresolvableException::class);

        $this->subject->resolve('id');
    }

    public function testDelegateTheResourceCheckingTheFirstTime()
    {
        $this->delegatedResolver->method('isResolvable')->with('id')->willReturn(true);

        $this->assertTrue($this->subject->isResolvable('id'));
    }

    public function testItReturnFalseWhenCheckingIfAResourceIsResolvableMoreThanOnce()
    {
        $this->delegatedResolver->method('isResolvable')->willReturn(true);

        $this->assertTrue($this->subject->isResolvable('id'));
        $this->assertFalse($this->subject->isResolvable('id'));
    }
}
