<?php

declare(strict_types=1);

namespace Thesis\Nats\Internal;

use Thesis\Nats\Exception\FeatureIsNotSupported;

/**
 * @internal
 */
final class RuntimeGuard
{
    /**
     * @throws FeatureIsNotSupported
     */
    public static function assertSupported(): void
    {
        if (!\class_exists(\OpenSwoole\Coroutine::class) || !\method_exists(\OpenSwoole\Coroutine::class, 'getCid')) {
            return;
        }

        if (\OpenSwoole\Coroutine::getCid() > 0) {
            throw FeatureIsNotSupported::forOpenSwoole();
        }
    }
}
