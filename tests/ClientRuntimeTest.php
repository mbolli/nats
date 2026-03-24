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
        self::defineOpenSwooleCoroutineStub();
        OpenSwooleCoroutineStub::$cid = -1;
    }

    protected function tearDown(): void
    {
        OpenSwooleCoroutineStub::$cid = -1;
    }

    public function testClientCanBeCreatedOutsideOpenSwooleCoroutine(): void
    {
        $client = new Client(Config::default());

        self::assertInstanceOf(Client::class, $client);
    }

    public function testClientFailsInsideOpenSwooleCoroutine(): void
    {
        OpenSwooleCoroutineStub::$cid = 1;

        self::expectException(FeatureIsNotSupported::class);
        self::expectExceptionMessage('OpenSwoole coroutines are not supported.');

        new Client(Config::default());
    }

    private static function defineOpenSwooleCoroutineStub(): void
    {
        if (\class_exists(\OpenSwoole\Coroutine::class)) {
            return;
        }

        eval(<<<'PHP'
namespace OpenSwoole;

final class Coroutine
{
    public static function getCid(): int
    {
        return \Thesis\Nats\OpenSwooleCoroutineStub::$cid;
    }
}
PHP);
    }
}

final class OpenSwooleCoroutineStub
{
    public static int $cid = -1;
}
