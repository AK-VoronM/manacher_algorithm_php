<?php

/**
 * Адаптация алгоритма Мнакера на PHP
 * Умеет работать с мультибайтовыми кодировками
 *
 * Class PalindromeCalc
 */
class PalindromeCalc
{
    /**
     * Кэшируется массив строки
     * @var array
     */
    protected static $string_array;

    /**
     * Основная функция поиска максимального палиндрома в переданной строке
     *
     * @param $s
     * @return string
     */
    public static function search($s = "А роза упала на лапу азора") {
        $s = self::prepareString($s);
        $n = mb_strlen($s);

        $d = [];
        for ($p = 0; $p < 2; ++$p) {
            $l = 0; $r = -1;
            for ($i = 0; $i < $n; ++$i) {
                $k = ($i > $r ? 0 : min($d[$p][$l + $r - $i] - $p, $r - $i)) + 1;
                while ($i + $k - 1 < $n && $i - $k + $p >= 0 && self::getCh($s, $i + $k - 1) == self::getCh($s, $i - $k + $p)) ++$k;
                $d[$p][$i] = --$k;
                if ($i + $k > $r) {
                    $l = $i - $k + $p;
                    $r = $i + $k - 1;
                }
            }
        }
        $m1 = 0;
        $m2 = 0;
        for ($i = 1; $i < $n; ++$i) {
            if ($d[1][$m1] < $d[1][$i]) {
                $m1 = $i;
            }
            if ($d[0][$m2] < $d[0][$i]) {
                $m2 = $i;
            }
        }
        if ($d[1][$m1] > $d[0][$m2]) {
            $f_string = mb_substr($s,$m1 - $d[1][$m1] + 1, $d[1][$m1] * 2 - 1);
        } else {
            $f_string = mb_substr($s,$m2 - $d[0][$m2], $d[0][$m2] * 2);
        }
        return $f_string;
    }

    /**
     * Очищает строку от пробелов и преобразует все в нижний регистр
     *
     * @param $str
     * @return string
     */
    public static function prepareString($str) {
        $str_arr = self::strToArray(mb_strtolower($str));
        return implode("", array_filter($str_arr, function ($el) {
            if (trim($el)) {
                return trim($el);
            }
            return false;
        }));
    }

    /**
     * Возвращает символ по номеру из строки
     * Работает с мультибитовыми кодировками
     *
     * @param $str
     * @param $n
     * @return array|string
     */
    protected static function getCh($str, $n) {
        $md_key = md5($str);
        if (!array_key_exists($md_key, self::$string_array)) {
            self::$string_array[$md_key] = self::strToArray($str);
        }
        return self::$string_array[$md_key][$n];
    }

    /**
     * Преобразует строку в массив
     * Работает с мультибитовыми кодировками
     *
     * @param $str
     * @return array[]|false|string[]
     */
    protected static function strToArray($str) {
        return $str_arr = preg_split('//u', $str, null, PREG_SPLIT_NO_EMPTY);
    }
}