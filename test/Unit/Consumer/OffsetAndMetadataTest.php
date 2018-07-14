<?php

namespace KafkaRestClient\Test\Unit\Consumer;

use KafkaRestClient\Consumer\OffsetAndMetadata;
use PHPUnit\Framework\TestCase;

final class OffsetAndMetadataTest extends TestCase
{
    public function testCanBeCreatedFromArray(): void
    {
        $offsetAndMetadata = OffsetAndMetadata::fromArray([
            'offset' => $offset = 20,
            'metadata' => $metadata = 'metadata',
        ]);

        $this->assertSame($offset, $offsetAndMetadata->offset());
        $this->assertSame($metadata, $offsetAndMetadata->metadata());
    }
}