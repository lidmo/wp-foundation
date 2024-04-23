<?php

namespace Lidmo\WP\Foundation;

use Lidmo\WP\Foundation\Commands\ScheduleCommand;
use WP_CLI;
use WP_CLI_Command;

class Console extends WP_CLI_Command
{
    /**
     * Register the commands for the application.
     */
    public static function registerCommands(): void
    {
        WP_CLI::add_command('lidmo schedule', ScheduleCommand::class);
        // Adicione mais comandos aqui, se necessário
    }
}