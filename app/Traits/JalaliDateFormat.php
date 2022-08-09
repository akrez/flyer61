<?php

namespace App\Traits;

use App\Helper;

trait JalaliDateFormat
{
    public function jalaliCreatedAt($format = 'Y-m-d H:i:s')
    {
        return Helper::carbonToJalali($this->created_at, $format);
    }

    public function jalaliUpdatedAt($format = 'Y-m-d H:i:s')
    {
        return Helper::carbonToJalali($this->updated_at, $format);
    }
}
