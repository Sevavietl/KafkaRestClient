<?php

namespace KafkaRestClient\Test\Unit;

use Codeception\Test\Unit;
use KafkaRestClient\DefaultUrlBuilder;

final class DefaultUrlBuilderTest extends Unit
{
    /** @var DefaultUrlBuilder */
    private $urlBuilder;

    /** @var string */
    private $baseUrl;

    protected function _before()
    {
        $this->urlBuilder = new DefaultUrlBuilder();
        $this->baseUrl = 'http://base_url';
        $this->urlBuilder = $this->urlBuilder->baseUrl($this->baseUrl);
    }

    public function test_it_builds_url_for_topics(): void
    {
        $this->assertEquals('http://base_url/topics', $this->urlBuilder->topics()->get());
    }

    public function test_it_builds_url_for_topic(): void
    {
        $this->assertEquals('http://base_url/topics/my_topic', $this->urlBuilder->topics('my_topic')->get());
    }

    public function test_it_builds_url_for_partitions(): void
    {
        $this->assertEquals(
            'http://base_url/topics/my_topic/partitions',
            $this->urlBuilder->topics('my_topic')->partitions()->get()
        );
    }

    public function test_it_builds_url_for_partition(): void
    {
        $this->assertEquals(
            'http://base_url/topics/my_topic/partitions/0',
            $this->urlBuilder->topics('my_topic')->partitions(0)->get()
        );
    }

    public function test_it_builds_url_for_consumers_group(): void
    {
        $this->assertEquals(
            'http://base_url/consumers/my_group',
            $this->urlBuilder->consumers('my_group')->get()
        );
    }

    public function test_it_builds_url_for_consumer_instance(): void
    {
        $this->assertEquals(
            'http://base_url/consumers/my_group/instances/my_instance',
            $this->urlBuilder->consumers('my_group')->instances('my_instance')->get()
        );
    }

    public function test_it_builds_url_for_offsets(): void
    {
        $this->assertEquals(
            'http://base_url/consumers/my_group/instances/my_instance/offsets',
            $this->urlBuilder->consumers('my_group')->instances('my_instance')->offsets()->get()
        );
    }

    public function test_it_builds_url_for_subscription(): void
    {
        $this->assertEquals(
            'http://base_url/consumers/my_group/instances/my_instance/subscription',
            $this->urlBuilder->consumers('my_group')->instances('my_instance')->subscription()->get()
        );
    }

    public function test_it_builds_url_for_assignments(): void
    {
        $this->assertEquals(
            'http://base_url/consumers/my_group/instances/my_instance/assignments',
            $this->urlBuilder->consumers('my_group')->instances('my_instance')->assignments()->get()
        );
    }

    public function test_it_builds_url_for_positions(): void
    {
        $this->assertEquals(
            'http://base_url/consumers/my_group/instances/my_instance/positions',
            $this->urlBuilder->consumers('my_group')->instances('my_instance')->positions()->get()
        );
    }

    public function test_it_builds_url_for_beginning(): void
    {
        $this->assertEquals(
            'http://base_url/consumers/my_group/instances/my_instance/positions/beginning',
            $this->urlBuilder->consumers('my_group')->instances('my_instance')->positions()->beginning()->get()
        );
    }

    public function test_it_builds_url_for_end(): void
    {
        $this->assertEquals(
            'http://base_url/consumers/my_group/instances/my_instance/positions/end',
            $this->urlBuilder->consumers('my_group')->instances('my_instance')->positions()->end()->get()
        );
    }

    public function test_it_builds_url_for_records(): void
    {
        $this->assertEquals(
            'http://base_url/consumers/my_group/instances/my_instance/records',
            $this->urlBuilder->consumers('my_group')->instances('my_instance')->records()->get()
        );
    }

    public function test_it_builds_url_with_parameters_for_records(): void
    {
        $this->assertEquals(
            'http://base_url/consumers/my_group/instances/my_instance/records?timeout=3000&max_bytes=300000',
            $this->urlBuilder->consumers('my_group')->instances('my_instance')->records()->withParameters([
                'timeout' => 3000,
                'max_bytes' => 300000,
            ])->get()
        );
    }

    public function test_it_builds_url_for_brokers(): void
    {
        $this->assertEquals(
            'http://base_url/brokers',
            $this->urlBuilder->brokers()->get()
        );
    }
}