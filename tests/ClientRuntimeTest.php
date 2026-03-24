<?php

declare(strict_types=1);

namespace Thesis\Nats;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Thesis\Nats\Exception\FeatureIsNotSupported;
use Thesis\Nats\Internal\RuntimeGuard;

#[CoversClass(Client::class)]
#[CoversClass(RuntimeGuard::class)]
final class ClientRuntimeTest extends TestCase
{
    protected function setUp(): void
    {
        if (\class_exists(\OpenSwoole\Coroutine::class) && !\property_exists(\OpenSwoole\Coroutine::class, 'cid')) {
            self::markTestSkipped('OpenSwoole extension is loaded; fixture-based coroutine stubbing is unavailable.');
        }

        if (!\class_exists(\OpenSwoole\Coroutine::class)) {
            require_once __DIR__ . '/Fixture/OpenSwoole/Coroutine.php';
        }

        \OpenSwoole\Coroutine::$cid = -1;
    }

    protected function tearDown(): void
    {
        if (\class_exists(\OpenSwoole\Coroutine::class) && \property_exists(\OpenSwoole\Coroutine::class, 'cid')) {
            \OpenSwoole\Coroutine::$cid = -1;
        }
    }

    public function testClientCanBeCreatedOutsideOpenSwooleCoroutine(): void
    {
        $client = new Client(Config::default());

        self::assertInstanceOf(Client::class, $client);
    }

    public function testClientFailsInsideOpenSwooleCoroutine(): void
    {
        \OpenSwoole\Coroutine::$cid = 0;

        self::expectException(FeatureIsNotSupported::class);
        self::expectExceptionMessage('OpenSwoole coroutines are not supported.');

        new Client(Config::default());
    }
}
