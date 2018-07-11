<?php

namespace KafkaRestClient\Test\Unit\Producer;

use Codeception\Test\Unit;
use KafkaRestClient\Producer\ProducerRecord;

final class ProducerRecordTest extends Unit
{
    public function test_it_can_be_created_from_value(): void
    {
        $producerRecord = ProducerRecord::fromValue($topic = 'topic', $value = 'value');

        $this->assertEquals($topic, $producerRecord->topic());
        $this->assertNull($producerRecord->partition());
        $this->assertNull($producerRecord->timestamp());
        $this->assertNull($producerRecord->key());
        $this->assertEquals($value, $producerRecord->value());
    }

    public function test_it_can_be_created_from_key_and_value(): void
    {
        $producerRecord = ProducerRecord::fromKeyAndValue($topic = 'topic', $key = 'key', $value = 'value');

        $this->assertEquals($topic, $producerRecord->topic());
        $this->assertNull($producerRecord->partition());
        $this->assertNull($producerRecord->timestamp());
        $this->assertEquals($key, $producerRecord->key());
        $this->assertEquals($value, $producerRecord->value());
    }
}