<?php

namespace KafkaRestClient\Test\Unit\Producer;

use Amp\Artax\Client;
use Amp\Artax\Response;
use Amp\ByteStream\InMemoryStream;
use Amp\ByteStream\Message;
use function Amp\Promise\wait;
use Amp\Success;
use KafkaRestClient\Producer\AsyncProducer;
use KafkaRestClient\Producer\ProducerRecord;
use KafkaRestClient\Producer\RecordMetadata;
use KafkaRestClient\ProducerConfig;
use KafkaRestClient\UrlBuilder;
use PHPUnit\Framework\TestCase;

final class AsyncProducerTest extends TestCase
{
    public function testSendsRecordToKafka(): void
    {
        $config = $this->createMock(ProducerConfig::class);
        $config->method('url')->willReturn('url');
        $config->method('contentTypeHeader')->willReturn('contentTypeHeader');
        $config->method('acceptHeader')->willReturn('acceptHeader');

        $client = $this->createMock(Client::class);
        $response = $this->createMock(Response::class);
        $response->method('getStatus')->willReturn(200);
        $response->method('getBody')->willReturn(new Message(new InMemoryStream(json_encode([
            'offsets' => [
                [
                    'partition' => 0,
                    'offset' => 0,
                    'error_code' => null,
                    'error' => null,
                ],
            ]
        ]))));
        $client->method('request')->willReturn(new Success($response));

        $urlBuilder = $this->createMock(UrlBuilder::class);
        $urlBuilder->method('baseUrl')->willReturn($urlBuilder);
        $urlBuilder->method('topics')->with('topic')->willReturn($urlBuilder);
        $urlBuilder->method('get')->willReturn('url/topics/topic');

        $producer = new AsyncProducer($config, $client, $urlBuilder);
        $producerRecord = ProducerRecord::fromValue($topic = 'topic', $value = 'value');

        $recordMetadata = wait($producer->send($producerRecord));

        $this->assertInstanceOf(RecordMetadata::class, $recordMetadata);
    }
}