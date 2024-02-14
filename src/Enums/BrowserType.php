<?php

namespace Delta4op\Laravel\Tracker\Enums;

enum BrowserType: string
{
    case CHROME = 'CHROME';
    case FIREFOX = 'FIREFOX';
    case OPERA = 'OPERA';
    case SAFARI = 'SAFARI';
    case INTERNET_EXPLORER = 'INTERNET_EXPLORER';
    case EDGE = 'EDGE';
    case IN_APP = 'IN_APP';
}
