<?php

namespace KafkaRestClient;

use MabeEnum\Enum;

final class UrlTransition extends Enum
{
    public const SET_BASE_URL = 'SET_BASE_URL';

    public const SET_TOPICS = 'SET_TOPICS';
    public const SET_PARTITIONS = 'SET_PARTITIONS';

    public const SET_CONSUMERS = 'SET_CONSUMERS';
    public const SET_INSTANCES = 'SET_INSTANCES';
    public const SET_OFFSETS = 'SET_OFFSETS';
    public const SET_SUBSCRIPTION = 'SET_SUBSCRIPTION';
    public const SET_ASSIGNMENTS = 'SET_ASSIGNMENTS';
    public const SET_POSITIONS = 'SET_POSITIONS';
    public const SET_BEGINNING = 'SET_BEGINNING';
    public const SET_END = 'SET_END';
    public const SET_RECORDS = 'SET_RECORDS';
    public const SET_PARAMETERS = 'SET_PARAMETERS';

    public const SET_BROKERS = 'SET_BROKERS';
}