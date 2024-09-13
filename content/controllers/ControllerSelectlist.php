<?php

/**
 * Контроллер для управления базой данных
 */

class ControllerSelectlist
{
    static function run()
    {
        global $app;
        // Массив сегментов адреса страницы
        $pagePathArray = $app->url['pathArray'];
        if ($pagePathArray[0] != 'selectlists') {
            return [
                'status' => 'error',
                'data' => $app->url['path'],
                'info' => ["Неверный выбор обработчика для запроса $pagePathArray[0]"],
            ];
        }
        $requestCategory = $pagePathArray[1] ?? 'info';

        switch ($requestCategory) {
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
                $template = 'db_lists.php';
                break;

            //----------------------------------------
            default:
                $contentData = [];              // результаты, полученных от контроллера
                $template = '';                 // шаблон для вывода
        }

        //return [$contentData, $template];
        $app->contentData = $contentData;
        $app->template = $template;
    }
}
