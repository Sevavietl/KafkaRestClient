<?php

namespace KafkaRestClient\Producer;

use KafkaRestClient\ApiVersion;
use KafkaRestClient\Config;
use KafkaRestClient\EmbeddedFormat;
use KafkaRestClient\ProducerConfig as ProducerConfigInterface;
use KafkaRestClient\SerializationFormat;

final class ProducerConfig implements ProducerConfigInterface
{
    /** @var Config */
    private $commonConfig;

    public function __construct(Config $commonConfig)
    {
        $this->commonConfig = $commonConfig;
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
}