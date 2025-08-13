<?php

namespace App\Infra\Logger;

use Monolog\Logger as MonologLogger;
use Monolog\Handler\StreamHandler;

class Logger
{
    public static function createLogger()
    {
        $log = new MonologLogger('app');
        $log->pushHandler(new StreamHandler(__DIR__ . '/../../../storage/logs/app.log', MonologLogger::DEBUG));
        return $log;
    }
}
