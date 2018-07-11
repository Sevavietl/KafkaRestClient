<?php

namespace KafkaRestClient;

use MabeEnum\Enum;

/**
 * @method static static JSON()
 * @method static static BINARY()
 * @method static static AVRO()
 */
final class EmbeddedFormat extends Enum
{
    public const JSON = 'json';
    public const BINARY = 'binary';
    public const AVRO = 'avro';
}