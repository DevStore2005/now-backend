<?php

namespace App\Utils;

class PageType
{
    const Terms = 1;
    const Privacy = 2;

    public static $types = [
        self::Terms => 'Terms',
        self::Privacy => 'Privacy',
    ];
}
