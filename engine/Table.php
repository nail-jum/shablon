<?php

/**
 * Класс для работы с таблицами базы данных
 */
include_once ($_SERVER['DOCUMENT_ROOT'] . '/config/db_config.php');

class Table
{
    public $tableName = false;      // имя таблицы
    public $tableTitle = false;     // Описание таблицы
    public $fields = [];            // поля
    public $primaryKey = false;     // первичный ключ
    public $rows = [];              // набор записей
    public $rowId = false;          // id записи
    public $rowData = [];           // данные записи
    public $action = '';            //
    public $newAction = '';         //

    public function __construct($tableName)
    {
        $this->tableName = $tableName;
        $tableInfo = DB_CONF[$tableName];
        foreach ($tableInfo['fields'] as $fieldName => $fieldInfo) {
            $this->fields[$fieldName] = [
                'name'  => $fieldName,                  // имя поля
                'title' => $fieldInfo['title'],         // шапка столбца
                'type'  => $fieldInfo['type'],          // тип данных
                'class' => $fieldInfo['class'] ?? 'c',  // класс (выравнивание)
            ];
        }
        $this->tableTitle = $tableInfo['title'];
        $this->primaryKey = $tableInfo['primary_key'];
    }

    public function getRows($start = 0, $count = false)
    {
        // Данные таблицы
        $sql = "SELECT * FROM \"{$this->tableName}\"";
        $result = Db::query($sql);
        if ($result != false) {
            if ($this->primaryKey != false) {
                $data = [];
                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    $data[$row[$this->primaryKey]] = $row;
                }
            } else {
                $data = $result->fetchAll(PDO::FETCH_ASSOC);
            }
            $this->rows = $data;
            //return $data;
            return true;
        } else {
            return false;
        }
    }

    public function editPrepare($action, $rowId = false)
    {
        //
        $newAction = [
            'new'  => 'add',
            'edit' => 'upd',
            'copy' => 'add',
            'del'  => 'delete',
        ];
        $this->action = $action;
        $this->newAction = $newAction[$action];
        // Данные таблицы
        if ($rowId != false) {
            $this->rowId = $rowId;
            $rowData = Db::rowData($this->tableName, $rowId);
            if ($rowData == false) {
                return false;
            }
            $this->rowData = $rowData;
        }
        return true;
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
