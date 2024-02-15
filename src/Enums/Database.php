<?php

namespace Delta4op\Laravel\Tracker\Enums;

use Delta4op\Laravel\Tracker\Enums\Concerns\StringEnumHelpers;

enum Database: string
{
    use StringEnumHelpers;

    case MYSQL = 'MYSQL';
    case SQLSRV = 'SQLSRV';
    case SQLITE = 'SQLITE';
    case POSTGRESQL = 'POSTGRESQL';
    case MONGODB = 'MONGODB';
    case OTHER = 'OTHER';
}
