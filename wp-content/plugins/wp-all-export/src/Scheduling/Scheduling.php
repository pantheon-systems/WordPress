<?php

namespace Wpae\Scheduling;


use Wpae\Scheduling\Interval\ScheduleTime;

class Scheduling
{
    /**
     * @var SchedulingApi
     */
    private $schedulingApi;
    /**
     * @var LicensingManager
     */
    private $licensingManager;

    public function __construct(SchedulingApi $schedulingApi, LicensingManager $licensingManager)
    {
        $this->schedulingApi = $schedulingApi;
        $this->licensingManager = $licensingManager;
    }

    public function schedule($elementId, ScheduleTime $scheduleTime, $schedulingEnabled)
    {
        $elementId = intval($elementId);

        if ($schedulingEnabled == 1) {
            $this->enableSchedule($elementId, $scheduleTime);
        } else {
            $this->disableSchedule($elementId);
        }
    }

    public function scheduleExists($elementId)
    {
        $response = $this->schedulingApi->getSchedules($elementId, Config::TYPE);

        return count($response);
    }

    public function getSchedule($elementId)
    {
        $response = $this->schedulingApi->getSchedules($elementId, Config::TYPE);

        if (count($response)) {
            return $response[0];
        } else {
            return false;
        }
    }

    public function checkLicense()
    {
        $options = \PMXE_Plugin::getInstance()->getOption();

        if (empty($options['scheduling_license'])) {
            return false;
        }

        return $this->licensingManager->checkLicense($options['scheduling_license'], \PMXE_Plugin::getSchedulingName());
    }

    public function checkConnection()
    {
        return $this->schedulingApi->checkConnection();
    }

    public function deleteScheduleIfExists($id) {

        if(!$this->checkLicense()) {
            return true;
        }

        $schedule = $this->getSchedule($id);
        if($schedule) {
            $this->deleteSchedule($schedule->id);
        }

        return true;
    }

    /**
     * @param $post
     */
    public function handleScheduling($id, $post)
    {
        $schedulingEnabled = $post['scheduling_enable'];
   
        if ($schedulingEnabled == 1) {


            if ($post['scheduling_run_on'] == 'weekly') {
                $monthly = false;
            } else {
                $monthly = true;
            }

            if($monthly) {
                $timesArray = self::buildTimesArray($post['scheduling_monthly_days'], $post['scheduling_times']);
            } else {
                $timesArray = self::buildTimesArray($post['scheduling_weekly_days'], $post['scheduling_times']);
            }
            $this->schedule(
                $id,
                new \Wpae\Scheduling\Interval\ScheduleTime($timesArray, $monthly, $post['scheduling_timezone']),
                $schedulingEnabled
            );
        }
    }

    private function enableSchedule($elementId, ScheduleTime $scheduleTime)
    {
        $schedule = $this->getSchedule($elementId);

        if ($schedule) {
            $this->updateSchedule($schedule->id, $scheduleTime);
        } else {
            $this->createSchedule($elementId, $scheduleTime);
        }
    }

    private function deleteSchedule($elementId)
    {
        $this->schedulingApi->deleteSchedule($elementId);
    }

    private function createSchedule($elementId, ScheduleTime $scheduleTime)
    {
        $scheduleData = array(
            "scheduled_job_id" => $elementId,
            "scheduled_job_type" => Config::TYPE,
            "endpoint" => get_site_url(),
            "key" => \PMXE_Plugin::getInstance()->getOption('cron_job_key'),
            'schedule' => array(
                'monthly' => $scheduleTime->isMonthly(),
                'timezone' => $scheduleTime->getTimezone(),
                'times' => $scheduleTime->getTime()
            )
        );

        $this->schedulingApi->createSchedule($scheduleData);
    }

    private function updateSchedule($scheduleId, ScheduleTime $scheduleTime, $enabled = true)
    {

        $scheduleTime = array(
            'enabled' => $enabled,
            'schedule' => array(
                'timezone' => $scheduleTime->getTimezone(),
                'monthly' => $scheduleTime->isMonthly(),
                'times' => $scheduleTime->getTime()
            ));

        $this->schedulingApi->updateSchedule($scheduleId, $scheduleTime);
    }

    private function disableSchedule($elementId)
    {
        $this->deleteSchedule($elementId);
    }

    public static function buildTimesArray($schedulingWeeklyDays, $schedulingTimes)
    {
        $times = array();
        $days = explode(',', $schedulingWeeklyDays);
        foreach ($days as $day) {
            foreach ($schedulingTimes as $time) {

                if (!$time) {
                    break;
                }
                $timeParts = explode(':', $time); 
                $hour = $timeParts[0];
                $min = (int)$timeParts[1];

                if (strpos($time, 'pm') !== false && $hour < 12) {
                    $hour = $hour + 12;
                }

                $times[] = array(
                    'day' => $day,
                    'hour' => $hour,
                    'min' => $min
                );
            }
        }

        return $times;
    }

    /**
     * TODO: Uglier but simpler method, if this gets in the way, extract to a class
     * 
     * @return Scheduling
     */
    public static function create()
    {
        $schedulingApi = new SchedulingApi(Config::API_URL);
        $licensingManager = new LicensingManager();

        return new Scheduling($schedulingApi, $licensingManager);
    }
}