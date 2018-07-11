<?php

namespace KafkaRestClient\Test\Unit;

use Codeception\Test\Unit;
use KafkaRestClient\ApiVersion;
use KafkaRestClient\CommonConfig;
use KafkaRestClient\EmbeddedFormat;
use KafkaRestClient\SerializationFormat;

final class CommonConfigTest extends Unit
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

    protected function _before()
    {
        $this->commonConfig = CommonConfig::fromArray([
            'url' => $this->url = 'url',
            'embeddedFormat' => $this->embeddedFormat = EmbeddedFormat::JSON(),
            'apiVersion' => $this->apiVersion = ApiVersion::V2(),
            'serializationFormat' => $this->serializationFormat = SerializationFormat::JSON(),
        ]);
    }

    public function test_it_can_be_constructed_from_array(): void
    {
        $this->assertSame($this->url, $this->commonConfig->url());
        $this->assertSame($this->embeddedFormat, $this->commonConfig->embeddedFormat());
        $this->assertSame($this->apiVersion, $this->commonConfig->apiVersion());
        $this->assertSame($this->serializationFormat, $this->commonConfig->serializationFormat());
    }

    public function test_it_can_form_content_type_header(): void
    {
        $this->assertEquals('application/vnd.kafka.json.v2+json', $this->commonConfig->contentTypeHeader());
    }

    public function test_it_can_form_accept_header(): void
    {
        $this->assertEquals('application/vnd.kafka.v2+json', $this->commonConfig->acceptHeader());
    }
}