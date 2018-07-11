<?php

namespace KafkaRestClient;

interface UrlBuilder
{
    public function baseUrl(string $baseUrl): UrlBuilder;

    /**
     * @see https://docs.confluent.io/current/kafka-rest/docs/api.html#get--topics
     * @see https://docs.confluent.io/current/kafka-rest/docs/api.html#get--topics-(string-topic_name)
     * @see https://docs.confluent.io/current/kafka-rest/docs/api.html#post--topics-(string-topic_name)
     */
    public function topics(?string $topicName = null): UrlBuilder;

    /**
     * @see https://docs.confluent.io/current/kafka-rest/docs/api.html#get--topics-(string-topic_name)-partitions
     * @see https://docs.confluent.io/current/kafka-rest/docs/api.html#get--topics-(string-topic_name)-partitions-(int-partition_id)
     * @see https://docs.confluent.io/current/kafka-rest/docs/api.html#post--topics-(string-topic_name)-partitions-(int-partition_id)
     */
    public function partitions(?int $partitionId = null): UrlBuilder;

    /**
     * @see https://docs.confluent.io/current/kafka-rest/docs/api.html#post--consumers-(string-group_name)
     */
    public function consumers(string $groupName): UrlBuilder;

    /**
     * @see https://docs.confluent.io/current/kafka-rest/docs/api.html#delete--consumers-(string-group_name)-instances-(string-instance)
     */
    public function instances(string $instance): UrlBuilder;

    /**
     * @see https://docs.confluent.io/current/kafka-rest/docs/api.html#post--consumers-(string-group_name)-instances-(string-instance)-offsets
     * @see https://docs.confluent.io/current/kafka-rest/docs/api.html#get--consumers-(string-group_name)-instances-(string-instance)-offsets
     */
    public function offsets(): UrlBuilder;

    /**
     * @see https://docs.confluent.io/current/kafka-rest/docs/api.html#post--consumers-(string-group_name)-instances-(string-instance)-subscription
     * @see https://docs.confluent.io/current/kafka-rest/docs/api.html#get--consumers-(string-group_name)-instances-(string-instance)-subscription
     * @see https://docs.confluent.io/current/kafka-rest/docs/api.html#delete--consumers-(string-group_name)-instances-(string-instance)-subscription
     */
    public function subscription(): UrlBuilder;

    /**
     * @see https://docs.confluent.io/current/kafka-rest/docs/api.html#post--consumers-(string-group_name)-instances-(string-instance)-assignments
     * @see https://docs.confluent.io/current/kafka-rest/docs/api.html#get--consumers-(string-group_name)-instances-(string-instance)-assignments
     */
    public function assignments(): UrlBuilder;

    /**
     * @see https://docs.confluent.io/current/kafka-rest/docs/api.html#post--consumers-(string-group_name)-instances-(string-instance)-positions
     */
    public function positions(): UrlBuilder;

    /**
     * @see https://docs.confluent.io/current/kafka-rest/docs/api.html#post--consumers-(string-group_name)-instances-(string-instance)-positions-beginning
     */
    public function beginning(): UrlBuilder;

    /**
     * @see https://docs.confluent.io/current/kafka-rest/docs/api.html#post--consumers-(string-group_name)-instances-(string-instance)-positions-end
     */
    public function end(): UrlBuilder;

    /**
     * @see https://docs.confluent.io/current/kafka-rest/docs/api.html#get--consumers-(string-group_name)-instances-(string-instance)-records
     */
    public function records(): UrlBuilder;

    /**
     * @see https://docs.confluent.io/current/kafka-rest/docs/api.html#get--brokers
     */
    public function brokers(): UrlBuilder;

    public function get(): string;
}