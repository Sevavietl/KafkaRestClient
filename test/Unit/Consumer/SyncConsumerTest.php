<?php

namespace KafkaRestClient\Test\Unit\Consumer;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;
use KafkaRestClient\Consumer\AutoOffsetReset;
use KafkaRestClient\Consumer\ConsumerRecord;
use KafkaRestClient\Consumer\SyncConsumer;
use KafkaRestClient\ConsumerConfig;
use KafkaRestClient\EmbeddedFormat;
use KafkaRestClient\TopicPartition;
use KafkaRestClient\UrlBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class SyncConsumerTest extends TestCase
{
    /** @var MockObject */
    private $config;

    /** @var MockObject */
    private $client;

    /** @var MockObject */
    private $urlBuilder;

    protected function setUp(): void
    {
        $this->config = $this->createMock(ConsumerConfig::class);
        $this->config->method('url')->willReturn('url');
        $this->config->method('embeddedFormat')->willReturn(EmbeddedFormat::JSON());
        $this->config->method('contentTypeHeader')->willReturn('contentTypeHeader');
        $this->config->method('acceptHeader')->willReturn('acceptHeader');
        $this->config->method('autoOffsetReset')->willReturn(AutoOffsetReset::LATEST());
        $this->config->method('autoCommitEnable')->willReturn(true);
        $this->config->method('autoCommitIntervalMs')->willReturn(1000);

        $this->client = $this->createMock(ClientInterface::class);

        $this->urlBuilder = $this->createMock(UrlBuilder::class);
        $this->urlBuilder->method('baseUrl')->willReturn($this->urlBuilder);
        $this->urlBuilder->method('consumers')->with()->willReturn($this->urlBuilder);
    }

    public function testCreatesConsumerInstance(): void
    {
        $response = $this->createMock(Response::class);
        $response->expects($this->once())->method('getStatusCode')->willReturn(200);
        $response->expects($this->once())->method('getBody')->willReturn($this->createStream([
            'instance_id' => $instanceId = 'my_consumer',
            'base_uri' => $baseUri = 'http://proxy-instance.kafkaproxy.example.com/consumers/testgroup/instances/my_consumer',
        ]));
        $this->client->expects($this->once())->method('send')->willReturn($response);

        $this->urlBuilder->expects($this->once())->method('get')->willReturn('http://proxy-instance.kafkaproxy.example.com/consumers/testgroup');

        $consumer = (new SyncConsumer($this->config, $this->client, $this->urlBuilder))->create();

        $this->assertSame($instanceId, $consumer->instanceId());
    }

    public function testCanDeleteConsumerInstance(): void
    {
        $response = $this->createMock(Response::class);
        $response->expects($this->once())->method('getStatusCode')->willReturn(204);
        $this->client->expects($this->once())->method('send')->willReturn($response);

        $this->urlBuilder->expects($this->once())->method('instances')->willReturn($this->urlBuilder);
        $this->urlBuilder->expects($this->once())->method('get')->willReturn('http://proxy-instance.kafkaproxy.example.com/consumers/testgroup/instances/my_consumer/subscription');

        (new SyncConsumer($this->config, $this->client, $this->urlBuilder))->withInstanceId('my_consumer')->delete();
    }

    public function testCanSubscribeToListOfTopics(): void
    {
        $response = $this->createMock(Response::class);
        $response->expects($this->once())->method('getStatusCode')->willReturn(204);
        $this->client->expects($this->once())->method('send')->willReturn($response);

        $this->urlBuilder->expects($this->once())->method('instances')->willReturn($this->urlBuilder);
        $this->urlBuilder->expects($this->once())->method('subscription')->willReturn($this->urlBuilder);
        $this->urlBuilder->expects($this->once())->method('get')->willReturn('http://proxy-instance.kafkaproxy.example.com/consumers/testgroup/instances/my_consumer/subscription');

        $topics = ['test1', 'test2'];

        $consumer = (new SyncConsumer($this->config, $this->client, $this->urlBuilder))->withInstanceId('my_consumer')->subscribe($topics);

        $this->assertInstanceOf(SyncConsumer::class, $consumer);
    }

    public function testCanGetListOfSubscribedTopics(): void
    {
        $response = $this->createMock(Response::class);
        $response->expects($this->once())->method('getStatusCode')->willReturn(200);
        $response->expects($this->once())->method('getBody')->willReturn($this->createStream([
            'topics' => $topics = [
                'test1',
                'test2',
            ]
        ]));
        $this->client->expects($this->once())->method('send')->willReturn($response);

        $this->urlBuilder->expects($this->once())->method('instances')->willReturn($this->urlBuilder);
        $this->urlBuilder->expects($this->once())->method('subscription')->willReturn($this->urlBuilder);
        $this->urlBuilder->expects($this->once())->method('get')->willReturn('http://proxy-instance.kafkaproxy.example.com/consumers/testgroup/instances/my_consumer/subscription');

        $consumer = (new SyncConsumer($this->config, $this->client, $this->urlBuilder))->withInstanceId('my_consumer');

        $this->assertSame($topics, $consumer->subscription());
    }

    public function testCanUnsubscribe(): void
    {
        $response = $this->createMock(Response::class);
        $response->expects($this->once())->method('getStatusCode')->willReturn(204);
        $this->client->expects($this->once())->method('send')->willReturn($response);

        $this->urlBuilder->expects($this->once())->method('instances')->willReturn($this->urlBuilder);
        $this->urlBuilder->expects($this->once())->method('subscription')->willReturn($this->urlBuilder);
        $this->urlBuilder->expects($this->once())->method('get')->willReturn('http://proxy-instance.kafkaproxy.example.com/consumers/testgroup/instances/my_consumer/subscription');

        (new SyncConsumer($this->config, $this->client, $this->urlBuilder))->withInstanceId('my_consumer')->unsubscribe();
    }

    public function testCanAssignListOfPartitions(): void
    {
        $response = $this->createMock(Response::class);
        $response->expects($this->once())->method('getStatusCode')->willReturn(204);
        $this->client->expects($this->once())->method('send')->willReturn($response);

        $this->urlBuilder->expects($this->once())->method('instances')->willReturn($this->urlBuilder);
        $this->urlBuilder->expects($this->once())->method('assignments')->willReturn($this->urlBuilder);
        $this->urlBuilder->expects($this->once())->method('get')->willReturn('http://proxy-instance.kafkaproxy.example.com/consumers/testgroup/instances/my_consumer/assignments');

        $partitions = [
            TopicPartition::fromArray(['topic' => 'test', 'partition' => 0]),
            TopicPartition::fromArray(['topic' => 'test', 'partition' => 1]),
        ];

        (new SyncConsumer($this->config, $this->client, $this->urlBuilder))->withInstanceId('my_consumer')->assign($partitions);
    }


    public function testGetsListOfAssignedPartitions(): void
    {
        $response = $this->createMock(Response::class);
        $response->expects($this->once())->method('getStatusCode')->willReturn(200);
        $response->expects($this->once())->method('getBody')->willReturn($this->createStream([
            'partitions' => [
                [
                    'topic' => 'test',
                    'partition' => 0,
                ],
                [
                    'topic' => 'test',
                    'partition' => 1,
                ]
            ]
        ]));
        $this->client->expects($this->once())->method('send')->willReturn($response);

        $this->urlBuilder->expects($this->once())->method('instances')->willReturn($this->urlBuilder);
        $this->urlBuilder->expects($this->once())->method('assignments')->willReturn($this->urlBuilder);
        $this->urlBuilder->expects($this->once())->method('get')->willReturn('http://proxy-instance.kafkaproxy.example.com/consumers/testgroup/instances/my_consumer/assignments');

        $partitions = (new SyncConsumer($this->config, $this->client, $this->urlBuilder))->withInstanceId('my_consumer')->assignment();

        $this->assertCount(2, $partitions);
        $this->assertContainsOnlyInstancesOf(TopicPartition::class, $partitions);
    }

    public function testCanPollRecords(): void
    {
        $response = $this->createMock(Response::class);
        $response->expects($this->once())->method('getStatusCode')->willReturn(200);
        $response->expects($this->once())->method('getBody')->willReturn($this->createStream([
            [
                'topic' => 'test',
                'key' => 'somekey',
                'value' => ['foo' => 'bar'],
                'partition' => 1,
                'offset' => 10,
            ],
            [
                'topic' => 'test',
                'key' => 'somekey',
                'value' => ['foo', 'bar'],
                'partition' => 2,
                'offset' => 11,
            ]
        ]));
        $this->client->expects($this->once())->method('send')->willReturn($response);

        $this->urlBuilder->expects($this->once())->method('instances')->willReturn($this->urlBuilder);
        $this->urlBuilder->expects($this->once())->method('records')->willReturn($this->urlBuilder);
        $this->urlBuilder->expects($this->once())->method('withParameters')->willReturn($this->urlBuilder);
        $this->urlBuilder->expects($this->once())->method('get')->willReturn('http://proxy-instance.kafkaproxy.example.com/consumers/testgroup/instances/my_consumer/subscription');

        $consumerRecords = (new SyncConsumer($this->config, $this->client, $this->urlBuilder))
            ->withInstanceId('my_consumer')
            ->poll();

        $this->assertCount(2, $consumerRecords);
        $this->assertContainsOnlyInstancesOf(ConsumerRecord::class, $consumerRecords);
    }

    public function testCommitsAllOffsets(): void
    {
        $response = $this->createMock(Response::class);
        $response->expects($this->once())->method('getStatusCode')->willReturn(200);
        $this->client->expects($this->once())->method('send')->willReturn($response);

        $this->urlBuilder->expects($this->once())->method('instances')->willReturn($this->urlBuilder);
        $this->urlBuilder->expects($this->once())->method('offsets')->willReturn($this->urlBuilder);
        $this->urlBuilder->expects($this->once())->method('get')->willReturn('http://proxy-instance.kafkaproxy.example.com/consumers/testgroup/instances/my_consumer/offsets');

       (new SyncConsumer($this->config, $this->client, $this->urlBuilder))->withInstanceId('my_consumer')->commit();
    }

    public function testCommitsListOfOffsets(): void
    {
        $response = $this->createMock(Response::class);
        $response->expects($this->once())->method('getStatusCode')->willReturn(200);
        $this->client->expects($this->once())->method('send')->willReturn($response);

        $this->urlBuilder->expects($this->once())->method('instances')->willReturn($this->urlBuilder);
        $this->urlBuilder->expects($this->once())->method('offsets')->willReturn($this->urlBuilder);
        $this->urlBuilder->expects($this->once())->method('get')->willReturn('http://proxy-instance.kafkaproxy.example.com/consumers/testgroup/instances/my_consumer/offsets');

        $offsets = new \SplObjectStorage();
        $offsets->attach(TopicPartition::fromArray(['topic' => 'test', 'partition' => 0]), 20);
        $offsets->attach(TopicPartition::fromArray(['topic' => 'test', 'partition' => 1]), 30);

        (new SyncConsumer($this->config, $this->client, $this->urlBuilder))->withInstanceId('my_consumer')->commit($offsets);
    }

    public function testGetsLastCommittedOffsets(): void
    {
        $response = $this->createMock(Response::class);
        $response->expects($this->once())->method('getStatusCode')->willReturn(200);
        $response->expects($this->once())->method('getBody')->willReturn($this->createStream([
            'offsets' => [
                [
                    'topic' => 'test',
                    'partition' => 0,
                    'offset' => 21,
                    'metadata' => '',
                ],
                [
                    'topic' => 'test',
                    'partition' => 1,
                    'offset' => 31,
                    'metadata' => '',
                ],
            ]
        ]));
        $this->client->expects($this->once())->method('send')->willReturn($response);

        $this->urlBuilder->expects($this->once())->method('instances')->willReturn($this->urlBuilder);
        $this->urlBuilder->expects($this->once())->method('offsets')->willReturn($this->urlBuilder);
        $this->urlBuilder->expects($this->once())->method('get')->willReturn('http://proxy-instance.kafkaproxy.example.com/consumers/testgroup/instances/my_consumer/offsets');

        $partitions = [
            TopicPartition::fromArray(['topic' => 'test', 'partition' => 0]),
            TopicPartition::fromArray(['topic' => 'test', 'partition' => 1]),
        ];

        $offsets = (new SyncConsumer($this->config, $this->client, $this->urlBuilder))
            ->withInstanceId('my_consumer')
            ->committed($partitions);

        $this->assertInstanceOf(\SplObjectStorage::class, $offsets);
        $this->assertCount(2, $offsets);
    }

    private function createStream(array $data): Stream
    {
        $stream = fopen('php://memory','r+');
        fwrite($stream, json_encode($data));
        rewind($stream);

        return new Stream($stream);
    }
}