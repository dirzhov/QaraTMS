<?php

namespace App\Classes;

class Hash
{
    public static function khash($data)
    {
//        static $map = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
//        $hash = bcadd(sprintf('%u',crc32($data)) , 0x100000000);
        static $map = "0123456789abcdef";
        $hash = bcadd(sprintf('%u',crc32($data)) , 0x10000000);
        $str = "";
        do
        {
//            $str = $map[bcmod($hash, 62) ] . $str;
//            $hash = bcdiv($hash, 62);
            $str = $map[bcmod($hash, 16)] . $str;
            $hash = bcdiv($hash, 16);
        }
        while ($hash >= 1);
        return $str;
    }

}
