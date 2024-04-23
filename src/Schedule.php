<?php

namespace Lidmo\WP\Foundation;

use Lidmo\WP\Foundation\Contracts\Schedule as ScheduleContract;

class Schedule implements ScheduleContract
{
    protected $hook;
    protected $interval;
    protected $args;

    public function __construct($hook)
    {
        $this->hook = $hook;
    }

    public function interval($interval)
    {
        $this->interval = $interval;
        return $this;
    }

    public function daily()
    {
        return $this->interval(24 * HOUR_IN_SECONDS);
    }

    public function twiceDaily($first = 1, $second = 13)
    {
        return $this->daily()->dailyAt($first)->dailyAt($second);
    }

    public function hourly($interval = 1)
    {
        return $this->interval($interval * HOUR_IN_SECONDS);
    }

    public function dailyAt($hour, $minute = 0)
    {
        $this->args['time'] = "{$hour}:{$minute}";
        return $this;
    }

    public function weekly()
    {
        return $this->interval(7 * DAY_IN_SECONDS);
    }

    public function weeklyOn($day, $time = '00:00')
    {
        $this->dailyAt($time);
        $this->args['day'] = $day;
        return $this;
    }

    public function monthly()
    {
        return $this->interval(30 * DAY_IN_SECONDS);
    }

    public function monthlyOn($day, $time = '00:00')
    {
        return $this->weeklyOn($day, $time);
    }

    public function cron($schedule)
    {
        $this->interval(0);
        $this->args['schedule'] = $schedule;
        return $this;
    }

    public function schedule()
    {
        if (!empty($this->args['schedule'])) {
            wp_schedule_event(time(), $this->args['schedule'], $this->hook);
        } elseif (!empty($this->interval)) {
            wp_schedule_event(time(), $this->interval, $this->hook, $this->args);
        }
    }

    public function unschedule()
    {
        wp_unschedule_event(wp_next_scheduled($this->hook), $this->hook);
    }
}