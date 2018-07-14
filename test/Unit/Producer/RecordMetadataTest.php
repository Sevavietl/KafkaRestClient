<?php

namespace KafkaRestClient\Test\Unit\Producer;

use KafkaRestClient\Producer\RecordMetadata;
use PHPUnit\Framework\TestCase;

final class RecordMetadataTest extends TestCase
{
    public function testCanBeCreatedFromArray(): void
    {
        $recordMetadata = RecordMetadata::fromArray([
            'partition' => $partition = 0,
            'offset' => $offset = 0,
            'error_code' => $errorCode = 500,
            'error' => $error = 'error',
        ]);

        $this->assertEquals($partition, $recordMetadata->partition());
        $this->assertEquals($offset, $recordMetadata->offset());
        $this->assertEquals($errorCode, $recordMetadata->errorCode());
        $this->assertEquals($error, $recordMetadata->error());
    }
}