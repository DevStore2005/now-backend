<?php

namespace App\Utils;

class ProductType
{
    const GROCERY = 'GROCERY';
    const FOOD = 'FOOD';

    public static $types = [
        self::GROCERY => 'Grocery',
        self::FOOD => 'Food',
    ];
}
