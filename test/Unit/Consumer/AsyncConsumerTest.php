<?php

namespace KafkaRestClient\Test\Unit\Consumer;

use Amp\Artax\Client;
use Amp\Artax\Response;
use Amp\ByteStream\InMemoryStream;
use Amp\ByteStream\Message;
use function Amp\Promise\wait;
use Amp\Success;
use KafkaRestClient\Consumer\AsyncConsumer;
use KafkaRestClient\Consumer\AutoOffsetReset;
use KafkaRestClient\Consumer\ConsumerRecord;
use KafkaRestClient\ConsumerConfig;
use KafkaRestClient\EmbeddedFormat;
use KafkaRestClient\TopicPartition;
use KafkaRestClient\UrlBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class AsyncConsumerTest extends TestCase
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

        $this->client = $this->createMock(Client::class);

        $this->urlBuilder = $this->createMock(UrlBuilder::class);
        $this->urlBuilder->method('baseUrl')->willReturn($this->urlBuilder);
        $this->urlBuilder->method('consumers')->with()->willReturn($this->urlBuilder);
    }

    public function testCreatesConsumerInstance(): void
    {
        $response = $this->createMock(Response::class);
        $response->method('getStatus')->willReturn(200);
        $response->method('getBody')->willReturn(new Message(new InMemoryStream(json_encode([
            'instance_id' => $instanceId = 'my_consumer',
            'base_uri' => $baseUri = 'http://proxy-instance.kafkaproxy.example.com/consumers/testgroup/instances/my_consumer',
        ]))));
        $this->client->method('request')->willReturn(new Success($response));

        $this->urlBuilder->method('get')->willReturn('http://proxy-instance.kafkaproxy.example.com/consumers/testgroup');

        /** @var AsyncConsumer $consumer */
        $consumer = wait((new AsyncConsumer($this->config, $this->client, $this->urlBuilder))->create());

        $this->assertSame($instanceId, $consumer->instanceId());
    }

    public function testCanDeleteConsumerInstance(): void
    {
        $response = $this->createMock(Response::class);
        $response->method('getStatus')->willReturn(204);
        $this->client->method('request')->willReturn(new Success($response));

        $this->urlBuilder->method('instances')->willReturn($this->urlBuilder);
        $this->urlBuilder->method('subscription')->willReturn($this->urlBuilder);
        $this->urlBuilder->method('get')->willReturn('http://proxy-instance.kafkaproxy.example.com/consumers/testgroup/instances/my_consumer/subscription');

        wait((new AsyncConsumer($this->config, $this->client, $this->urlBuilder))->withInstanceId('my_consumer')->delete());
    }

    public function testCanSubscribeToListOfTopics(): void
    {
        $response = $this->createMock(Response::class);
        $response->method('getStatus')->willReturn(204);
        $this->client->method('request')->willReturn(new Success($response));

        $this->urlBuilder->method('instances')->willReturn($this->urlBuilder);
        $this->urlBuilder->method('subscription')->willReturn($this->urlBuilder);
        $this->urlBuilder->method('get')->willReturn('http://proxy-instance.kafkaproxy.example.com/consumers/testgroup/instances/my_consumer/subscription');

        $topics = ['test1', 'test2'];

        wait((new AsyncConsumer($this->config, $this->client, $this->urlBuilder))->withInstanceId('my_consumer')->subscribe($topics));
    }

    public function testCanGetListOfSubscribedTopics(): void
    {
        $response = $this->createMock(Response::class);
        $response->method('getStatus')->willReturn(200);
        $response->method('getBody')->willReturn(new Message(new InMemoryStream(json_encode([
            'topics' => $topics = [
                'test1',
                'test2',
            ]
        ]))));
        $this->client->method('request')->willReturn(new Success($response));

        $this->urlBuilder->method('instances')->willReturn($this->urlBuilder);
        $this->urlBuilder->method('subscription')->willReturn($this->urlBuilder);
        $this->urlBuilder->method('get')->willReturn('http://proxy-instance.kafkaproxy.example.com/consumers/testgroup/instances/my_consumer/subscription');

        $consumer = (new AsyncConsumer($this->config, $this->client, $this->urlBuilder))->withInstanceId('my_consumer');

        $this->assertSame($topics, wait($consumer->subscription()));
    }

    public function testCanUnsubscribe(): void
    {
        $response = $this->createMock(Response::class);
        $response->method('getStatus')->willReturn(204);
        $this->client->method('request')->willReturn(new Success($response));

        $this->urlBuilder->method('instances')->willReturn($this->urlBuilder);
        $this->urlBuilder->method('subscription')->willReturn($this->urlBuilder);
        $this->urlBuilder->method('get')->willReturn('http://proxy-instance.kafkaproxy.example.com/consumers/testgroup/instances/my_consumer/subscription');

        wait((new AsyncConsumer($this->config, $this->client, $this->urlBuilder))->withInstanceId('my_consumer')->unsubscribe());
    }

    public function testCanAssignListOfPartitions(): void
    {
        $response = $this->createMock(Response::class);
        $response->method('getStatus')->willReturn(204);
        $this->client->method('request')->willReturn(new Success($response));

        $this->urlBuilder->method('instances')->willReturn($this->urlBuilder);
        $this->urlBuilder->method('assignments')->willReturn($this->urlBuilder);
        $this->urlBuilder->method('get')->willReturn('http://proxy-instance.kafkaproxy.example.com/consumers/testgroup/instances/my_consumer/assignments');

        $partitions = [
            TopicPartition::fromArray(['topic' => 'test', 'partition' => 0]),
            TopicPartition::fromArray(['topic' => 'test', 'partition' => 1]),
        ];

        wait((new AsyncConsumer($this->config, $this->client, $this->urlBuilder))->withInstanceId('my_consumer')->assign($partitions));
    }

    public function testGetsListOfAssignedPartitions(): void
    {
        $response = $this->createMock(Response::class);
        $response->method('getStatus')->willReturn(200);
        $response->method('getBody')->willReturn(new Message(new InMemoryStream(json_encode([
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
        ]))));
        $this->client->method('request')->willReturn(new Success($response));

        $this->urlBuilder->method('instances')->willReturn($this->urlBuilder);
        $this->urlBuilder->method('assignments')->willReturn($this->urlBuilder);
        $this->urlBuilder->method('get')->willReturn('http://proxy-instance.kafkaproxy.example.com/consumers/testgroup/instances/my_consumer/assignments');

        $partitions = wait((new AsyncConsumer($this->config, $this->client, $this->urlBuilder))->withInstanceId('my_consumer')->assignment());

        $this->assertCount(2, $partitions);
        $this->assertContainsOnlyInstancesOf(TopicPartition::class, $partitions);
    }

    public function testCanPollRecords(): void
    {
        $response = $this->createMock(Response::class);
        $response->method('getStatus')->willReturn(200);
        $response->method('getBody')->willReturn(new Message(new InMemoryStream(json_encode([
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
        ]))));
        $this->client->method('request')->willReturn(new Success($response));

        $this->urlBuilder->method('instances')->willReturn($this->urlBuilder);
        $this->urlBuilder->method('subscription')->willReturn($this->urlBuilder);
        $this->urlBuilder->method('get')->willReturn('http://proxy-instance.kafkaproxy.example.com/consumers/testgroup/instances/my_consumer/subscription');

        $consumer = (new AsyncConsumer($this->config, $this->client, $this->urlBuilder))->withInstanceId('my_consumer');
        $consumerRecords = wait($consumer->poll());

        $this->assertCount(2, $consumerRecords);
        $this->assertContainsOnlyInstancesOf(ConsumerRecord::class, $consumerRecords);
    }

    public function testCommitsAllOffsets(): void
    {
        $response = $this->createMock(Response::class);
        $response->method('getStatus')->willReturn(200);
        $this->client->method('request')->willReturn(new Success($response));

        $this->urlBuilder->method('instances')->willReturn($this->urlBuilder);
        $this->urlBuilder->method('subscription')->willReturn($this->urlBuilder);
        $this->urlBuilder->method('offsets')->willReturn($this->urlBuilder);
        $this->urlBuilder->method('get')->willReturn('http://proxy-instance.kafkaproxy.example.com/consumers/testgroup/instances/my_consumer/offsets');

        wait((new AsyncConsumer($this->config, $this->client, $this->urlBuilder))->withInstanceId('my_consumer')->commit());
    }

    public function testCommitsListOfOffsets(): void
    {
        $response = $this->createMock(Response::class);
        $response->method('getStatus')->willReturn(200);
        $this->client->method('request')->willReturn(new Success($response));

        $this->urlBuilder->method('instances')->willReturn($this->urlBuilder);
        $this->urlBuilder->method('subscription')->willReturn($this->urlBuilder);
        $this->urlBuilder->method('offsets')->willReturn($this->urlBuilder);
        $this->urlBuilder->method('get')->willReturn('http://proxy-instance.kafkaproxy.example.com/consumers/testgroup/instances/my_consumer/offsets');

        $offsets = new \SplObjectStorage();
        $offsets->attach(TopicPartition::fromArray(['topic' => 'test', 'partition' => 0]), 20);
        $offsets->attach(TopicPartition::fromArray(['topic' => 'test', 'partition' => 1]), 30);

        wait((new AsyncConsumer($this->config, $this->client, $this->urlBuilder))->withInstanceId('my_consumer')->commit());
    }

    public function testGetsLastCommittedOffsets(): void
    {
        $response = $this->createMock(Response::class);
        $response->method('getStatus')->willReturn(200);
        $response->method('getBody')->willReturn(new Message(new InMemoryStream(json_encode([
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
        ]))));

        $this->client->method('request')->willReturn(new Success($response));

        $this->urlBuilder->method('instances')->willReturn($this->urlBuilder);
        $this->urlBuilder->method('subscription')->willReturn($this->urlBuilder);
        $this->urlBuilder->method('offsets')->willReturn($this->urlBuilder);
        $this->urlBuilder->method('get')->willReturn('http://proxy-instance.kafkaproxy.example.com/consumers/testgroup/instances/my_consumer/offsets');

        $partitions = [
            TopicPartition::fromArray(['topic' => 'test', 'partition' => 0]),
            TopicPartition::fromArray(['topic' => 'test', 'partition' => 1]),
        ];

        $offsets = wait((new AsyncConsumer($this->config, $this->client, $this->urlBuilder))->withInstanceId('my_consumer')->committed($partitions));

        $this->assertInstanceOf(\SplObjectStorage::class, $offsets);
        $this->assertCount(2, $offsets);
    }
}