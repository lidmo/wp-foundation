<?php

namespace Lidmo\WP\Foundation\Contracts;

interface Schedule
{
    public function interval($interval);

    public function daily();

    public function twiceDaily($first = 1, $second = 13);

    public function hourly($interval = 1);

    public function dailyAt($hour, $minute = 0);

    public function weekly();

    public function weeklyOn($day, $time = '00:00');

    public function monthly();

    public function monthlyOn($day, $time = '00:00');

    public function cron($schedule);
}