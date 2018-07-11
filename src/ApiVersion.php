<?php

namespace KafkaRestClient;

use MabeEnum\Enum;

/**
 * @method static static V1()
 * @method static static V2()
 */
final class ApiVersion extends Enum
{
    public const V1 = 'v1';
    public const V2 = 'v2';
}