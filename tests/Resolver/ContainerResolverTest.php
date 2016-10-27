<?php
namespace ResourceResolverTest;

use PHPUnit\Framework\TestCase;
use ResourceResolver\ContainerInterface;
use ResourceResolver\Resolver\ContainerResolver;
use ResourceResolver\Resolver\ResolverInterface;
use ResourceResolverTest\Utility\Container;

class ContainerResolverTest extends TestCase
{

    /** @var ContainerResolver */
    private $subject;

    /** @var ContainerInterface */
    private $container;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $nextResolver;

    public function setUp()
    {
        parent::setUp();

        $this->container = new Container();

        /** @var ResolverInterface $nextResolver */
        $this->nextResolver = $nextResolver = $this->createMock(ResolverInterface::class);

        $this->subject = new ContainerResolver($this->container, $nextResolver);
    }

    public function testResolve()
    {
        $object = new \DateTime();
        $this->container->add('id', $object);

        $this->assertSame($object, $this->subject->resolve('id'));
    }

    public function testPassToTheNextResolverIfFailToResolve()
    {
        $this->nextResolver->expects($this->once())->method('resolve')->with('id');

        $this->subject->resolve('id');
    }
}
