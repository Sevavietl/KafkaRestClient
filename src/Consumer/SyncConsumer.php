<?php

namespace KafkaRestClient\Consumer;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use KafkaRestClient\ConsumerConfig;
use KafkaRestClient\DefaultUrlBuilder;
use KafkaRestClient\KafkaRestException;
use KafkaRestClient\TopicPartition;
use KafkaRestClient\UrlBuilder;

final class SyncConsumer implements Consumer
{
    /** @var ConsumerConfig */
    private $config;

    /** @var ClientInterface */
    private $client;

    /** @var UrlBuilder */
    private $urlBuilder;

    /** @var string */
    private $instanceId;

    public function __construct(ConsumerConfig $config, ?ClientInterface $client = null, ?UrlBuilder $urlBuilder = null)
    {
        $this->config = $config;
        $this->client = $client ?? new Client();
        $this->urlBuilder = $urlBuilder ?? new DefaultUrlBuilder();
    }

    public function withInstanceId(string $instanceId): Consumer
    {
        $consumer = new self($this->config, $this->client, $this->urlBuilder);
        $consumer->instanceId = $instanceId;

        return $consumer;
    }

    public function instanceId(): string
    {
        return $this->instanceId;
    }

    public function create(?string $name = null): Consumer
    {
        $request = new Request(
            'POST',
            $this->urlBuilder->baseUrl($this->config->url())->consumers($this->config->groupId())->get(),
            [
                'Content-Type' => $this->config->acceptHeader(),
            ],
            json_encode(array_filter([
                'name' => $name,
                'format' => $this->config->embeddedFormat()->getValue(),
                'auto.offset.reset' => $this->config->autoOffsetReset()->getValue(),
                'auto.commit.enable' => $this->config->autoCommitEnable(),
            ], function ($property) { return null !== $property; })) ?: null
        );

        $response = $this->client->send($request);

        if (200 !== $response->getStatusCode()) {
            throw KafkaRestException::fromJson((string) $response->getBody());
        }

        $data = json_decode((string) $response->getBody(), true);

        return $this->withInstanceId($data['instance_id']);
    }

    public function delete(): void
    {
        $request = new Request(
            'DELETE',
            $this->baseUri()->get(),
            [
                'Content-Type' => $this->config->contentTypeHeader(),
            ]
        );

        $response = $this->client->send($request);

        if (204 !== $response->getStatusCode()) {
            throw KafkaRestException::fromJson((string) $response->getBody());
        }
    }

    public function subscribe(array $topics): SyncConsumer
    {
        $request = new Request(
            'POST',
            $this->baseUri()->subscription()->get(),
            [
                'Content-Type' => $this->config->acceptHeader(),
            ],
            json_encode([
                'topics' => $topics,
            ]) ?: null
        );

        $response = $this->client->send($request);

        if (204 !== $response->getStatusCode()) {
            throw KafkaRestException::fromJson((string) $response->getBody());
        }

        return $this;
    }

    public function subscription(): array
    {
        $request = new Request(
            'GET',
            $this->baseUri()->subscription()->get(),
            [
                'Accept' => $this->config->acceptHeader(),
            ]
        );

        $response = $this->client->send($request);

        if (200 !== $response->getStatusCode()) {
            throw KafkaRestException::fromJson((string) $response->getBody());
        }

        $data = json_decode((string) $response->getBody(), true);

        return \is_array($data['topics']) ? $data['topics'] : [];
    }

    public function unsubscribe(): SyncConsumer
    {
        $request = new Request(
            'DELETE',
            $this->baseUri()->subscription()->get(),
            [
                'Accept' => $this->config->acceptHeader(),
            ]
        );

        $response = $this->client->send($request);

        if (204 !== $response->getStatusCode()) {
            throw KafkaRestException::fromJson((string) $response->getBody());
        }

        return $this;
    }

    public function assign(array $partitions): SyncConsumer
    {
        $request = new Request(
            'POST',
            $this->baseUri()->assignments()->get(),
            [
                'Content-Type' => $this->config->acceptHeader(),
            ],
            json_encode([
                'partitions' => $partitions,
            ]) ?: null
        );

        $response = $this->client->send($request);

        if (204 !== $response->getStatusCode()) {
            throw KafkaRestException::fromJson((string) $response->getBody());
        }

        return $this;
    }

    public function assignment(): array
    {
        $request = new Request(
            'GET',
            $this->baseUri()->assignments()->get(),
            [
                'Accept' => $this->config->acceptHeader(),
            ]
        );

        $response = $this->client->send($request);

        if (200 !== $response->getStatusCode()) {
            throw KafkaRestException::fromJson((string) $response->getBody());
        }

        $data = json_decode((string) $response->getBody(), true);

        return array_reduce($data['partitions'], function (array $partitions, array $partition) {
            $partitions[] = TopicPartition::fromArray($partition);

            return $partitions;
        }, []);
    }

    public function poll(?int $timeout = null, ?int $maxBytes = null): array
    {
        $request = new Request(
            'GET',
            $this->baseUri()->records()->withParameters(array_filter(['timeout' => $timeout, 'max_bytes' => $maxBytes]))->get(),
            [
                'Accept' => $this->config->contentTypeHeader(),
            ]
        );

        $response = $this->client->send($request);

        if (200 !== $response->getStatusCode()) {
            throw KafkaRestException::fromJson((string) $response->getBody());
        }

        return array_map(function ($record) {
            return ConsumerRecord::fromArray($record);
        }, json_decode((string) $response->getBody(), true));
    }

    public function commit(?\SplObjectStorage $offsets = null): SyncConsumer
    {
        $body = null;

        if (null !== $offsets) {
            $body = [];
            /** @var TopicPartition $topicPartion */
            foreach($offsets as $topicPartion) {
                $body[] = array_merge($topicPartion->jsonSerialize(), ['offset' => $offsets[$topicPartion]]);
            }
            $body = \json_encode($body) ?: null;
        }

        $request = new Request(
            'POST',
            $this->baseUri()->offsets()->get(),
            [
                'Content-Type' => $this->config->contentTypeHeader(),
            ],
            $body
        );

        $response = $this->client->send($request);

        if (200 !== $response->getStatusCode()) {
            throw KafkaRestException::fromJson((string) $response->getBody());
        }

        return $this;
    }

    public function committed(array $partitions): \SplObjectStorage
    {
        $request = new Request(
            'GET',
            $this->baseUri()->offsets()->get(),
            [
                'Accept' => $this->config->contentTypeHeader(),
            ],
            json_encode([
                'partitions' => $partitions,
            ]) ?: null
        );

        $response = $this->client->send($request);

        if (200 !== $response->getStatusCode()) {
            throw KafkaRestException::fromJson((string) $response->getBody());
        }

        $data = json_decode((string) $response->getBody(), true);

        return array_reduce($data['offsets'], function (\SplObjectStorage $offsets, array $offset) {
            $offsets->attach(TopicPartition::fromArray($offset), OffsetAndMetadata::fromArray($offset));

            return $offsets;
        }, new \SplObjectStorage());
    }

    private function baseUri(): UrlBuilder
    {
        return $this->urlBuilder
            ->baseUrl($this->config->url())
            ->consumers($this->config->groupId())
            ->instances($this->instanceId);
    }
}