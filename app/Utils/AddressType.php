<?php

namespace App\Utils;

class AddressType
{
    const HOME = "HOME";
    const OFFILE = "OFFICE";
    const OTHER = 'OTHER';

    public static $types = [
        self::HOME => 'Home',
        self::OFFILE => 'Office',
        self::OTHER => 'Other',
    ];
}
