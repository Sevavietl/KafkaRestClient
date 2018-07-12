<?php

namespace KafkaRestClient;

use KafkaRestClient\Consumer\AutoOffsetReset;

interface ConsumerConfig extends Config
{
    public function groupId(): string;

    public function autoOffsetReset(): AutoOffsetReset;

    public function autoCommitEnable(): bool;

    public function autoCommitIntervalMs(): int;
}