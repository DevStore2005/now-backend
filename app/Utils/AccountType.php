<?php

namespace App\Utils;

class AccountType
{
    const BASIC = 'BASIC';
    const PREMIUM = 'PREMIUM';

    public static $types = [
        self::BASIC => 'Basic',
        self::PREMIUM => 'Premium',
    ];
}
