<?php

namespace App\Utils;

class BusinessProfileType
{
    const GROCERY = 'GROCERY';
    const RESTAURANT = 'RESTAURANT';

    public static $types = [
        self::GROCERY => 'Grocery',
        self::RESTAURANT => 'Restaurant',
    ];
}