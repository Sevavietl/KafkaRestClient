<?php

namespace KafkaRestClient;

final class KafkaRestException extends \Exception
{
    public static function fromJson($json): self
    {
        $array = json_decode($json, true);

        $message = $array['message'] ?? '';
        $errorCode = $array['error_code'] ?? 0;

        return new static($message, $errorCode);
    }
}