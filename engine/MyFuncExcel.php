<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

/**
 * Класс для работы с электронными таблицами
 * Методы:
 * readExcelFromPost     - чтение файла Excel, полученного через веб-форму, возвращает массив
 * excelFileToArray      - чтение файла Excel, возвращает массив
 * excelFileToArraySpout - чтение файла Excel (вариант 2), возвращает массив
 * arrayToExcelFile      - запись двумерного массива в файл Excel
 * assocArrayToExcel     - запись одномерного ассоциативного массива в файл Excel
 */

class MyFuncExcel
{
    /**
     * чтение файла Excel, полученного через веб-форму
     * возвращает массив с данными активной страницы
     */
    static function readExcelFromPost($inputName)
    {
        // Типы файлов электронных таблиц
        $file_mimes = [
            'text/x-comma-separated-values',
            'text/comma-separated-values',
            'application/octet-stream',
            'application/vnd.ms-excel',
            'application/x-csv',
            'text/x-csv',
            'text/csv',
            'application/csv',
            'application/excel',
            'application/vnd.msexcel',
            'text/plain',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ];

        if (isset($_FILES[$inputName]['name']) && in_array($_FILES[$inputName]['type'], $file_mimes)) {
            $extension = pathinfo($_FILES[$inputName]['name'], PATHINFO_EXTENSION);
            if ($extension == 'csv') {
                $reader = new PhpOffice\PhpSpreadsheet\Reader\Csv();
            } elseif ($extension == 'xls') {
                $reader = new PhpOffice\PhpSpreadsheet\Reader\Xls();
            } elseif ($extension == 'xlsx') {
                $reader = new PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            } else {
                return false;
            }
            $spreadsheet = $reader->load($_FILES[$inputName]['tmp_name']);
            unset($reader);
            return $spreadsheet->getActiveSheet()->toArray('', true, false); // без форматирования
        } else {
            return false;
        }
    } // readExcelFromPost

    /**
     * чтение файла Excel по указанному пути
     * возвращает массив с данными активной страницы
     * - - - - - - - - - - - - - - - - - - - - - - -
     * При работе с большими файлами работает быстро, но требует много оперативной памяти
     */
    static function excelFileToArray($fileName)
    {
        if (!file_exists($fileName)) {
            return false;
        }
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        //$text = '';
        //$text .= 'used memory 1 = ' . number_format(memory_get_usage(false), 0, '', ' ') . PHP_EOL;
        if ('csv' == $extension) {
            $reader = new PhpOffice\PhpSpreadsheet\Reader\Csv();
        } elseif ('xls' == $extension) {
            $reader = new PhpOffice\PhpSpreadsheet\Reader\Xls();
        } elseif ('xlsx' == $extension) {
            $reader = new PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        } else {
            return false;
        }
        //$text .= 'used memory 2 = ' . number_format(memory_get_usage(false), 0, '', ' ') . PHP_EOL;
        $spreadsheet = $reader->load($fileName, $reader::READ_DATA_ONLY);
        //$text .= 'used memory 3 = ' . number_format(memory_get_usage(false), 0, '', ' ') . PHP_EOL;
        //file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/log/excelFileToArray.log', $text, FILE_APPEND);
        //unset($reader);
        return $spreadsheet->getActiveSheet()->toArray();
    }

    /**
     * чтение файла Excel (Spout)
     * возвращает массив с данными активной страницы
     * - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
     * При работе с большими файлами использует мало оперативной памяти,
     * но нагружает процессор и работает медленно, необходимо использовать set_time_limit()
     */
    static function excelFileToArraySpout($fileName)
    {
        if (!file_exists($fileName)) {
            return false;
        }
        // отключение вывода уведомлений об устаревших конструкциях
        // (библиотека использует устаревшие конструкции)
        $error_level = error_reporting();
        error_reporting($error_level & ~E_DEPRECATED);

        $reader = ReaderEntityFactory::createXLSXReader();
        $reader->open($fileName);
        $lines = [];
        $count = 0;
        $sheet = current(iterator_to_array($reader->getSheetIterator()));
        //$lines = iterator_to_array($sheet->getRowIterator());

        foreach ($sheet->getRowIterator() as $row) {
            ++$count;
            $lines[] = $row->toArray();
            if ($count > ARRAY_SIZE_LIMIT) {
                break;
            }
        }
        // восстановление режима уведомлений
        error_reporting($error_level);
        return $lines;
    }

    /**
     * чтение файла Excel (SimpleXML)
     * возвращает массив с данными активной страницы
     * НЕ ДОДЕЛАНО !!!
     */
    static function excelFileToArraySimpleXML($fileName)
    {
        if (!file_exists($fileName)) {
            return false;
        }
        $zip = new ZipArchive;

        if ($zip->open($fileName) === TRUE) {
            $sheetXML = $zip->getFromName('xl/worksheets/sheet1.xml');
            $xml = simplexml_load_string($sheetXML);
            //$json = json_encode($xml[0]);
            //$lines = json_decode($json, true);
            //print_r($data);
            $zip->close();
        } else {
            echo 'Error opening the XLSX file.';
        }
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/files/test.xml', $xml);
        //print_r($xml[0]);
        //return $xml;
        //return $lines;
    }

    /**
     * Запись двумерного массива в файл Excel
     */
    static function arrayToExcelFile($array, $fileName)
    {
        // Создание экземпляра класса электронной таблицы
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->setActiveSheetIndex(0);
        $worksheet->fromArray($array);
        // Сохранение результатов
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($fileName);
        unset($writer);
        unset($worksheet);
        unset($spreadsheet);
    }

    /**
     * Запись одномерного ассоциативного массива в файл Excel
     */
    static function assocArrayToExcel($array, $fileName)
    {
        $newArray = [];
        foreach ($array as $key => $val) {
            $newArray[] = [$key, $val];
        }
        arrayToExcelFile($newArray, $fileName);
    }
}
