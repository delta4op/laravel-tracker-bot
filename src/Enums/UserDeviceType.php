<?php

namespace Delta4op\Laravel\Tracker\Enums;

enum UserDeviceType: string
{
    case MOBILE = 'MOBILE';
    case TABLET = 'TABLET';
    case DESKTOP = 'DESKTOP';
    case BOT = 'BOT';
}
