<?php

namespace Wpae\Scheduling\Interval;


class ScheduleTime
{
    private $times;

    private $monthly;
    private $timezone;

    public function __construct($times, $monthly, $timezone)
    {
        $this->times = $times;
        $this->monthly = $monthly;
        $this->timezone = $timezone;
    }

    public function getTime()
    {
        $response = array();

        foreach ($this->times as $time) {
            $response[] = array(
                'day' => $time['day'],
                'hour' => $time['hour'],
                'min' => $time['min']
            );
        }

        return $response;
    }

    public function getTimezone()
    {
        return $this->timezone;
    }
    
    public function isMonthly()
    {
        return $this->monthly;
    }
}