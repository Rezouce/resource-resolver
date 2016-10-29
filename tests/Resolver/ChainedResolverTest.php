<?php
namespace ResourceResolverTest;

use PHPUnit\Framework\TestCase;
use ResourceResolver\Exception\UnresolvableException;
use ResourceResolver\Resolver\ResolverInterface;
use ResourceResolver\Resolver\ChainedResolver;

class ChainedResolverTest extends TestCase
{

    const WILL_RESOLVED_ID = true;
    const WONT_RESOLVED_ID = false;

    public function testResolveWithTheProvidedResolvers()
    {
        for ($i = 1; $i <= 10; ++$i) {
            $resolvers = $this->createResolvers($i, static::WILL_RESOLVED_ID);

            $subject = $this->createChainedResolver($resolvers);

            $this->assertEquals('resolved', $subject->resolve('id'));
        }
    }

    public function testThrowsAnExceptionIfFailedToResolve()
    {
        $this->expectException(UnresolvableException::class);

        $resolvers = $this->createResolvers(10, static::WONT_RESOLVED_ID);

        $subject = $this->createChainedResolver($resolvers);

        $subject->resolve('id');
    }

    public function testIsResolvableIfOneOfTheResolverCanResolveIt()
    {
        for ($i = 1; $i <= 10; ++$i) {
            $resolvers = $this->createResolvers($i, static::WILL_RESOLVED_ID);

            $subject = $this->createChainedResolver($resolvers);

            $this->assertTrue($subject->isResolvable('id'));
        }
    }

    public function testIsNotResolvableIfNoneOfTheResolverCanResolveIt()
    {
        $resolvers = $this->createResolvers(10, static::WONT_RESOLVED_ID);

        $subject = $this->createChainedResolver($resolvers);

        $this->assertFalse($subject->isResolvable('id'));
    }

    private function createResolvers(int $numberResolvers, bool $willResolved)
    {
        $firstToResolve = $willResolved ? rand(1, $numberResolvers) : null;
        $resolvers = [];

        for ($i = 1; $i <= $numberResolvers; ++$i) {
            $resolver = $this->createMock(ResolverInterface::class);

            if ($i === $firstToResolve) {
                $resolver->method('isResolvable')->with('id')->willReturn(true);
                $resolver->method('resolve')->with('id')->willReturn('resolved');
            } else {
                $resolver->method('isResolvable')->with('id')->willReturn(false);
            }

            $resolvers[] = $resolver;
        }

        return $resolvers;
    }

    /**
     * @param array $resolvers
     * @return ChainedResolver
     */
    private function createChainedResolver(array $resolvers)
    {
        $reflection = new \ReflectionClass(ChainedResolver::class);

        /** @var ChainedResolver $subject */
        return $reflection->newInstanceArgs($resolvers);
    }
}
