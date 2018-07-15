<?php

namespace KafkaRestClient\Test\Unit\Producer;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;
use KafkaRestClient\Producer\ProducerRecord;
use KafkaRestClient\Producer\RecordMetadata;
use KafkaRestClient\Producer\SyncProducer;
use KafkaRestClient\ProducerConfig;
use KafkaRestClient\UrlBuilder;
use PHPUnit\Framework\TestCase;

final class SyncProducerTest extends TestCase
{
    public function testSendsRecordToKafka(): void
    {
        $config = $this->createMock(ProducerConfig::class);
        $config->method('url')->willReturn('url');
        $config->method('contentTypeHeader')->willReturn('contentTypeHeader');
        $config->method('acceptHeader')->willReturn('acceptHeader');

        $client = $this->createMock(ClientInterface::class);
        $response = $this->createMock(Response::class);
        $response->method('getStatusCode')->willReturn(200);
        $stream = fopen('php://memory','r+');
        fwrite($stream, json_encode([
            'offsets' => [
                [
                    'partition' => 0,
                    'offset' => 0,
                    'error_code' => null,
                    'error' => null,
                ],
            ]
        ]));
        rewind($stream);
        $response->method('getBody')->willReturn(new Stream($stream));
        $client->method('send')->willReturn($response);

        $urlBuilder = $this->createMock(UrlBuilder::class);
        $urlBuilder->method('baseUrl')->willReturn($urlBuilder);
        $urlBuilder->method('topics')->with('topic')->willReturn($urlBuilder);
        $urlBuilder->method('get')->willReturn('url/topics/topic');

        $producer = new SyncProducer($config, $client, $urlBuilder);
        $producerRecord = ProducerRecord::fromValue($topic = 'topic', $value = 'value');

        $recordMetadata = $producer->send($producerRecord);

        $this->assertInstanceOf(RecordMetadata::class, $recordMetadata);
    }
}