<?php

namespace KafkaRestClient\Producer;

use Amp\Artax\Client;
use Amp\Artax\DefaultClient;
use Amp\Artax\Request;
use Amp\Artax\Response;
use function Amp\call;
use Amp\Promise;
use KafkaRestClient\DefaultUrlBuilder;
use KafkaRestClient\KafkaRestException;
use KafkaRestClient\ProducerConfig;
use KafkaRestClient\UrlBuilder;

final class AsyncProducer implements Producer
{
    /** @var ProducerConfig */
    private $config;

    /** @var Client */
    private $client;

    /** @var UrlBuilder */
    private $urlBuilder;

    public function __construct(ProducerConfig $config, ?Client $client = null, ?UrlBuilder $urlBuilder = null)
    {
        $this->config = $config;
        $this->client = $client ?? new DefaultClient();
        $this->urlBuilder = $urlBuilder ?? new DefaultUrlBuilder();
    }

    /**
     * @see https://docs.confluent.io/current/kafka-rest/docs/api.html#post--topics-(string-topic_name)
     */
    public function send(ProducerRecord $record): Promise
    {
        return call(function () use ($record) {
            $request = (new Request($this->urlBuilder->baseUrl($this->config->url())->topics($record->topic())->get(), 'POST'))
                ->withHeader('Content-Type', $this->config->contentTypeHeader())
                ->withHeader('Accept', $this->config->acceptHeader())
                ->withBody(json_encode([
                    'records' => [$record]
                ]));

            /** @var Response $response */
            $response = yield $this->client->request($request);

            if (200 !== $response->getStatus()) {
                throw KafkaRestException::fromJson(yield $response->getBody());
            }

            $data = json_decode(yield $response->getBody(), true);

            $recordMetadata = array_map(function (array $recordMetadata) {
                return RecordMetadata::fromArray($recordMetadata);
            }, $data['offsets'] ?? []);

            return array_shift($recordMetadata);
        });
    }
}