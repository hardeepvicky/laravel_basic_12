<?php
<<<<<<< HEAD
/**
 * Date Utility
 * 
 * 
=======

/**
 * Date Utility
 *
 *
>>>>>>> base_v3
 * @created    08-11-2016
 * @copyright  Copyright (C) 2016
 * @license    Proprietary
 * @author     Hardeep
 * @version    1.0
 */

namespace App\Helpers;

<<<<<<< HEAD
class DateUtility 
=======
use DateTime;

class DateUtility
>>>>>>> base_v3
{
    const DATE_FORMAT = 'Y-m-d';
    const TIME_FORMAT = 'H:i:s';
    const DATETIME_FORMAT = 'Y-m-d H:i:s';

    const DATE_OUT_FORMAT = "d-M-Y";
<<<<<<< HEAD
    const TIME_OUT_FORMAT = "h:i a";
    const DATETIME_OUT_FORMAT = 'd-M-Y h:i a';
    const DATETIME_OUT_FORMAT_FILE = 'd-M-Y_h-i-s_a';
	
=======
    const TIME_OUT_FORMAT = "H:i";
    const DATETIME_OUT_FORMAT = 'd-M-Y H:i';
    const DATETIME_OUT_FORMAT_FILE = 'd-M-Y_H-i';

>>>>>>> base_v3
    const YEARS = "years";
    const MONTHS = "months";
    const WEEKS = "weeks";
    const DAYS = "days";
    const HOURS = "hours";
<<<<<<< HEAD
    const MINTUES = "minutes";    
    const SECONDS = "seconds";

    /**
     * convert time string to standered date object 
     * @param String $date
     * @param String $timezone
     * @return DateTime object
     */
    protected static function getDateObj($date = "", $timezone = NULL)
    {
        if ($date)
        {
            $timestamp = strtotime($date);

            if ($timestamp == false || $timestamp == -1)
            {
                return false;
            }

            $date = new \DateTime(date(self::DATETIME_FORMAT, $timestamp));
        }
        else
        {
            $date = new \Datetime();
        }

        if ($timezone)
        {
            $date->setTimezone(new \DateTimeZone($timezone));
=======
    const MINTUES = "minutes";
    const SECONDS = "seconds";

    public static \DateTimeZone | null $login_user_time_zone = null;

    /**
     * convert time string to standered date object
     * @param String $date
     * @param $timezone
     * @return DateTime object
     */
    public static function getDateObj($date = "", \DateTimeZone | null $timezone = NULL) : DateTime | null
    {
        if ($date) {

            if (is_int($date)) {
                $timestamp = $date;
            }
            else if (is_object($date))
            {
                return $date;
            }
            else {
                $timestamp = strtotime($date);
            }

            if ($timestamp == false || $timestamp == -1) {
                return null;
            }

            $date = new \DateTime(date(self::DATETIME_FORMAT, $timestamp), $timezone);
        } else {
            $date = new \Datetime(now());
>>>>>>> base_v3
        }

        return $date;
    }

    /**
     * Convert String Date to Specfic String date Format
     * If Date is given null then current date is formated
     * It does not change the timezone of date
<<<<<<< HEAD
     * 
     * @param String $date NULL
     * @param String $format NULL
     * @param String $timezone NULL
     * @return String date
     */
    public static function getDate($date = NULL, $format = NULL, $timezone = NULL)
    {
        $date = self::getDateObj($date, $timezone);

        if ($date == false)
        {
=======
     *
     * @param String $date NULL
     * @param String $format NULL
     * @param $timezone NULL
     * @return String date
     */
    public static function getDate($date = NULL, $format = NULL, \DateTimeZone | null $timezone = NULL)
    {
        $date = self::getDateObj($date, $timezone);

        if ($date == false) {
>>>>>>> base_v3
            return false;
        }

        $format = $format ? $format : self::DATETIME_FORMAT;

<<<<<<< HEAD
        if ($date)
        {
            return $date->format($format);
        }
        else
        {
            return $date->format($format);
        }
=======
        return $date->format($format);
>>>>>>> base_v3
    }


    /**
     * compare dates and return the diffrence between them
<<<<<<< HEAD
     * 
=======
     *
>>>>>>> base_v3
     * @param String $start_date
     * @param String $end_date
     * @param int $type
     * @return String
     */
    public static function diff($start_date, $end_date, $type = "seconds")
    {
        $from_date = self::getDateObj($start_date);
        $to_date = self::getDateObj($end_date);

<<<<<<< HEAD
        if ($from_date == false || $to_date == false)
        {
=======
        if ($from_date == false || $to_date == false) {
>>>>>>> base_v3
            return false;
        }

        $diff = $to_date->diff($from_date);
<<<<<<< HEAD
        
        $days = $diff->format('%a');
        
        $seconds = ($days * 24 * 60 * 60) + ($diff->h * 60 * 60) + ($diff->i * 60) + $diff->s;
        
        switch($type)
        {
            case self::YEARS:              
                $result = $diff->y;
            break;

            case self::MONTHS:              
                $result = ($diff->y * 12) + $diff->m;
            break;

            case self::WEEKS:              
                $result = $days / 7;
            break;

            case self::DAYS:              
                $result = $days;
            break;

            case self::HOURS:
                $result = $seconds / 3600;
            break;

            case self::MINTUES:
                $result = $seconds / 60;
            break;

            default:                
                $result = $seconds;
            break;
=======

        $days = $diff->format('%a');

        $seconds = ($days * 24 * 60 * 60) + ($diff->h * 60 * 60) + ($diff->i * 60) + $diff->s;

        switch ($type) {
            case self::YEARS:
                $result = $diff->y;
                break;

            case self::MONTHS:
                $result = ($diff->y * 12) + $diff->m;
                break;

            case self::WEEKS:
                $result = $days / 7;
                break;

            case self::DAYS:
                $result = $days;
                break;

            case self::HOURS:
                $result = $seconds / 3600;
                break;

            case self::MINTUES:
                $result = $seconds / 60;
                break;

            default:
                $result = $seconds;
                break;
>>>>>>> base_v3
        }

        $sign = $diff->format('%R');

<<<<<<< HEAD
        if ($sign == "-")
        {
=======
        if ($sign == "-") {
>>>>>>> base_v3
            $result = $sign . $result;
        }

        return $result;
    }

    /**
     * compare two dates
<<<<<<< HEAD
     * 
     * if from_date is greater than to_date then return > 0
     * if not return < 0
     * 
=======
     *
     * if from_date is greater than to_date then return > 0
     * if not return < 0
     *
>>>>>>> base_v3
     * @param type $first_date
     * @param type $second_date
     * @return int
     */
    public static function compare($first_date, $second_date)
    {
        $first_date = self::getDate($first_date, "U");
        $second_date = self::getDate($second_date, "U");

<<<<<<< HEAD
        if ($first_date == false || $second_date == false)
        {
            return false;
        }

        if ($first_date > $second_date)
        {
            return 1;
        }

        if ($first_date < $second_date)
        {
            return -1;
        }

        if ($first_date == $second_date)
        {
=======
        if ($first_date == false || $second_date == false) {
            return false;
        }

        if ($first_date > $second_date) {
            return 1;
        }

        if ($first_date < $second_date) {
            return -1;
        }

        if ($first_date == $second_date) {
>>>>>>> base_v3
            return 0;
        }
    }

    /**
     * Add/Remove days/Months/Years/Weeks/Hours/Seconds in date
<<<<<<< HEAD
     * 
=======
     *
>>>>>>> base_v3
     * @param String $date
     * @param String $duration
     * @param String $duration_type
     * @param String $format
     * @return String
     */
    public static function change($date = "", $duration = "", $duration_type = "days", $format = NULL)
    {
<<<<<<< HEAD
        if (is_numeric($duration))
        {
            $duration = round($duration);
            if ($duration > 0)
            {
                $duration = "+" . $duration;
            }
        }
        
        $f = DateUtility::DATETIME_FORMAT;
        if ( in_array($duration_type, [DateUtility::DAYS, DateUtility::WEEKS, DateUtility::MONTHS, DateUtility::YEARS]) )
        {
			if($format){
				$f = $format;
			}else{
				$f = DateUtility::DATE_FORMAT;
			}

        }
        
        $date = DateUtility::getDate($date, $f);

        $duration .= " " . $duration_type;
        
=======
        if (is_numeric($duration)) {
            $duration = round($duration);
            if ($duration > 0) {
                $duration = "+" . $duration;
            }
        }

        $f = DateUtility::DATETIME_FORMAT;
        if (in_array($duration_type, [DateUtility::DAYS, DateUtility::WEEKS, DateUtility::MONTHS, DateUtility::YEARS])) {
            if ($format) {
                $f = $format;
            }
        }

        $date = DateUtility::getDate($date, $f);

        $duration .= " " . $duration_type;

>>>>>>> base_v3
        return self::getDate($date . " " . $duration, $format);
    }

    /**
     * return day list between two dates
<<<<<<< HEAD
     * 
     * @param String $start_date
     * @param String $end_date
     * @param String $key_format {n}, 'Y-m-d', 'd'
     * @param String $format 
=======
     *
     * @param String $start_date
     * @param String $end_date
     * @param String $key_format {n}, 'Y-m-d', 'd'
     * @param String $format
>>>>>>> base_v3
     * @return type
     */
    public static function getDayListBetweenTwoDates($start_date, $end_date, $key_format = "{n}", $format = NULL, $hierarchy = FALSE)
    {
        $list = array();
<<<<<<< HEAD
        
        $diff_days = self::diff($start_date, $end_date, self::DAYS);
        
        while($diff_days <= 0)
        {
            $str = self::getDate($start_date, $format);

            $k = "";
            if ($key_format != "{n}")
            {
                $k = self::getDate($start_date, $key_format);
            }

            if ($hierarchy)
            {
                $year = self::getDate($start_date, "Y");
                $month = self::getDate($start_date, "m");

                if($k)
                {
                    $list[$year][$month][$k] = $str;
                }
                else
                {
                    $list[$year][$month][] = $str;
                }
            }
            else 
            {
                if($k)
                {
                    $list[$k] = $str;
                }
                else
                {
=======

        $diff_days = self::diff($start_date, $end_date, self::DAYS);

        while ($diff_days <= 0) {
            $str = self::getDate($start_date, $format);

            $k = "";
            if ($key_format != "{n}") {
                $k = self::getDate($start_date, $key_format);
            }

            if ($hierarchy) {
                $year = self::getDate($start_date, "Y");
                $month = self::getDate($start_date, "m");

                if ($k) {
                    $list[$year][$month][$k] = $str;
                } else {
                    $list[$year][$month][] = $str;
                }
            } else {
                if ($k) {
                    $list[$k] = $str;
                } else {
>>>>>>> base_v3
                    $list[] = $str;
                }
            }

            $start_date = self::change($start_date, 1, DateUtility::DAYS, DateUtility::DATE_FORMAT);
<<<<<<< HEAD
            
=======

>>>>>>> base_v3
            $diff_days++;
        }

        return $list;
    }
<<<<<<< HEAD
    
     /**
     * return month list between two dates
     * 
=======

    /**
     * return month list between two dates
     *
>>>>>>> base_v3
     * @param String $start_date
     * @param String $end_date
     * @return type
     */
    public static function getMonthListBetweenTwoDates($start_date, $end_date)
    {
        $list = array();

        $diff_days = self::diff($start_date, $end_date, self::DAYS);

<<<<<<< HEAD
        while ($diff_days <= 0)
        {
=======
        while ($diff_days <= 0) {
>>>>>>> base_v3
            $year = self::getDate($start_date, "Y");
            $month = self::getDate($start_date, "M");
            $m = self::getDate($start_date, "m");

            $list[$year][(int) $m] = $month;
<<<<<<< HEAD
                
=======

>>>>>>> base_v3
            $start_date = self::change($start_date, 1, DateUtility::DAYS, DateUtility::DATE_FORMAT);
            $diff_days++;
        }

        return $list;
    }
<<<<<<< HEAD
    
=======

>>>>>>> base_v3
    public static function getLastDateOfMonth($year, $month)
    {
        return date("Y-m-t", strtotime(date("$year-$month-d")));
    }
<<<<<<< HEAD
    
    public static function getTotalDaysInMonth($y, $m)
    {
        return cal_days_in_month(CAL_GREGORIAN, $m, $y); 
=======

    public static function getTotalDaysInMonth($y, $m)
    {
        return cal_days_in_month(CAL_GREGORIAN, $m, $y);
>>>>>>> base_v3
    }

    public function isDate(String $date)
    {
<<<<<<< HEAD
        if (\DateTime::createFromFormat('Y-m-d', $date) !== false)
        {
            return true;
        }
        
        if (\DateTime::createFromFormat('d-M-Y', $date) !== false)
        {
            return true;
        }
        
        if (\DateTime::createFromFormat('d-m-Y', $date) !== false)
        {
            return true;
        }
        
        return false;
    }
}
=======
        if (\DateTime::createFromFormat('Y-m-d', $date) !== false) {
            return true;
        }

        if (\DateTime::createFromFormat('d-M-Y', $date) !== false) {
            return true;
        }

        if (\DateTime::createFromFormat('d-m-Y', $date) !== false) {
            return true;
        }

        return false;
    }

    public static function getCelecDate($date, $timezone = null)
    {
        $dt = self::getDateObj($date, $timezone);

        $ret_array['date'] = $dt->format(self::DATE_OUT_FORMAT);
        $ret_array['time'] = $dt->format(self::TIME_OUT_FORMAT);
        $ret_array['day'] = $dt->format('D');

        return $ret_array;
    }
}
>>>>>>> base_v3
