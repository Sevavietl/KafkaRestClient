<?php

namespace KafkaRestClient\Test\Unit\Consumer;

use Codeception\Test\Unit;
use KafkaRestClient\Consumer\ConsumerRecord;

final class ConsumerRecordTest extends Unit
{
    public function test_it_can_be_created_from_array(): void
    {
        $consumerRecord = ConsumerRecord::fromArray([
            'topic' => $topic = 'topic',
            'key' => null,
            'value' => $value = 'value',
            'partition' => $partition = 0,
            'offset' => $offset = 0,
        ]);

        $this->assertSame($topic, $consumerRecord->topic());
        $this->assertNull($consumerRecord->key());
        $this->assertSame($value, $consumerRecord->value());
        $this->assertSame($partition, $consumerRecord->partition());
        $this->assertSame($offset, $consumerRecord->offset());
    }
}