<?php
namespace ResourceResolverTest;

use PHPUnit\Framework\TestCase;
use ResourceResolver\Exception\UnresolvableException;
use ResourceResolver\Resolver\ContainerResolver;
use ResourceResolver\Resolver\UnresolvableResolver;

class UnresolvableResolverTest extends TestCase
{

    /** @var ContainerResolver */
    private $subject;

    public function setUp()
    {
        parent::setUp();

        $this->subject = new UnresolvableResolver();
    }

    public function testResolve()
    {
        $this->expectException(UnresolvableException::class);

        $this->subject->resolve('id');
    }
}
