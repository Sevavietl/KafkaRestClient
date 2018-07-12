<?php

namespace KafkaRestClient\Test\Unit;

use Codeception\Test\Unit;
use KafkaRestClient\TopicPartition;

final class TopicPartitionTest extends Unit
{
    public function it_can_be_created_from_array(): void
    {
        $topicPartition = TopicPartition::fromArray([
            'topic' => $topic = 'topic',
            'partition' => $partition = 'partition',
        ]);

        $this->assertSame($topic, $topicPartition->topic());
        $this->assertSame($partition, $topicPartition->partition());
    }
}