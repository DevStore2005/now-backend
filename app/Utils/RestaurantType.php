<?php

namespace App\Utils;

class RestaurantType
{
    const VEG = 'VEG';
    const NON_VEG = 'NON_VEG';
    const ALL = 'ALL';

    public static $types = [
        self::VEG => 'Veg',
        self::NON_VEG => 'Non Veg',
        self::ALL => 'All',
    ];
}
