<?php

namespace KafkaRestClient\Test\Unit\Producer;

use Codeception\Test\Unit;
use KafkaRestClient\Producer\RecordMetadata;

final class RecordMetadataTest extends Unit
{
    public function test_it_can_be_created_from_array(): void
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