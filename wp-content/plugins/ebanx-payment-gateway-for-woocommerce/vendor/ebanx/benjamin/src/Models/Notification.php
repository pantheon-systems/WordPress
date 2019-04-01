<?php

namespace Ebanx\Benjamin\Models;

class Notification
{
    private $operation;
    private $notification_type;
    private $hash_codes;

    public function __construct($operation, $notification_type, $hash_codes)
    {
        $this->operation = $operation;
        $this->notification_type = $notification_type;
        $this->hash_codes = $hash_codes;
    }

    public function getOperation()
    {
        return $this->operation;
    }

    public function getNotificationType()
    {
        return $this->notification_type;
    }

    public function getHashCodes()
    {
        return $this->hash_codes;
    }
}
