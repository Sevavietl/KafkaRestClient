<?php

namespace KafkaRestClient;


interface Config
{
    public function url(): string;

    public function embeddedFormat(): EmbeddedFormat;

    public function apiVersion(): ApiVersion;

    public function serializationFormat(): SerializationFormat;

    public function contentTypeHeader(): string;

    public function acceptHeader(): string;
}