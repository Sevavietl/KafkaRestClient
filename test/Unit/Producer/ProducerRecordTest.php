<?php

namespace KafkaRestClient\Test\Unit\Producer;

use KafkaRestClient\Producer\ProducerRecord;
use PHPUnit\Framework\TestCase;

final class ProducerRecordTest extends TestCase
{
    public function testCanBeCreatedFromValue(): void
    {
        $producerRecord = ProducerRecord::fromValue($topic = 'topic', $value = 'value');

        $this->assertEquals($topic, $producerRecord->topic());
        $this->assertNull($producerRecord->partition());
        $this->assertNull($producerRecord->timestamp());
        $this->assertNull($producerRecord->key());
        $this->assertEquals($value, $producerRecord->value());
    }

    public function testCanBeCreatedFromKeyAndValue(): void
    {
        $producerRecord = ProducerRecord::fromKeyAndValue($topic = 'topic', $key = 'key', $value = 'value');

        $this->assertEquals($topic, $producerRecord->topic());
        $this->assertNull($producerRecord->partition());
        $this->assertNull($producerRecord->timestamp());
        $this->assertEquals($key, $producerRecord->key());
        $this->assertEquals($value, $producerRecord->value());
    }
}