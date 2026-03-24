<?php

declare(strict_types=1);

namespace OpenSwoole;

final class Coroutine
{
    public static int $cid = -1;

    public static function getCid(): int
    {
        return self::$cid;
    }
}
