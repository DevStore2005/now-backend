<?php

namespace App\Utils;

class ProviderType
{
    const INDIVIDUAL = 'Individual';
    const BUSINESS = 'Business';

    public static $types = [
        self::INDIVIDUAL => 'Individual',
        self::BUSINESS => 'Business',
    ];
}
