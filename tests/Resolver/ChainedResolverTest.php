<?php
namespace ResourceResolverTest;

use PHPUnit\Framework\TestCase;
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
        $this->resolver->method('resolve')->with('id')->willReturn('resolved');

        $this->assertEquals('resolved', $this->subject->resolve('id'));
    }

    public function testResolveUsingTheNextResolverIfTheFirstOneFail()
    {
        $this->resolver->method('resolve')->with('id');
        $this->nextResolver->method('resolve')->with('id')->willReturn('resolved');

        $this->assertEquals('resolved', $this->subject->resolve('id'));
    }
}
