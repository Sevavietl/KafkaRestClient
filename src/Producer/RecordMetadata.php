<?php

namespace KafkaRestClient\Producer;

final class RecordMetadata
{
    /** @var int */
    private $partition;

    /** @var int */
    private $offset;

    /** @var null|int */
    private $errorCode;

    /** @var null|string */
    private $error;

    public static function fromArray($data): self
    {
        return new self(
            $data['partition'],
            $data['offset'],
            $data['error_code'],
            $data['error']
        );
    }

    public function __construct(int $partition, int $offset, ?int $errorCode, ?string $error)
    {
        $this->partition = $partition;
        $this->offset = $offset;
        $this->errorCode = $errorCode;
        $this->error = $error;
    }

    public function partition(): int
    {
        return $this->partition;
    }

    public function offset(): int
    {
        return $this->offset;
    }

    public function errorCode(): ?int
    {
        return $this->errorCode;
    }

    public function error(): ?string
    {
        return $this->error;
    }
}