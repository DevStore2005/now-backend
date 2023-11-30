<?php

namespace App\Utils;

class ServiceType
{
    const SERVICE = 'SERVICE';
    const MOVING = 'MOVING';
    const MULTIPLE = 'MULTIPLE';

    public static $types = [
        self::SERVICE => 'Service',
        self::MOVING => 'Moving',
        self::MULTIPLE => 'Multiple',
    ];
}
