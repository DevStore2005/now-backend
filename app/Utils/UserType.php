<?php

namespace App\Utils;

class UserType{
    const ADMIN='ADMIN';
    const PROVIDER='PROVIDER';
    const USER='USER';
    const RESTAURANT_OWNER = 'RESTAURANT_OWNER';
    const GROCERY_OWNER = 'GROCERY_OWNER';
    
    public static $types = [
        self::ADMIN => 'Admin',
        self::PROVIDER => 'Provider',
        self::USER => 'User',
        self::RESTAURANT_OWNER => 'Restaurant owner',
        self::GROCERY_OWNER => 'Grocery store',
    ];
}
