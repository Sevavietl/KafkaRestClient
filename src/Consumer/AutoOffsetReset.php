<?php

namespace KafkaRestClient\Consumer;

use MabeEnum\Enum;

/**
 * @method static static LATEST()
 * @method static static EARLIEST()
 * @method static static NONE()
 */
final class AutoOffsetReset extends Enum
{
    public const LATEST = 'latest';
    public const EARLIEST = 'earliest';
    public const NONE = 'none';
}