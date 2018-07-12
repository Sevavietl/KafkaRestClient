<?php

namespace KafkaRestClient\Test\Unit\Consumer;

use Codeception\Test\Unit;
use KafkaRestClient\Consumer\OffsetAndMetadata;

final class OffsetAndMetadataTest extends Unit
{
    public function it_can_be_created_from_array(): void
    {
        $offsetAndMetadata = OffsetAndMetadata::fromArray([
            'offset' => $offset = 20,
            'metadata' => $metadata = 'metadata',
        ]);

        $this->assertSame($offset, $offsetAndMetadata->offset());
        $this->assertSame($metadata, $offsetAndMetadata->metadata());
    }
}