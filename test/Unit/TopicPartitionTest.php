<?php

namespace KafkaRestClient\Test\Unit;

use KafkaRestClient\TopicPartition;
use PHPUnit\Framework\TestCase;

final class TopicPartitionTest extends TestCase
{
    public function testCanBeCreatedFromArray(): void
    {
        $topicPartition = TopicPartition::fromArray([
            'topic' => $topic = 'topic',
            'partition' => $partition = 0,
        ]);

        $this->assertSame($topic, $topicPartition->topic());
        $this->assertSame($partition, $topicPartition->partition());
    }
}