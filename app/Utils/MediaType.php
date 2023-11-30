<?php

namespace App\Utils;

class MediaType
{
    const IMAGE = 1;
    const FILE = 2;
    const VIDEO = 3;

    public static $types = [
        self::IMAGE => 'image',
        self::FILE => 'file',
        self::VIDEO => 'video',
    ];
}
