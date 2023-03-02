<?php

namespace Lidmo\WP\Foundation;

use Lidmo\WP\Foundation\Contracts\Plugin;
use Monolog\Handler\StreamHandler;
use Psr\Log\LoggerInterface;

class Logger implements LoggerInterface
{
    protected $logger;

    protected static $path = WP_CONTENT_DIR . "/lidmo-logs/";

    public function __construct(Plugin $plugin)
    {
        $name = $plugin->slug();
        $this->logger = new \Monolog\Logger($name);
        $this->logger->pushHandler(new StreamHandler(self::$path . "{$name}.log", \Monolog\Logger::DEBUG));
    }

    public function emergency($message, array $context = array()): void
    {
        $this->logger->emergency($message, $context);
    }

    public function alert($message, array $context = array()): void
    {
        $this->logger->alert($message, $context);
    }

    public function critical($message, array $context = array()): void
    {
        $this->logger->critical($message, $context);
    }

    public function error($message, array $context = array()): void
    {
        $this->logger->error($message, $context);
    }

    public function warning($message, array $context = array()): void
    {
        $this->logger->warning($message, $context);
    }

    public function notice($message, array $context = array()): void
    {
        $this->logger->notice($message, $context);
    }

    public function info($message, array $context = array()): void
    {
        $this->logger->info($message, $context);
    }

    public function debug($message, array $context = array()): void
    {
        $this->logger->debug($message, $context);
    }

    public function log($level, $message, array $context = array()): void
    {
        $this->logger->log($level, $message, $context);
    }

    private function setDir()
    {
        if (!is_dir(self::$path)) {
            wp_mkdir_p(self::$path);
        }
    }
}