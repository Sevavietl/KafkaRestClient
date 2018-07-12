<?php

namespace KafkaRestClient\Test\Unit\Consumer;

use Amp\Artax\Client;
use Amp\Artax\Response;
use Amp\ByteStream\InMemoryStream;
use Amp\ByteStream\Message;
use function Amp\Promise\wait;
use Amp\Success;
use Codeception\Test\Unit;
use KafkaRestClient\Consumer\AsyncConsumer;
use KafkaRestClient\Consumer\AutoOffsetReset;
use KafkaRestClient\Consumer\ConsumerRecord;
use KafkaRestClient\ConsumerConfig;
use KafkaRestClient\EmbeddedFormat;
use KafkaRestClient\UrlBuilder;
use PHPUnit\Framework\MockObject\MockObject;

final class AsyncConsumerTest extends Unit
{
    /** @var MockObject */
    private $config;

    /** @var MockObject */
    private $client;

    /** @var MockObject */
    private $urlBuilder;

    protected function _before()
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

    public function test_it_creates_consumer_instance(): void
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

    public function test_it_can_delete_consumer_instance(): void
    {
        $response = $this->createMock(Response::class);
        $response->method('getStatus')->willReturn(204);
        $this->client->method('request')->willReturn(new Success($response));

        $this->urlBuilder->method('instances')->willReturn($this->urlBuilder);
        $this->urlBuilder->method('subscription')->willReturn($this->urlBuilder);
        $this->urlBuilder->method('get')->willReturn('http://proxy-instance.kafkaproxy.example.com//consumers/testgroup/instances/my_consumer/subscription');

        wait((new AsyncConsumer($this->config, $this->client, $this->urlBuilder))->withInstanceId('my_consumer')->delete());
    }

    public function test_it_can_subscribe_to_list_of_topics(): void
    {
        $response = $this->createMock(Response::class);
        $response->method('getStatus')->willReturn(204);
        $this->client->method('request')->willReturn(new Success($response));

        $this->urlBuilder->method('instances')->willReturn($this->urlBuilder);
        $this->urlBuilder->method('subscription')->willReturn($this->urlBuilder);
        $this->urlBuilder->method('get')->willReturn('http://proxy-instance.kafkaproxy.example.com//consumers/testgroup/instances/my_consumer/subscription');

        $topics = ['test1', 'test2'];

        wait((new AsyncConsumer($this->config, $this->client, $this->urlBuilder))->withInstanceId('my_consumer')->subscribe($topics));
    }

    public function test_it_can_get_list_of_subscribed_topics(): void
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
        $this->urlBuilder->method('get')->willReturn('http://proxy-instance.kafkaproxy.example.com//consumers/testgroup/instances/my_consumer/subscription');

        $topics = ['test1', 'test2'];

        $consumer = (new AsyncConsumer($this->config, $this->client, $this->urlBuilder))->withInstanceId('my_consumer');

        $this->assertSame($topics, wait($consumer->subscription($topics)));
    }

    public function test_it_can_unsubscribe(): void
    {
        $response = $this->createMock(Response::class);
        $response->method('getStatus')->willReturn(204);
        $this->client->method('request')->willReturn(new Success($response));

        $this->urlBuilder->method('instances')->willReturn($this->urlBuilder);
        $this->urlBuilder->method('subscription')->willReturn($this->urlBuilder);
        $this->urlBuilder->method('get')->willReturn('http://proxy-instance.kafkaproxy.example.com//consumers/testgroup/instances/my_consumer/subscription');

        wait((new AsyncConsumer($this->config, $this->client, $this->urlBuilder))->withInstanceId('my_consumer')->unsubscribe());
    }

    public function test_it_can_poll_records(): void
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
        $this->urlBuilder->method('get')->willReturn('http://proxy-instance.kafkaproxy.example.com//consumers/testgroup/instances/my_consumer/subscription');

        $consumer = (new AsyncConsumer($this->config, $this->client, $this->urlBuilder))->withInstanceId('my_consumer');
        $consumerRecords = wait($consumer->poll());

        $this->assertCount(2, $consumerRecords);
        $this->assertContainsOnlyInstancesOf(ConsumerRecord::class, $consumerRecords);
    }
}