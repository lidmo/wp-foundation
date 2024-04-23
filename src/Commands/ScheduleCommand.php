<?php

namespace Lidmo\WP\Foundation\Commands;

use Lidmo\WP\Foundation\Contracts\Schedule as ScheduleContract;
use Lidmo\WP\Foundation\Schedule;
use WP_CLI;
use WP_CLI_Command;

class ScheduleCommand extends WP_CLI_Command
{
    public function __invoke()
    {
        $schedules = wp_get_schedules();

        foreach ($schedules as $scheduleName => $schedule) {
            $hook = "lidmo_wp_schedule_{$scheduleName}";

            add_action($hook, function () use ($scheduleName) {
                do_action_ref_array("lidmo_wp_schedule_{$scheduleName}", func_get_args());
            });

            $this->schedule(new Schedule($hook), "Scheduled WordPress task '{$scheduleName}'");
        }
    }

    private function schedule(ScheduleContract $schedule, string $message): void
    {
        $schedule->schedule();
        WP_CLI::success($message);
    }
}