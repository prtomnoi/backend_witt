<?php

namespace App\Helpers;

class ThaiBahtHelper
{
    public static function bahtText($number)
    {
        $number = number_format($number, 2, ".", "");
        list($integer, $fraction) = explode(".", $number);

        $bahtText = self::convert($integer) . "บาท";
        $bahtText .= ($fraction == "00") ? "ถ้วน" : self::convert($fraction) . "สตางค์";

        return $bahtText;
    }

    protected static function convert($number)
    {
        $txtnum1 = ["", "หนึ่ง", "สอง", "สาม", "สี่", "ห้า", "หก", "เจ็ด", "แปด", "เก้า"];
        $txtnum2 = ["", "สิบ", "ร้อย", "พัน", "หมื่น", "แสน", "ล้าน"];
        $number = str_replace(",", "", $number);
        $number = (string)$number;

        $convert = "";
        $len = strlen($number);

        for ($i = 0; $i < $len; $i++) {
            $n = $number[$i];

            if ($n != 0) {
                if ($i == ($len - 1) && $n == 1 && $len > 1) {
                    $convert .= "เอ็ด";
                } elseif ($i == ($len - 2) && $n == 2) {
                    $convert .= "ยี่";
                } elseif ($i == ($len - 2) && $n == 1) {
                    $convert .= "";
                } else {
                    $convert .= $txtnum1[$n];
                }
                $convert .= $txtnum2[$len - $i - 1];
            }
        }

        return $convert;
    }
}
