<?php

namespace Wpae\Scheduling\Timezone;


class TimezoneSelect
{

    private $regions = array(
        'Africa' => \DateTimeZone::AFRICA,
        'America' => \DateTimeZone::AMERICA,
        'Antarctica' => \DateTimeZone::ANTARCTICA,
        'Asia' => \DateTimeZone::ASIA,
        'Atlantic' => \DateTimeZone::ATLANTIC,
        'Europe' => \DateTimeZone::EUROPE,
        'Indian' => \DateTimeZone::INDIAN,
        'Pacific' => \DateTimeZone::PACIFIC
    );

    public function getTimezoneSelect($value = false)
    {

        $timezones = array();
        foreach ($this->regions as $name => $mask) {
            $zones = \DateTimeZone::listIdentifiers($mask);

            foreach ($zones as $timezone) {

                $timeZoneObject = new \DateTimeZone($timezone);

                // Lets sample the time there right now
                $time = new \DateTime('now', $timeZoneObject);

                // Us dumb Americans can't handle millitary time
                $ampm = $time->format('H') > 12 ? ' (' . $time->format('g:i a') . ')' : '';
                $offset = $timeZoneObject->getOffset($time)/3600;

                if($offset < 10 && $offset > 0 && is_int($offset)) {
                    $offsetName = '0'.$offset;
                } else if($offset < 0 && $offset >-10 && is_int($offset)){
                    $offsetName = str_replace('- ','-0', $offset);
                }
                else{
                    $offsetName = str_replace('-','-', $offset);
                }

                if($offset > 0) {
                    $offsetName = "+".$offsetName;
                    $offset = "+".$offset;
                }


                $timezones[$name][$timezone]['offset'] = $offset;
                $timezones[$name][$timezone]['timezoneAbbrev'] = $time->format('T');
                // Remove region name and add a sample time
                $timezones[$name][$timezone]['name'] = 'UTC ' . $offsetName.' - '.substr($timezone, strlen($name) + 1) . ' ('.$timezones[$name][$timezone]['timezoneAbbrev'].')';
            }
        }

        $result = '';
        $result .= '<select id="timezone" name="scheduling_timezone">';
        foreach ($timezones as $region => $list) {
            $result .= '<optgroup label="' . $region . '">' . "\n";
            foreach ($list as $timezone => $data) {

                $selected = '';

                if($value) {
                    if($value == $timezone) {
                        $selected = ' selected="selected" ';
                    }
                }

                $keywords = array(
                    "UTC".$data['offset'],
                    "UTC ".$data['offset'],
                    $data['offset'],
                    str_replace("+","+ ", $data['offset']),
                    $data['timezoneAbbrev']
                );

                $keywords = implode(',', $keywords);
                $result .= '<option value="' . $timezone . '" ' . $selected . ' data-keywords="'.$keywords.'" >' . str_replace("_"," ",$data['name']) .'</option>' . "\n";
            }
            $result .= '<optgroup>' . "\n";
        }
        $result .= '</select>';

        return $result;
    }
}