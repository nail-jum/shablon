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
        $rowId = $pagePathArray[2] ?? false;

        if ($tableName == false) {
            $contentData = Db::getDbStructure();
            $template = 'db_info.php';
            return [$contentData, $template];
        }

        $action = $_GET['_action'] ?? $_POST['_action'] ?? '';
        $userKey = $_GET['_user_key'] ?? $_POST['_user_key'] ?? '';
        $refererPage = $_GET['_referer-page'] ?? $_POST['_referer-page'] ?? '';

        $table = new Table($tableName);
        switch ($action) {
            //----------------------------------------
            case 'new':                         // Создание новой записи
            case 'edit':                        // Редактирование записи
            case 'copy':                        // Копирование записи
            case 'del':                         // Запрос на удаление записи
                //$contentData = Table::editPrepare($action, $tableName, $rowId);
                $table->editPrepare($action, $rowId);
                $contentData = $table;
                $template = 'table_row_edit.php';
                break;

            //----------------------------------------
            case 'add':                         // Добавление записи
            case 'upd':                         // Обновление записи
                $contentData = Table::editSubmit($action, $tableName, $rowId);
                //$contentData = ['status' => 'ok', 'data' => $_POST,];
                // Перенаправление на страницу результатов
                header("Location: $refererPage");
                //$template = 'debug_page.php';     // для тестирования
                break;

            //----------------------------------------
            case 'delete':                      // Удаление записи
                //$contentData = Table::deletePrompt($tableName, $rowId);
                $template = 'table_row_edit.php';
                break;

            //----------------------------------------
            default:
                // вывод списка записей таблицы
                $table->getRows();          // изменить (добавить фильтры и сортировку)
                $contentData = $table;
                $template = 'table_edit.php';
                //return [ ['status' => 'ok', 'data' => $_POST,], 'debug_page.php' ];
                break;
        }

        return [$contentData, $template];
    }
}
