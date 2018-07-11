<?php

namespace KafkaRestClient;

use Finite\StatefulInterface;

/**
 * @internal
 */
final class Url implements StatefulInterface
{
    /** @var string */
    private $state = UrlState::INITIATED;

    /** @var string */
    private $value;

    public function getFiniteState(): string
    {
        return $this->state;
    }

    public function setFiniteState($state): void
    {
        $this->state = $state;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function baseUrl(string $baseUrl): self
    {
        $this->value = $baseUrl;

        return $this;
    }

    /**
     * @see https://docs.confluent.io/current/kafka-rest/docs/api.html#get--topics
     * @see https://docs.confluent.io/current/kafka-rest/docs/api.html#get--topics-(string-topic_name)
     * @see https://docs.confluent.io/current/kafka-rest/docs/api.html#post--topics-(string-topic_name)
     */
    public function topics(?string $topicName = null): self
    {
        $this->value .= '/topics';

        if (null !== $topicName) {
            $this->value .= "/{$topicName}";
        }

        return $this;
    }

    /**
     * @see https://docs.confluent.io/current/kafka-rest/docs/api.html#get--topics-(string-topic_name)-partitions
     * @see https://docs.confluent.io/current/kafka-rest/docs/api.html#get--topics-(string-topic_name)-partitions-(int-partition_id)
     * @see https://docs.confluent.io/current/kafka-rest/docs/api.html#post--topics-(string-topic_name)-partitions-(int-partition_id)
     */
    public function partitions(?int $partitionId = null): self
    {
        $this->value .= '/partitions';

        if (null !== $partitionId) {
            $this->value .= "/$partitionId";
        }

        return $this;
    }

    /**
     * @see https://docs.confluent.io/current/kafka-rest/docs/api.html#post--consumers-(string-group_name)
     */
    public function consumers(string $groupName): self
    {
        $this->value .= "/consumers/$groupName";

        return $this;
    }

    /**
     * @see https://docs.confluent.io/current/kafka-rest/docs/api.html#delete--consumers-(string-group_name)-instances-(string-instance)
     */
    public function instances(string $instance): self
    {
        $this->value .= "/instances/$instance";

        return $this;
    }

    /**
     * @see https://docs.confluent.io/current/kafka-rest/docs/api.html#post--consumers-(string-group_name)-instances-(string-instance)-offsets
     * @see https://docs.confluent.io/current/kafka-rest/docs/api.html#get--consumers-(string-group_name)-instances-(string-instance)-offsets
     */
    public function offsets(): self
    {
        $this->value .= '/offsets';

        return $this;
    }

    /**
     * @see https://docs.confluent.io/current/kafka-rest/docs/api.html#post--consumers-(string-group_name)-instances-(string-instance)-subscription
     * @see https://docs.confluent.io/current/kafka-rest/docs/api.html#get--consumers-(string-group_name)-instances-(string-instance)-subscription
     * @see https://docs.confluent.io/current/kafka-rest/docs/api.html#delete--consumers-(string-group_name)-instances-(string-instance)-subscription
     */
    public function subscription(): self
    {
        $this->value .= '/subscription';

        return $this;
    }

    /**
     * @see https://docs.confluent.io/current/kafka-rest/docs/api.html#post--consumers-(string-group_name)-instances-(string-instance)-assignments
     * @see https://docs.confluent.io/current/kafka-rest/docs/api.html#get--consumers-(string-group_name)-instances-(string-instance)-assignments
     */
    public function assignments(): self
    {
        $this->value .= '/assignments';

        return $this;
    }

    /**
     * @see https://docs.confluent.io/current/kafka-rest/docs/api.html#post--consumers-(string-group_name)-instances-(string-instance)-positions
     */
    public function positions(): self
    {
        $this->value .= '/positions';

        return $this;
    }

    /**
     * @see https://docs.confluent.io/current/kafka-rest/docs/api.html#post--consumers-(string-group_name)-instances-(string-instance)-positions-beginning
     */
    public function beginning(): self
    {
        $this->value .= '/beginning';

        return $this;
    }

    /**
     * @see https://docs.confluent.io/current/kafka-rest/docs/api.html#post--consumers-(string-group_name)-instances-(string-instance)-positions-end
     */
    public function end(): self
    {
        $this->value .= '/end';

        return $this;
    }

    /**
     * @see https://docs.confluent.io/current/kafka-rest/docs/api.html#get--consumers-(string-group_name)-instances-(string-instance)-records
     */
    public function records(): self
    {
        $this->value .= '/records';

        return $this;
    }

    /**
     * @see https://docs.confluent.io/current/kafka-rest/docs/api.html#get--brokers
     */
    public function brokers(): self
    {
        $this->value .= '/brokers';

        return $this;
    }
}