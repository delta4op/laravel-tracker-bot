<?php

namespace Delta4op\Laravel\Tracker\Enums;

enum ModelMorphKey: string
{
    case APP_REQUEST = 'APP_REQUEST';
    case CLIENT_REQUEST = 'CLIENT_REQUEST';
    case CACHE = 'CACHE';
    case DB_QUERY = 'DB_QUERY';
    case APP_LOG = 'APP_LOG';
    case CONSOLE_COMMAND = 'CONSOLE_COMMAND';
    case APP_ERROR = 'APP_ERROR';
    case COMMAND_SCHEDULE = 'COMMAND_SCHEDULE';
    case REDIS = 'REDIS';
    case MAIL = 'MAIL';
    case MODEL = 'MODEL';
    case EVENT = 'EVENT';
}
