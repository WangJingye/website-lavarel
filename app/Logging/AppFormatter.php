<?php

namespace App\Logging;

class AppFormatter extends BaseFormatter
{
    public $logType = 'dms-app';

    public function format(array $record)
    {
        /** @var \Monolog\DateTimeImmutable $datetime */
        $datetime = $record['datetime'];
        return json_encode([
                'log_type' => $this->logType,
                'uri' => $record['context']['uri'],
                'datetime' => $datetime->format(\DateTime::ISO8601),
                'token' => $record['context']['token'],
                'content' => $record['message'] ? json_decode($record['message'], true) : [],
                'ip' => getHostByName(getHostName())
            ]) . PHP_EOL;
    }
}
