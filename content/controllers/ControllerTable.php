<?php

/**
 * Контроллер для работы с таблицами
 */

class ControllerTable
{
    static function run($pageRequest)
    {
        // Массив сегментов адреса страницы
        $pagePathArray = $pageRequest->url['pathArray'];
        if ($pagePathArray[0] != 'table') {
            return [
                'status' => 'error',
                'data' => $pageRequest->url['path'],
                'info' => ["Неверный выбор обработчика для запроса $pagePathArray[0]"],
            ];
        }
        $tableName = $pagePathArray[1] ?? false;
        $rowId = $pagePathArray[2] ?? 0;

        if ($tableName == false) {
            $contentData = Db::getDbStructure();
            $template = 'db_info.php';
            return [$contentData, $template];
        }

        $action = $_GET['_action'] ?? '';
        $user_key = $_GET['_user_key'] ?? '';

        switch ($action) {
            //----------------------------------------
            case 'new':                         // Создание новой записи
                $contentData = Table::new($tableName);
                $template = 'table_row_edit.php';
                break;

            //----------------------------------------
            case 'edit':                        // Редактирование записи
                $contentData = Table::edit($tableName, $rowId);
                $template = 'table_row_edit.php';
                break;

            //----------------------------------------
            case 'copy':                        // Копирование записи
                $contentData = Table::copy($tableName, $rowId);
                $template = 'table_row_edit.php';
                break;

            //----------------------------------------
            case 'del':                         // Запрос на удаление записи
                $contentData = Table::deletePrompt($tableName, $rowId);
                $template = 'table_row_edit.php';
                break;

            //----------------------------------------
            case 'delete':                      // Удаление записи
                $contentData = Table::deletePrompt($tableName, $rowId);
                $template = 'table_row_edit.php';
                break;

            //----------------------------------------
            default:
                // вывод списка записей таблицы
                $contentData = Db::getTableData($tableName); // изменить (добавить фильтры и сортировку)
                $template = 'table_edit.php';
                break;
        }

        return [$contentData, $template];
    }
}
