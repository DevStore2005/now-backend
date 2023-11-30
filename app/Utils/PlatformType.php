<?php

namespace App\Utils;

class PlatformType {
    const WEB = 0;
    const ANDROID = 1;
    const IOS = 2;

    public static $types = [
        self::WEB => 'Web',
        self::ANDROID => 'Android',
        self::IOS => 'IOS',
    ];
}