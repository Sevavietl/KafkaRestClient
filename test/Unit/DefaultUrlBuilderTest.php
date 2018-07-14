<?php

namespace KafkaRestClient\Test\Unit;

use KafkaRestClient\DefaultUrlBuilder;
use PHPUnit\Framework\TestCase;

final class DefaultUrlBuilderTest extends TestCase
{
    /** @var DefaultUrlBuilder */
    private $urlBuilder;

    /** @var string */
    private $baseUrl;

    protected function setUp(): void
    {
        $this->urlBuilder = new DefaultUrlBuilder();
        $this->baseUrl = 'http://base_url';
        $this->urlBuilder = $this->urlBuilder->baseUrl($this->baseUrl);
    }

    public function testBuildsUrlForTopics(): void
    {
        $this->assertEquals('http://base_url/topics', $this->urlBuilder->topics()->get());
    }

    public function testBuildsUrlForTopic(): void
    {
        $this->assertEquals('http://base_url/topics/my_topic', $this->urlBuilder->topics('my_topic')->get());
    }

    public function testBuildsUrlForPartitions(): void
    {
        $this->assertEquals(
            'http://base_url/topics/my_topic/partitions',
            $this->urlBuilder->topics('my_topic')->partitions()->get()
        );
    }

    public function testBuildsUrlForPartition(): void
    {
        $this->assertEquals(
            'http://base_url/topics/my_topic/partitions/0',
            $this->urlBuilder->topics('my_topic')->partitions(0)->get()
        );
    }

    public function testBuildsUrlForConsumersGroup(): void
    {
        $this->assertEquals(
            'http://base_url/consumers/my_group',
            $this->urlBuilder->consumers('my_group')->get()
        );
    }

    public function testBuildsUrlForConsumerInstance(): void
    {
        $this->assertEquals(
            'http://base_url/consumers/my_group/instances/my_instance',
            $this->urlBuilder->consumers('my_group')->instances('my_instance')->get()
        );
    }

    public function testBuildsUrlForOffsets(): void
    {
        $this->assertEquals(
            'http://base_url/consumers/my_group/instances/my_instance/offsets',
            $this->urlBuilder->consumers('my_group')->instances('my_instance')->offsets()->get()
        );
    }

    public function testBuildsUrlForSubscription(): void
    {
        $this->assertEquals(
            'http://base_url/consumers/my_group/instances/my_instance/subscription',
            $this->urlBuilder->consumers('my_group')->instances('my_instance')->subscription()->get()
        );
    }

    public function testBuildsUrlForAssignments(): void
    {
        $this->assertEquals(
            'http://base_url/consumers/my_group/instances/my_instance/assignments',
            $this->urlBuilder->consumers('my_group')->instances('my_instance')->assignments()->get()
        );
    }

    public function testBuildsUrlForPositions(): void
    {
        $this->assertEquals(
            'http://base_url/consumers/my_group/instances/my_instance/positions',
            $this->urlBuilder->consumers('my_group')->instances('my_instance')->positions()->get()
        );
    }

    public function testBuildsUrlForBeginning(): void
    {
        $this->assertEquals(
            'http://base_url/consumers/my_group/instances/my_instance/positions/beginning',
            $this->urlBuilder->consumers('my_group')->instances('my_instance')->positions()->beginning()->get()
        );
    }

    public function testBuildsUrlForEnd(): void
    {
        $this->assertEquals(
            'http://base_url/consumers/my_group/instances/my_instance/positions/end',
            $this->urlBuilder->consumers('my_group')->instances('my_instance')->positions()->end()->get()
        );
    }

    public function testBuildsUrlForRecords(): void
    {
        $this->assertEquals(
            'http://base_url/consumers/my_group/instances/my_instance/records',
            $this->urlBuilder->consumers('my_group')->instances('my_instance')->records()->get()
        );
    }

    public function testBuildsUrlWithParametersForRecords(): void
    {
        $this->assertEquals(
            'http://base_url/consumers/my_group/instances/my_instance/records?timeout=3000&max_bytes=300000',
            $this->urlBuilder->consumers('my_group')->instances('my_instance')->records()->withParameters([
                'timeout' => 3000,
                'max_bytes' => 300000,
            ])->get()
        );
    }

    public function testBuildsUrlForBrokers(): void
    {
        $this->assertEquals(
            'http://base_url/brokers',
            $this->urlBuilder->brokers()->get()
        );
    }
}