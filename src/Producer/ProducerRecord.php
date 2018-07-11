<?php

namespace KafkaRestClient\Producer;

final class ProducerRecord implements \JsonSerializable
{
    /** @var string */
    private $topic;

    /** @var null|int */
    private $partition;

    /** @var null|int */
    private $timestamp;

    /** @var null|mixed */
    private $key;

    /** @var mixed */
    private $value;

    public static function fromValue(string $topic, $value): self
    {
        return new self($topic, null, null, null, $value);
    }

    public static function fromKeyAndValue(string $topic, $key, $value): self
    {
        return new self($topic, null, null, $key, $value);
    }

    public function __construct(string $topic, ?int $partition, ?int $timestamp, $key, $value)
    {
        $this->topic = $topic;
        $this->partition = $partition;
        $this->timestamp = $timestamp;
        $this->key = $key;
        $this->value = $value;
    }

    public function topic(): string
    {
        return $this->topic;
    }

    public function partition(): ?int
    {
        return $this->partition;
    }

    public function timestamp(): ?int
    {
        return $this->timestamp;
    }

    public function key()
    {
        return $this->key;
    }

    public function value()
    {
        return $this->value;
    }

    public function jsonSerialize()
    {
        return array_filter([
            'partition' => $this->partition,
            'timestamp' => $this->timestamp,
            'key' => $this->key,
            'value' => json_encode($this->value)
        ]);
    }
}