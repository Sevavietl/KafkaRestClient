<?php

namespace KafkaRestClient\Consumer;

final class ConsumerRecord
{
    /** @var string */
    private $topic;

    /** @var null|string */
    private $key;

    /** @var mixed */
    private $value;

    /** @var int */
    private $partition;

    /** @var int */
    private $offset;

    public static function fromArray(array $data): self
    {
        return new self(
            $data['topic'],
            $data['key'],
            $data['value'],
            $data['partition'],
            $data['offset']
        );
    }

    public function __construct(string $topic, ?string $key, $value, int $partition, int $offset)
    {
        $this->topic = $topic;
        $this->key = $key;
        $this->value = $value;
        $this->partition = $partition;
        $this->offset = $offset;
    }

    public function topic(): string
    {
        return $this->topic;
    }

    public function key(): ?string
    {
        return $this->key;
    }

    public function value()
    {
        return $this->value;
    }

    public function partition(): int
    {
        return $this->partition;
    }

    public function offset(): int
    {
        return $this->offset;
    }
}