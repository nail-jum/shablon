<?php

/**
 * Контроллер для работы с таблицами
 * - - - - - - - - - - - - - - - -
 * Получаемый запрос:
 * <имя домена>/table/<имя таблицы>/<id записи>
 * - - - - - - - - - - - - - - - -
 * Алгоритм работы контроллера
 * 1) Если не указано имя таблицы, выводится список стаблиц
 * 2) Если не указан id записи, выводится список записей таблицы
 * 3) Если id указан (в т.ч. ноль), действие зависит от действия:
 *  3.1)
 * - - - - - - - - - - - - - - - -
 *
 */

class ControllerTable
{
    static function run()
    {
        global $app;
        // Массив сегментов адреса страницы
        $pagePathArray = $app->url['pathArray'];
        $tableName  = $pagePathArray[1] ?? false;
        $rowId      = $pagePathArray[2] ?? false;

        // Таблица не указана
        if ($tableName == false) {
            $contentData = Db::getDbStructure();    // getTableList()
            $template = 'db_info.php';
            return [$contentData, $template];
        }

        $action      = $_GET['_action']       ?? $_POST['_action'] ?? '';
        //$userKey     = $_GET['user-token']    ?? $_POST['user-token'] ?? '';
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
                //header("Location: $refererPage");
                //$template = 'debug_page.php';     // для тестирования
                $template = 'app_debug.php';     // для тестирования
                break;

            //----------------------------------------
            case 'delete':                      // Удаление записи
                //$contentData = Table::deletePrompt($tableName, $rowId);
                $template = 'table_row_edit.php';
                break;

            //----------------------------------------
            default:
                // вывод списка записей таблицы
                $table->getRowCount();
                $table->getRows();          // изменить (добавить фильтры и сортировку)
                $contentData = $table;
                $template = 'table_edit.php';
                //return [ ['status' => 'ok', 'data' => $_POST,], 'debug_page.php' ];
                break;
        }

        return [$contentData, $template];
    }
}
