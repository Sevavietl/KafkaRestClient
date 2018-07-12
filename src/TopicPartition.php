<?php

namespace KafkaRestClient;

final class TopicPartition implements \JsonSerializable
{
    /** @var string */
    private $topic;

    /** @var int */
    private $partition;

    public static function fromArray(array $data): self
    {
        return new self($data['topic'], $data['partition']);
    }

    public function __construct(string $topic, int $partition)
    {
        $this->topic = $topic;
        $this->partition = $partition;
    }

    public function topic(): string
    {
        return $this->topic;
    }

    public function partition(): int
    {
        return $this->partition;
    }

    public function jsonSerialize()
    {
        return [
            'topic' => $this->topic,
            'partition' => $this->partition,
        ];
    }
}