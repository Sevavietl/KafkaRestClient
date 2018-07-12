<?php

namespace KafkaRestClient;

use MabeEnum\Enum;

final class UrlState extends Enum
{
    public const INITIATED = 'INITIATED';

    public const BASE_URL_SET = 'BASE_URL_SET';

    public const TOPICS_SET = 'TOPICS_SET';
    public const PARTITIONS_SET = 'PARTITIONS_SET';

    public const CONSUMERS_SET = 'CONSUMERS_SET';
    public const INSTANCES_SET = 'INSTANCES_SET';
    public const OFFSETS_SET = 'OFFSETS_SET';
    public const SUBSCRIPTION_SET = 'SUBSCRIPTION_SET';
    public const ASSIGNMENTS_SET = 'ASSIGNMENTS_SET';
    public const POSITIONS_SET = 'POSITIONS_SET';
    public const BEGINNING_SET = 'BEGINNING_SET';
    public const END_SET = 'END_SET';
    public const RECORDS_SET = 'RECORDS_SET';
    public const PARAMETERS_SET = 'PARAMETERS_SET';

    public const BROKERS_SET = 'BROKERS_SET';
}