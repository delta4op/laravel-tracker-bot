<?php

namespace Delta4op\Laravel\Tracker\Enums;

enum AppEntryType: string
{
    case REQUEST = 'REQUEST';
    case CLIENT_REQUEST = 'CLIENT_REQUEST';
    case CACHE_EVENT = 'CACHE_EVENT';
    case DB_QUERY = 'DB_QUERY';
    case LOG = 'APP_LOG';
    case CONSOLE_COMMAND = 'CONSOLE_COMMAND';
    case ERROR = 'ERROR';
    case CONSOLE_SCHEDULE = 'COMMAND_SCHEDULE';
    case EVENT = 'EVENT';
    case REDIS_EVENT = 'REDIS_EVENT';
    case MAIL = 'MAIL';
    case MODEL = 'MODEL';
    case DUMP = 'DUMP';
}
