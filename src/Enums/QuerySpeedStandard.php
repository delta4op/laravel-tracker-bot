<?php

namespace Delta4op\Laravel\Tracker\Enums;

enum QuerySpeedStandard: int
{
    case VERY_FAST = 1;
    case FAST = 2;
    case AVERAGE = 3;
    case SLOW = 4;
    case SLOWEST = 5;
}
