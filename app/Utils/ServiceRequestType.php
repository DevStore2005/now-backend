<?php

namespace App\Utils;

class ServiceRequestType
{
    const SERVICE_REQUEST = 'SERVICE_REQUEST';
    const MOVING_REQUEST = 'MOVING_REQUEST';

    public static $types = [
        self::SERVICE_REQUEST => 'Service Request',
        self::MOVING_REQUEST => 'Moving Request',
    ];
}
