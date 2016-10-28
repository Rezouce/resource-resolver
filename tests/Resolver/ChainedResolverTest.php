<?php
namespace ResourceResolverTest;

use PHPUnit\Framework\TestCase;
use ResourceResolver\Exception\UnresolvableException;
use ResourceResolver\Resolver\ResolverInterface;
use ResourceResolver\Resolver\ChainedResolver;

class ChainedResolverTest extends TestCase
{

    /** @var ChainedResolver */
    private $subject;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $resolver;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $nextResolver;

    public function setUp()
    {
        parent::setUp();

        /** @var ResolverInterface $resolver */
        $this->resolver = $resolver = $this->createMock(ResolverInterface::class);
        /** @var ResolverInterface $nextResolver */
        $this->nextResolver = $nextResolver = $this->createMock(ResolverInterface::class);

        $this->subject = new ChainedResolver($resolver, $nextResolver);
    }

    public function testResolveWithTheProvidedResolver()
    {
        $this->resolver->method('isResolvable')->with('id')->willReturn(true);
        $this->resolver->method('resolve')->with('id')->willReturn('resolved');

        $this->assertEquals('resolved', $this->subject->resolve('id'));
    }

    public function testResolveUsingTheNextResolverIfTheFirstOneFail()
    {
        $this->resolver->method('isResolvable')->with('id')->willReturn(false);
        $this->nextResolver->method('isResolvable')->with('id')->willReturn(true);
        $this->nextResolver->method('resolve')->with('id')->willReturn('resolved');

        $this->assertEquals('resolved', $this->subject->resolve('id'));
    }

    public function testThrowsAnExceptionIfFailedToResolve()
    {
        $this->resolver->method('isResolvable')->with('id')->willReturn(false);
        $this->nextResolver->method('isResolvable')->with('id')->willReturn(false);

        $this->expectException(UnresolvableException::class);

        $this->subject->resolve('id');
    }

    public function testIsResolvable()
    {
        $this->resolver->method('isResolvable')->with('id')->willReturn(true, true, false, false);
        $this->nextResolver->method('isResolvable')->with('id')->willReturn(true, false, true, false);

        $this->assertTrue($this->subject->isResolvable('id'));
        $this->assertTrue($this->subject->isResolvable('id'));
        $this->assertTrue($this->subject->isResolvable('id'));
        $this->assertFalse($this->subject->isResolvable('id'));
    }
}
