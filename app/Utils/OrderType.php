<?php

namespace App\Utils;

class OrderType
{
    const CASH_ON_DELIVERY = 'CASH_ON_DELIVERY';

    public static $types = [
        self::CASH_ON_DELIVERY => 'Cash on delivery',
    ];
}
