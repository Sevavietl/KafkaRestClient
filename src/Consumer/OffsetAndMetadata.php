<?php

namespace KafkaRestClient\Consumer;

final class OffsetAndMetadata implements \JsonSerializable
{
    /** @var int */
    private $offset;

    /** @var string */
    private $metadata;

    public static function fromArray(array $data): self
    {
        return new self($data['offset'], $data['metadata']);
    }

    public function __construct(int $offset, string $metadata)
    {
        $this->offset = $offset;
        $this->metadata = $metadata;
    }

    public function offset(): int
    {
        return $this->offset;
    }

    public function metadata(): string
    {
        return $this->metadata;
    }

    public function jsonSerialize()
    {
        return [
            'offset' => $this->offset,
            'metadata' => $this->metadata,
        ];
    }
}