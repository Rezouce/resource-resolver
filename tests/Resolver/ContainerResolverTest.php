<?php
namespace ResourceResolverTest;

use PHPUnit\Framework\TestCase;
use ResourceResolver\Exception\UnresolvableException;
use ResourceResolver\Resolver\ContainerResolver;
use ResourceResolverTest\Utility\Container;

class ContainerResolverTest extends TestCase
{

    /** @var ContainerResolver */
    private $subject;

    /** @var Container */
    private $container;

    public function setUp()
    {
        parent::setUp();

        $this->container = new Container();

        $this->subject = new ContainerResolver($this->container);
    }

    public function testResolve()
    {
        $this->container->add('id', 'resolved');

        $this->assertEquals('resolved', $this->subject->resolve('id'));
    }

    public function testThrowsAnExceptionIfFailedToResolve()
    {
        $this->expectException(UnresolvableException::class);

        $this->subject->resolve('id');
    }

    public function testIsResolvable()
    {
        $this->container->add('id', 'resolved');

        $this->assertTrue($this->subject->isResolvable('id'));
        $this->assertFalse($this->subject->isResolvable('unknown'));
    }
}
