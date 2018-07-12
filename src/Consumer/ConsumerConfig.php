<?php

namespace KafkaRestClient\Consumer;

use KafkaRestClient\ApiVersion;
use KafkaRestClient\Config;
use KafkaRestClient\ConsumerConfig as ConsumerConfigInterface;
use KafkaRestClient\EmbeddedFormat;
use KafkaRestClient\SerializationFormat;

final class ConsumerConfig implements ConsumerConfigInterface
{
    /** @var Config */
    private $commonConfig;

    /** @var string */
    private $groupId;

    /** @var AutoOffsetReset */
    private $autoOffsetReset;

    /** @var bool */
    private $autoCommitEnable;

    /** @var int */
    private $autoCommitIntervalMs;

    public function __construct(
        Config $commonConfig,
        string $groupId,
        AutoOffsetReset $autoOffsetReset,
        bool $autoCommitEnable = true,
        int $autoCommitIntervalMs = 1000
    )
    {
        $this->commonConfig = $commonConfig;
        $this->groupId = $groupId;
        $this->autoOffsetReset = $autoOffsetReset;
        $this->autoCommitEnable = $autoCommitEnable;
        $this->autoCommitIntervalMs = $autoCommitIntervalMs;
    }

    public function url(): string
    {
        return $this->commonConfig->url();
    }

    public function embeddedFormat(): EmbeddedFormat
    {
        return $this->commonConfig->embeddedFormat();
    }

    public function apiVersion(): ApiVersion
    {
        return $this->commonConfig->apiVersion();
    }

    public function serializationFormat(): SerializationFormat
    {
        return $this->commonConfig->serializationFormat();
    }

    public function contentTypeHeader(): string
    {
        return $this->commonConfig->contentTypeHeader();
    }

    public function acceptHeader(): string
    {
        return $this->commonConfig->acceptHeader();
    }

    public function groupId(): string
    {
        return $this->groupId;
    }

    public function autoOffsetReset(): AutoOffsetReset
    {
        return $this->autoOffsetReset;
    }

    public function autoCommitEnable(): bool
    {
        return $this->autoCommitEnable;
    }

    public function autoCommitIntervalMs(): int
    {
        return $this->autoCommitIntervalMs;
    }
}