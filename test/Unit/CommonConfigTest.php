<?php

namespace KafkaRestClient\Test\Unit;

use KafkaRestClient\ApiVersion;
use KafkaRestClient\CommonConfig;
use KafkaRestClient\EmbeddedFormat;
use KafkaRestClient\SerializationFormat;
use PHPUnit\Framework\TestCase;

final class CommonConfigTest extends TestCase
{
    /** @var CommonConfig */
    private $commonConfig;

    /** @var string */
    private $url;

    /** @var EmbeddedFormat */
    private $embeddedFormat;

    /** @var ApiVersion */
    private $apiVersion;

    /** @var SerializationFormat */
    private $serializationFormat;

    protected function setUp(): void
    {
        $this->commonConfig = CommonConfig::fromArray([
            'url' => $this->url = 'url',
            'embeddedFormat' => $this->embeddedFormat = EmbeddedFormat::JSON(),
            'apiVersion' => $this->apiVersion = ApiVersion::V2(),
            'serializationFormat' => $this->serializationFormat = SerializationFormat::JSON(),
        ]);
    }

    public function testCanBeConstructedFromArray(): void
    {
        $this->assertSame($this->url, $this->commonConfig->url());
        $this->assertSame($this->embeddedFormat, $this->commonConfig->embeddedFormat());
        $this->assertSame($this->apiVersion, $this->commonConfig->apiVersion());
        $this->assertSame($this->serializationFormat, $this->commonConfig->serializationFormat());
    }

    public function testFormsContentTypeHeader(): void
    {
        $this->assertEquals('application/vnd.kafka.json.v2+json', $this->commonConfig->contentTypeHeader());
    }

    public function testFormsAcceptHeader(): void
    {
        $this->assertEquals('application/vnd.kafka.v2+json', $this->commonConfig->acceptHeader());
    }
}