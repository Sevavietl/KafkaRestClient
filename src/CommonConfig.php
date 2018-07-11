<?php

namespace KafkaRestClient;


final class CommonConfig implements Config
{
    /** @var string */
    private $url;

    /** @var EmbeddedFormat */
    private $embeddedFormat;

    /** @var ApiVersion */
    private $apiVersion;

    /** @var SerializationFormat */
    private $serializationFormat;

    /** @var string */
    private $contentTypeHeader;

    /** @var string */
    private $acceptHeader;

    public static function fromArray(array $data): self
    {
        return new self(
            $data['url'],
            $data['embeddedFormat'],
            $data['apiVersion'],
            $data['serializationFormat']
        );
    }

    public function __construct(
        string $url,
        EmbeddedFormat $embeddedFormat,
        ApiVersion $apiVersion,
        SerializationFormat $serializationFormat
    )
    {
        $this->url = $url;
        $this->embeddedFormat = $embeddedFormat;
        $this->apiVersion = $apiVersion;
        $this->serializationFormat = $serializationFormat;
    }

    public function url(): string
    {
        return $this->url;
    }

    public function embeddedFormat(): EmbeddedFormat
    {
        return $this->embeddedFormat;
    }

    public function apiVersion(): ApiVersion
    {
        return $this->apiVersion;
    }

    public function serializationFormat(): SerializationFormat
    {
        return $this->serializationFormat;
    }

    public function contentTypeHeader(): string
    {
        if (null === $this->contentTypeHeader) {
            $this->contentTypeHeader = sprintf(
                'application/vnd.kafka.%s.%s+%s',
                $this->embeddedFormat->getValue(),
                $this->apiVersion->getValue(),
                $this->serializationFormat->getValue()
            );
        }

        return $this->contentTypeHeader;
    }

    public function acceptHeader(): string
    {
        if (null === $this->acceptHeader) {
            $this->acceptHeader = sprintf(
                'application/vnd.kafka.%s+%s',
                $this->apiVersion->getValue(),
                $this->serializationFormat->getValue()
            );
        }

        return $this->acceptHeader;
    }
}