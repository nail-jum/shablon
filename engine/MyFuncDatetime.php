<?php

/**
 * Класс для работы с датой и временем
 */

class MyFuncDatetime
{
    static function str2date($format, $date)
    {
        $d = DateTime::createFromFormat($format, $date);
        // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits
        // so changing the comparison from == to === fixes the issue.
        if ($d === false) {                     // ошибка преобразования в дату
            return false;
        }
        if ($d->format($format) !== $date) {    // сформированная дата не совпадает с заданной
            return false;
        }
        return $d;
        //return $d->getTimestamp();
    }

    static function date2mysql($data)
    {
        //=== Преобразование даты из обычного формата в формат MYSQL ===
        if (empty($data)) return '0000-00-00';
        $date = substr($data, 0, 10);
        $time = substr($data, 10, 6);
        $a_data = explode('.', $date);
        return $a_data[2] . '-' . $a_data[1] . '-' . $a_data[0] . $time;
    }

    static function mysql2date($data, $showtime = false)
    {
        //=== Преобразование даты из формата MYSQL в обычный формат ===
        $date = substr($data, 0, 10);
        $time = substr($data, 10, 6);
        if ($date == '0000-00-00') return '';
        if (empty($date)) return '';
        $a_data = explode('-', $date);
        return $a_data[2] . '.' . $a_data[1] . '.' . $a_data[0] . ($showtime ? $time : '');
    }
}
