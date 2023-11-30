<?php

namespace App\Utils;

class WorkingStatus
{
    const STARTED = 'STARTED';
    const PAUSED = 'PAUSED';
    const ENDED = 'ENDED';

    public static $types = [
        self::STARTED => 'Started',
        self::PAUSED => 'Paused',
        self::ENDED => 'Ended',
    ];
}
