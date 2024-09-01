<?php

/**
 * Класс для работы с таблицами базы данных
 */

class Table
{
    static function new($tableName)
    {
        // Параметры столбцов таблицы для вывода на экран
        $columnsInfo = Db::tableColumnsInfo($tableName);
        return [
            'status' => 'ok',
            'data' => [
                'name' => $tableName,
                'title' => $columnsInfo['title'],
                'columns' => $columnsInfo['fields'],
                'data' => [],
                'action' => 'new',
                'new-action' => 'add',
            ],
        ];
    }

    static function edit($tableName, $rowId)
    {
        // Параметры столбцов таблицы для вывода на экран
        $columnsInfo = Db::tableColumnsInfo($tableName);
        $rowData = Db::rowData($tableName, $rowId);
        if ($rowData == false) {
            return Db::errorInfo();
        }
        return [
            'status' => 'ok',
            'data' => [
                'name' => $tableName,
                'title' => $columnsInfo['title'],
                'columns' => $columnsInfo['fields'],
                'data' => $rowData,
                'action' => 'edit',
                'new-action' => 'save',
            ],
        ];
    }

    static function copy($tableName, $rowId)
    {
        // Параметры столбцов таблицы для вывода на экран
        $columnsInfo = Db::tableColumnsInfo($tableName);
        $rowData = Db::rowData($tableName, $rowId);
        if ($rowData == false) {
            return Db::errorInfo();
        }
        return [
            'status' => 'ok',
            'data' => [
                'name' => $tableName,
                'title' => $columnsInfo['title'],
                'columns' => $columnsInfo['fields'],
                'data' => $rowData,
                'action' => 'copy',
                'new-action' => 'add',
            ],
        ];
    }

    static function deletePrompt($tableName, $rowId)
    {
        // Параметры столбцов таблицы для вывода на экран
        $columnsInfo = Db::tableColumnsInfo($tableName);
        $rowData = Db::rowData($tableName, $rowId);
        if ($rowData == false) {
            return Db::errorInfo();
        }
        return [
            'status' => 'ok',
            'data' => [
                'name' => $tableName,
                'title' => $columnsInfo['title'],
                'columns' => $columnsInfo['fields'],
                'data' => $rowData,
                'action' => 'del',
                'new-action' => 'delete',
            ],
        ];
    }

}
