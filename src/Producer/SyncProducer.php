<?php

namespace KafkaRestClient\Producer;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use KafkaRestClient\DefaultUrlBuilder;
use KafkaRestClient\ProducerConfig;
use KafkaRestClient\KafkaRestException;
use KafkaRestClient\UrlBuilder;

final class SyncProducer implements Producer
{
    /** @var ProducerConfig */
    private $config;

    /** @var ClientInterface */
    private $client;

    /** @var UrlBuilder */
    private $urlBuilder;

    public function __construct(ProducerConfig $config, ?ClientInterface $client = null, ?UrlBuilder $urlBuilder = null)
    {
        $this->config = $config;
        $this->client = $client ?? new Client();
        $this->urlBuilder = $urlBuilder ?? new DefaultUrlBuilder();
    }

    /**
     * @see https://docs.confluent.io/current/kafka-rest/docs/api.html#post--topics-(string-topic_name)
     */
    public function send(ProducerRecord $record): RecordMetadata
    {
        $request = new Request(
            'POST',
            $this->urlBuilder->baseUrl($this->config->url())->topics($record->topic())->get(),
            [
                'Content-Type' => $this->config->contentTypeHeader(),
                'Accept' => $this->config->acceptHeader(),
            ],
            json_encode([
                'records' => [$record]
            ]) ?: null
        );

        $response = $this->client->send($request);

        if (200 !== $response->getStatusCode()) {
            throw KafkaRestException::fromJson((string) $response->getBody());
        }

        $data = json_decode((string) $response->getBody(), true);

        $recordMetadata = array_map(function (array $recordMetadata) {
            return RecordMetadata::fromArray($recordMetadata);
        }, $data['offsets'] ?? []);

        return array_shift($recordMetadata);
    }
}
