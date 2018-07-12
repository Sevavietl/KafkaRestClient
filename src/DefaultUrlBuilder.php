<?php

namespace KafkaRestClient;

use Finite\Event\TransitionEvent;
use Finite\Exception\NoSuchPropertyException;
use Finite\Loader\ArrayLoader;
use Finite\State\StateInterface;
use Finite\StateMachine\StateMachine;

final class DefaultUrlBuilder implements UrlBuilder
{
    /** @var StateMachine */
    private $stateMachine;

    /**
     * @throws \Finite\Exception\ObjectException
     */
    public function __construct()
    {
        $this->stateMachine = new StateMachine(new Url());
        $this->stateMachine->getDispatcher()->addListener('finite.post_transition', function(TransitionEvent $event) {
            ['method' => $method, 'args' => $args] = $event->getProperties();

            if (null === $method) {
                throw new NoSuchPropertyException('There is no "method" property');
            }

            /** @var Url $url */
            $url = $event->getStateMachine()->getObject();

            $url->$method(...($args ?? []));
        });

        $loader = new ArrayLoader([
            'class'  => Url::class,
            'states' => $states = [
                UrlState::INITIATED => ['type' => StateInterface::TYPE_INITIAL],

                UrlState::BASE_URL_SET => ['type' => StateInterface::TYPE_NORMAL],

                UrlState::TOPICS_SET => ['type' => StateInterface::TYPE_NORMAL],
                UrlState::PARTITIONS_SET => ['type' => StateInterface::TYPE_NORMAL],

                UrlState::CONSUMERS_SET => ['type' => StateInterface::TYPE_NORMAL],
                UrlState::INSTANCES_SET => ['type' => StateInterface::TYPE_NORMAL],
                UrlState::OFFSETS_SET => ['type' => StateInterface::TYPE_NORMAL],
                UrlState::SUBSCRIPTION_SET => ['type' => StateInterface::TYPE_NORMAL],
                UrlState::ASSIGNMENTS_SET => ['type' => StateInterface::TYPE_NORMAL],
                UrlState::POSITIONS_SET => ['type' => StateInterface::TYPE_NORMAL],
                UrlState::BEGINNING_SET => ['type' => StateInterface::TYPE_NORMAL],
                UrlState::END_SET => ['type' => StateInterface::TYPE_NORMAL],
                UrlState::RECORDS_SET => ['type' => StateInterface::TYPE_NORMAL],
                UrlState::PARAMETERS_SET => ['type' => StateInterface::TYPE_NORMAL],

                UrlState::BROKERS_SET => ['type' => StateInterface::TYPE_NORMAL],
            ],
            'transitions' => [
                UrlTransition::SET_BASE_URL => ['from' => array_keys($states), 'to' => UrlState::BASE_URL_SET, 'properties' => ['method' => null, 'args' => []]],

                UrlTransition::SET_TOPICS  => ['from' => [UrlState::BASE_URL_SET], 'to' => UrlState::TOPICS_SET, 'properties' => ['method' => null, 'args' => []]],
                UrlTransition::SET_PARTITIONS  => ['from' => [UrlState::TOPICS_SET], 'to' => UrlState::PARTITIONS_SET, 'properties' => ['method' => null, 'args' => []]],

                UrlTransition::SET_CONSUMERS  => ['from' => [UrlState::BASE_URL_SET], 'to' => UrlState::CONSUMERS_SET, 'properties' => ['method' => null, 'args' => []]],
                UrlTransition::SET_INSTANCES  => ['from' => [UrlState::CONSUMERS_SET], 'to' => UrlState::INSTANCES_SET, 'properties' => ['method' => null, 'args' => []]],
                UrlTransition::SET_OFFSETS  => ['from' => [UrlState::INSTANCES_SET], 'to' => UrlState::OFFSETS_SET, 'properties' => ['method' => null, 'args' => []]],
                UrlTransition::SET_SUBSCRIPTION  => ['from' => [UrlState::INSTANCES_SET], 'to' => UrlState::SUBSCRIPTION_SET, 'properties' => ['method' => null, 'args' => []]],
                UrlTransition::SET_ASSIGNMENTS  => ['from' => [UrlState::INSTANCES_SET], 'to' => UrlState::ASSIGNMENTS_SET, 'properties' => ['method' => null, 'args' => []]],
                UrlTransition::SET_POSITIONS  => ['from' => [UrlState::INSTANCES_SET], 'to' => UrlState::POSITIONS_SET, 'properties' => ['method' => null, 'args' => []]],
                UrlTransition::SET_BEGINNING  => ['from' => [UrlState::POSITIONS_SET], 'to' => UrlState::BEGINNING_SET, 'properties' => ['method' => null, 'args' => []]],
                UrlTransition::SET_END  => ['from' => [UrlState::POSITIONS_SET], 'to' => UrlState::END_SET, 'properties' => ['method' => null, 'args' => []]],
                UrlTransition::SET_RECORDS  => ['from' => [UrlState::INSTANCES_SET], 'to' => UrlState::RECORDS_SET, 'properties' => ['method' => null, 'args' => []]],
                UrlTransition::SET_PARAMETERS  => ['from' => [UrlState::RECORDS_SET], 'to' => UrlState::PARAMETERS_SET, 'properties' => ['method' => null, 'args' => []]],

                UrlTransition::SET_BROKERS  => ['from' => [UrlState::BASE_URL_SET], 'to' => UrlState::BROKERS_SET, 'properties' => ['method' => null, 'args' => []]],
            ]
        ]);

        $loader->load($this->stateMachine);
        $this->stateMachine->initialize();
    }

    /**
     * @param string $baseUrl
     * @return DefaultUrlBuilder
     * @throws \Finite\Exception\StateException
     */
    public function baseUrl(string $baseUrl): UrlBuilder
    {
        $this->stateMachine->apply(UrlTransition::SET_BASE_URL, ['method' => __FUNCTION__, 'args' => [$baseUrl]]);

        return $this;
    }

    /**
     * @param null|string $topicName
     * @return DefaultUrlBuilder
     * @throws \Finite\Exception\StateException
     */
    public function topics(?string $topicName = null): UrlBuilder
    {
        $this->stateMachine->apply(UrlTransition::SET_TOPICS, ['method' => __FUNCTION__, 'args' => [$topicName]]);

        return $this;
    }

    /**
     * @param int|null $partitionId
     * @return DefaultUrlBuilder
     * @throws \Finite\Exception\StateException
     */
    public function partitions(?int $partitionId = null): UrlBuilder
    {
        $this->stateMachine->apply(UrlTransition::SET_PARTITIONS, ['method' => __FUNCTION__, 'args' => [$partitionId]]);

        return $this;
    }

    /**
     * @param string $groupName
     * @return DefaultUrlBuilder
     * @throws \Finite\Exception\StateException
     */
    public function consumers(string $groupName): UrlBuilder
    {
        $this->stateMachine->apply(UrlTransition::SET_CONSUMERS, ['method' => __FUNCTION__, 'args' => [$groupName]]);

        return $this;
    }

    /**
     * @param string $instance
     * @return DefaultUrlBuilder
     * @throws \Finite\Exception\StateException
     */
    public function instances(string $instance): UrlBuilder
    {
        $this->stateMachine->apply(UrlTransition::SET_INSTANCES, ['method' => __FUNCTION__, 'args' => [$instance]]);

        return $this;
    }

    /**
     * @return DefaultUrlBuilder
     * @throws \Finite\Exception\StateException
     */
    public function offsets(): UrlBuilder
    {
        $this->stateMachine->apply(UrlTransition::SET_OFFSETS, ['method' => __FUNCTION__]);

        return $this;
    }

    /**
     * @return DefaultUrlBuilder
     * @throws \Finite\Exception\StateException
     */
    public function subscription(): UrlBuilder
    {
        $this->stateMachine->apply(UrlTransition::SET_SUBSCRIPTION, ['method' => __FUNCTION__]);

        return $this;
    }

    /**
     * @return DefaultUrlBuilder
     * @throws \Finite\Exception\StateException
     */
    public function assignments(): UrlBuilder
    {
        $this->stateMachine->apply(UrlTransition::SET_ASSIGNMENTS, ['method' => __FUNCTION__]);

        return $this;
    }

    /**
     * @return DefaultUrlBuilder
     * @throws \Finite\Exception\StateException
     */
    public function positions(): UrlBuilder
    {
        $this->stateMachine->apply(UrlTransition::SET_POSITIONS, ['method' => __FUNCTION__]);

        return $this;
    }

    /**
     * @return DefaultUrlBuilder
     * @throws \Finite\Exception\StateException
     */
    public function beginning(): UrlBuilder
    {
        $this->stateMachine->apply(UrlTransition::SET_BEGINNING, ['method' => __FUNCTION__]);

        return $this;
    }

    /**
     * @return DefaultUrlBuilder
     * @throws \Finite\Exception\StateException
     */
    public function end(): UrlBuilder
    {
        $this->stateMachine->apply(UrlTransition::SET_END, ['method' => __FUNCTION__]);

        return $this;
    }

    /**
     * @return DefaultUrlBuilder
     * @throws \Finite\Exception\StateException
     */
    public function records(): UrlBuilder
    {
        $this->stateMachine->apply(UrlTransition::SET_RECORDS, ['method' => __FUNCTION__]);

        return $this;
    }

    public function withParameters(array $parameters): UrlBuilder
    {
        $this->stateMachine->apply(UrlTransition::SET_PARAMETERS, ['method' => __FUNCTION__, 'args' => [$parameters]]);

        return $this;
    }

    /**
     * @return DefaultUrlBuilder
     * @throws \Finite\Exception\StateException
     */
    public function brokers(): UrlBuilder
    {
        $this->stateMachine->apply(UrlTransition::SET_BROKERS, ['method' => __FUNCTION__]);

        return $this;
    }

    public function get(): string
    {
        /** @var Url $url */
        $url = $this->stateMachine->getObject();

        return $url->value();
    }
}