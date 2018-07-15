<?php

namespace KafkaRestClient\Consumer;

interface Consumer
{
    public function withInstanceId(string $instanceId): Consumer;

    public function instanceId(): string;

    /**
     * @see https://docs.confluent.io/current/kafka-rest/docs/api.html#post--consumers-(string-group_name)
     */
    public function create(?string $name = null);

    /**
     * @see https://docs.confluent.io/current/kafka-rest/docs/api.html#delete--consumers-(string-group_name)-instances-(string-instance)
     */
    public function delete();

    /**
     * @see https://docs.confluent.io/current/kafka-rest/docs/api.html#post--consumers-(string-group_name)-instances-(string-instance)-subscription
     */
    public function subscribe(array $topics);

    /**
     * @see https://docs.confluent.io/current/kafka-rest/docs/api.html#get--consumers-(string-group_name)-instances-(string-instance)-subscription
     */
    public function subscription();

    /**
     * @see https://docs.confluent.io/current/kafka-rest/docs/api.html#delete--consumers-(string-group_name)-instances-(string-instance)-subscription
     */
    public function unsubscribe();

    /**
     * @see https://docs.confluent.io/current/kafka-rest/docs/api.html#post--consumers-(string-group_name)-instances-(string-instance)-assignments
     */
    public function assign(array $partitions);

    /**
     * @see https://docs.confluent.io/current/kafka-rest/docs/api.html#get--consumers-(string-group_name)-instances-(string-instance)-assignments
     */
    public function assignment();

    /**
     * @see https://docs.confluent.io/current/kafka-rest/docs/api.html#get--consumers-(string-group_name)-instances-(string-instance)-records
     */
    public function poll(?int $timeout = null, ?int $maxBytes = null);

    /**
     * @see https://docs.confluent.io/current/kafka-rest/docs/api.html#post--consumers-(string-group_name)-instances-(string-instance)-offsets
     */
    public function commit(?\SplObjectStorage $offsets = null);

    /**
     * @see https://docs.confluent.io/current/kafka-rest/docs/api.html#get--consumers-(string-group_name)-instances-(string-instance)-offsets
     */
    public function committed(array $partitions);
}