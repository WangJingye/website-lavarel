<?php


namespace App\Logging;


use Monolog\Formatter\FormatterInterface;
use Monolog\Handler\LogglyHandler;

class BaseFormatter implements FormatterInterface
{
    public $logType = 'dms';

    /**
     * 自定义给定的日志实例。
     *
     * @param \Illuminate\Log\Logger $logger
     * @return void
     */
    public function __invoke($logger)
    {
        /** @var LogglyHandler $handler */
        foreach ($logger->getHandlers() as $handler) {
            $handler->setFormatter($this);
        }
    }

    public function format(array $record)
    {
        /** @var \Monolog\DateTimeImmutable $datetime */
        $datetime = $record['datetime'];
        return json_encode([
                'log_type' => $this->logType,
                'datetime' => $datetime->format(\DateTime::ISO8601),
                'content' => $record['message'],
                'ip' => getHostByName(getHostName())
            ]) . PHP_EOL;
    }

    public function formatBatch(array $records)
    {
        $str = '';
        foreach ($records as $record) {
            $str .= $this->format($record);
        }
        return $str;
    }
}