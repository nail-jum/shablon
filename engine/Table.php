<?php

/**
 * Класс для работы с таблицами базы данных
 */

class Table
{
    static function editPrepare($action, $tableName, $rowId = false)
    {
        //
        $newAction = [
            'new'  => 'add',
            'edit' => 'upd',
            'copy' => 'add',
            'del'  => 'delete',
        ];
        // Параметры столбцов таблицы для вывода на экран
        $columnsInfo = Db::tableColumnsInfo($tableName);
        // Данные таблицы
        if ($rowId != false) {
            $rowData = Db::rowData($tableName, $rowId);
            if ($rowData == false) {
                return Db::errorInfo();
            }
        } else {
            $rowData = [];
        }
        return [
            'status' => 'ok',
            'data' => [
                'name' => $tableName,
                'row-id' => $rowId,
                'title' => $columnsInfo['title'],
                'columns' => $columnsInfo['fields'],
                'data' => $rowData,
                'action' => $action,
                'new-action' => $newAction[$action],
            ],
        ];
    }

    static function editSubmit($action, $tableName, $rowId = false)
    {
        // Параметры столбцов таблицы
        $columnsInfo = Db::tableColumnsInfo($tableName);
        $fields = $columnsInfo['fields'];

        // Чтение значений полей из массива POST
        //$fieldNames = [];               // массив имен полей
        $fieldValues = [];              // массив значений
        //$fieldUpdateValues = [];        // массив пар поле = значение
        $placeholders = [];
        foreach ($fields as $field) {
            switch ($field['type']) {
                case 'text':
                    $field['value'] = $_POST[$field['name']] ?? '';
                    break;
                case 'integer':
                    $field['value'] = (int)($_POST[$field['name']] ?? '');
                    break;
                default:
                    $field['value'] = $_POST[$field['name']] ?? '';
                    break;
            }
            //$fieldNames[] = $field['name'];
            $fieldValues[] = $field;
            //$fieldUpdateValues[] = "\"{$field['name']}\" = '{$field['value']}'";
            $placeholders[] = '?';
        }

        // формирование запроса к базе данных
        $sql ='';
        switch ($action) {
            case 'add':
                // новая запись
                Db::rowInsert($tableName, $fieldValues);
                break;
            case 'upd':
                // обновление записи
                Db::rowUpdate($tableName, $fieldValues, $rowId);
                break;
            default:
                break;
        }


        return [
            'status' => 'ok',
            'data' => [
                'post' => $_POST,
                'fields' => $fields,
                'sql' => $sql,
            ],
        ];
    }
}
