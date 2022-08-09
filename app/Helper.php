<?php

namespace App;

class Helper
{
    public static function explodeByInteger($string)
    {
        return preg_split('~\\D~', $string, -1, PREG_SPLIT_NO_EMPTY);
    }

    public static function carbonToJalali($date, $format = 'Y-m-d H:i:s')
    {
        $result = null;

        if ($date and $date->timestamp) {
            $result = static::unixToJalali($date->timestamp, $format);
        }

        return $result;
    }

    public static function unixToJalali($unix, $format = 'Y-m-d H:i:s')
    {
        $result = null;

        if ($unix) {
            $result = Jdf::jdate($format, $unix);
        }

        return $result;
    }

    public static function jalaliToUnix($jalaliDate)
    {
        $result = null;

        $jalaliDateArray = static::explodeByInteger($jalaliDate);
        if (is_array($jalaliDateArray) and 6 == count($jalaliDateArray)) {
            $unix = Jdf::jmktime(
                $jalaliDateArray[3],
                $jalaliDateArray[4],
                $jalaliDateArray[5],
                $jalaliDateArray[1],
                $jalaliDateArray[2],
                $jalaliDateArray[0]
            );
            if ($unix) {
                $result = $unix;
            }
        }

        return $result;
    }

    public static function jalaliToGregorian($jalaliDate, $format = 'Y-m-d H:i:s')
    {
        $result = null;

        $unix = static::jalaliToUnix($jalaliDate);
        if ($unix) {
            $result = \date($format, $unix);
        }

        return $result;
    }
}
