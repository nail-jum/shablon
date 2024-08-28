<?php

/**
 * Контроллер для управления базой данных
 */

class ControllerDb
{
    static function run($pageRequest)
    {
        // Массив сегментов адреса страницы
        $pagePathArray = $pageRequest->url['pathArray'];
        if ($pagePathArray[0] != 'db') {
            return [
                'status' => 'error',
                'data' => $pageRequest->url['path'],
                'info' => ["Неверный выбор обработчика для запроса $pagePathArray[0]"],
            ];
        }
        $requestSegment_2 = $pagePathArray[1] ?? 'info';

        switch ($requestSegment_2) {
            //----------------------------------------
            case 'info':                        // Информация о базе данных
                $contentData = Db::getDbStructure();
                $template = 'db_info.php';
                break;

            //----------------------------------------
            case 'create':                      // Создание базы данных по умолчанию
                $contentData = Db::dbCreate();
                $template = 'result_info.php';
                break;

            //----------------------------------------
            case 'selectlists':                 // Список справочников
                $filter = "\"name\" LIKE 'list%'";
                $contentData = Db::getDbStructureInfo($filter);
                /*
                $dbConfig = Db::dbConfig();
                $dbStructure = Db::getDbStructure($filter);
                if ($dbStructure['status'] == 'ok') {
                    $structure = [];
                    foreach ($dbStructure['data'] as $tableName => $tableStructure) {
                        $title = $dbConfig[$tableName]['title'];
                        $structure[$tableName] = $tableStructure;
                        $structure[$tableName]['title'] = $title;
                    }
                    $contentData = [
                        'status' => 'ok',
                        'data' => $structure,
                    ];
                } else {
                    $contentData = $dbStructure;
                }
                */
                //$template = 'db_info.php';
                $template = 'db_lists.php';
                break;

            //----------------------------------------
            default:
                $contentData = [];              // результаты, полученных от контроллера
                $template = '';                 // шаблон для вывода
        }

        return [$contentData, $template];
    }
}
