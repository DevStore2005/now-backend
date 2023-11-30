<?php

namespace App\Utils;

class TransactionType
{
    const COMMISSION = 'COMMISSION';
    const BONUS = 'BONUS';
    const EARN = 'EARN';

    public static $types = [
        self::COMMISSION => 'Commission',
        self::BONUS => 'Bonus',
        self::EARN => 'Earn',
    ];
}
