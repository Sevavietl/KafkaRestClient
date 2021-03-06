<?php

namespace KafkaRestClient\Consumer;

use Amp\Artax\Client;
use Amp\Artax\DefaultClient;
use Amp\Artax\Request;
use Amp\Artax\Response;
use function Amp\call;
use Amp\Promise;
use KafkaRestClient\ConsumerConfig;
use KafkaRestClient\DefaultUrlBuilder;
use KafkaRestClient\KafkaRestException;
use KafkaRestClient\TopicPartition;
use KafkaRestClient\UrlBuilder;

final class AsyncConsumer implements Consumer
{
    /** @var ConsumerConfig */
    private $config;

    /** @var Client */
    private $client;

    /** @var UrlBuilder */
    private $urlBuilder;

    /** @var string */
    private $instanceId;

    public function __construct(ConsumerConfig $config, ?Client $client = null, ?UrlBuilder $urlBuilder = null)
    {
        $this->config = $config;
        $this->client = $client ?? new DefaultClient();
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

    public function create(?string $name = null): Promise
    {
        return call(function () use ($name) {
            $request = (new Request($this->urlBuilder->baseUrl($this->config->url())->consumers($this->config->groupId())->get(), 'POST'))
                ->withHeader('Content-Type', $this->config->acceptHeader())
                ->withBody(json_encode(array_filter([
                    'name' => $name,
                    'format' => $this->config->embeddedFormat()->getValue(),
                    'auto.offset.reset' => $this->config->autoOffsetReset()->getValue(),
                    'auto.commit.enable' => $this->config->autoCommitEnable(),
                ], function ($property) { return null !== $property; })));

            /** @var Response $response */
            $response = yield $this->client->request($request);

            if (200 !== $response->getStatus()) {
                throw KafkaRestException::fromJson(yield $response->getBody());
            }

            $data = json_decode(yield $response->getBody(), true);

            return $this->withInstanceId($data['instance_id']);
        });
    }

    public function delete(): Promise
    {
        return call(function () {
            $request = (new Request($this->baseUri()->get(), 'DELETE'))
                ->withHeader('Content-Type', $this->config->contentTypeHeader());

            /** @var Response $response */
            $response = yield $this->client->request($request);

            if (204 !== $response->getStatus()) {
                throw KafkaRestException::fromJson(yield $response->getBody());
            }
        });
    }

    public function subscribe(array $topics): Promise
    {
        return call(function () use ($topics) {
            $request = (new Request($this->baseUri()->subscription()->get(), 'POST'))
                ->withHeader('Content-Type', $this->config->acceptHeader())
                ->withBody(json_encode([
                    'topics' => $topics,
                ]));

            /** @var Response $response */
            $response = yield $this->client->request($request);

            if (204 !== $response->getStatus()) {
                throw KafkaRestException::fromJson(yield $response->getBody());
            }

            return $this;
        });
    }

    public function subscription(): Promise
    {
        return call(function () {
            $request = (new Request($this->baseUri()->subscription()->get(), 'GET'))
                ->withHeader('Accept', $this->config->acceptHeader());

            /** @var Response $response */
            $response = yield $this->client->request($request);

            if (200 !== $response->getStatus()) {
                throw KafkaRestException::fromJson(yield $response->getBody());
            }

            $data = json_decode(yield $response->getBody(), true);

            return $data['topics'];
        });
    }

    public function unsubscribe(): Promise
    {
        return call(function () {
            $request = (new Request($this->baseUri()->subscription()->get(), 'DELETE'))
                ->withHeader('Accept', $this->config->acceptHeader());

            /** @var Response $response */
            $response = yield $this->client->request($request);

            if (204 !== $response->getStatus()) {
                throw KafkaRestException::fromJson(yield $response->getBody());
            }

            return $this;
        });
    }

    public function assign(array $partitions): Promise
    {
        return call(function () use ($partitions) {
            $request = (new Request($this->baseUri()->assignments()->get(), 'POST'))
                ->withHeader('Content-Type', $this->config->acceptHeader())
                ->withBody(json_encode([
                    'partitions' => $partitions,
                ]));

            /** @var Response $response */
            $response = yield $this->client->request($request);

            if (204 !== $response->getStatus()) {
                throw KafkaRestException::fromJson(yield $response->getBody());
            }

            return $this;
        });
    }

    public function assignment(): Promise
    {
        return call(function () {
            $request = (new Request($this->baseUri()->assignments()->get(), 'GET'))
                ->withHeader('Accept', $this->config->acceptHeader());

            /** @var Response $response */
            $response = yield $this->client->request($request);

            if (200 !== $response->getStatus()) {
                throw KafkaRestException::fromJson(yield $response->getBody());
            }

            $data = json_decode(yield $response->getBody(), true);

            return array_reduce($data['partitions'], function (array $partitions, array $partition) {
                $partitions[] = TopicPartition::fromArray($partition);

                return $partitions;
            }, []);
        });
    }

    public function poll(?int $timeout = null, ?int $maxBytes = null): Promise
    {
        return call(function () use ($timeout, $maxBytes) {
            $request = (new Request($this->baseUri()->records()->withParameters(array_filter([
                'timeout' => $timeout,
                'max_bytes' => $maxBytes,
            ]))->get(), 'GET'))
                ->withHeader('Accept', $this->config->contentTypeHeader())
                ->withBody(json_encode([
                    'timeout' => $timeout,
                ]));

            /** @var Response $response */
            $response = yield $this->client->request($request);

            if (200 !== $response->getStatus()) {
                throw KafkaRestException::fromJson(yield $response->getBody());
            }

            return array_map(function ($record) {
                return ConsumerRecord::fromArray($record);
            }, json_decode(yield $response->getBody(), true));
        });
    }

    public function commit(?\SplObjectStorage $offsets = null): Promise
    {
        return call(function () use ($offsets) {
            if ($this->config->autoCommitEnable()) {
                return $this;
            }

            $request = (new Request($this->baseUri()->offsets()->get(), 'POST'))
                ->withHeader('Content-Type', $this->config->contentTypeHeader());

            if (null !== $offsets) {
                $body = [];
                /** @var TopicPartition $topicPartion */
                foreach($offsets as $topicPartion) {
                    $body['offsets'][] = array_merge($topicPartion->jsonSerialize(), ['offset' => $offsets[$topicPartion]]);
                }

                $request = $request->withBody(json_encode($body));
            }

            /** @var Response $response */
            $response = yield $this->client->request($request);

            if (200 !== $response->getStatus()) {
                throw KafkaRestException::fromJson(yield $response->getBody());
            }

            return $this;
        });
    }

    public function committed(array $partitions): Promise
    {
        return call(function () use ($partitions) {
            $request = (new Request($this->baseUri()->offsets()->get(), 'GET'))
                ->withHeader('Accept', $this->config->contentTypeHeader())
                ->withBody(json_encode([
                    'partitions' => $partitions,
                ]));

            /** @var Response $response */
            $response = yield $this->client->request($request);

            if (200 !== $response->getStatus()) {
                throw KafkaRestException::fromJson(yield $response->getBody());
            }

            $data = json_decode(yield $response->getBody(), true);

            return array_reduce($data['offsets'], function (\SplObjectStorage $offsets, array $offset) {
                $offsets->attach(TopicPartition::fromArray($offset), OffsetAndMetadata::fromArray($offset));

                return $offsets;
            }, new \SplObjectStorage());
        });
    }

    private function baseUri(): UrlBuilder
    {
        return $this->urlBuilder
            ->baseUrl($this->config->url())
            ->consumers($this->config->groupId())
            ->instances($this->instanceId);
    }
}