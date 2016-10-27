<?php
namespace ResourceResolverTest;

use PHPUnit\Framework\TestCase;
use ResourceResolver\ContainerInterface;
use ResourceResolver\ResourceResolver;
use ResourceResolver\UnresolvableException;
use ResourceResolverTest\Utility\Container;

class ResourceResolverTest extends TestCase
{

    /** @var ResourceResolver */
    private $subject;

    /** @var ContainerInterface */
    private $container;

    public function setUp()
    {
        parent::setUp();

        $this->container = new Container();
        $this->subject = new ResourceResolver($this->container);
    }

    public function testResolveFromTheContainer()
    {
        $object = new \DateTime();
        $this->container->add('id', $object);

        $this->assertSame($object, $this->subject->resolve('id'));
    }

    public function testThrowAnExceptionIfIdDoesntExists()
    {
        $this->expectException(UnresolvableException::class);

        $this->subject->resolve('id');
    }
}
