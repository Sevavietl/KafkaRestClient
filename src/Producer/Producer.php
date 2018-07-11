<?php

namespace KafkaRestClient\Producer;

interface Producer
{
    public function send(ProducerRecord $record);
}