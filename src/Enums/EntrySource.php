<?php

namespace Delta4op\Laravel\TrackerBot\Enums;

enum EntrySource: string
{
    case CMS_API = 'CMS_API';
    case BE_API = 'BE_API';
}
