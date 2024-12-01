<?php

/**
 * Класс для работы с таблицами базы данных
 */
include_once ($_SERVER['DOCUMENT_ROOT'] . '/config/db_config.php');

class Table
{
    static $maxLimit = 1000;
    public $tableName = false;      // имя таблицы
    public $tableTitle = false;     // Описание таблицы
    public $fields = [];            // поля
    public $primaryKey = false;     // первичный ключ
    public $rows = [];              // набор записей
    public $rowId = false;          // id записи
    public $rowData = [];           // данные записи
    public $action = '';            //
    public $newAction = '';         //
    public $rowCount = 0;

    public function __construct($tableName)
    {
        $this->tableName = $tableName;
        $tableInfo = DB_CONF[$tableName];
        foreach ($tableInfo['fields'] as $fieldName => $fieldInfo) {
            $this->fields[$fieldName] = [
                'name'   => $fieldName,                     // имя поля
                'title'  => $fieldInfo['title'],            // шапка столбца
                'type'   => $fieldInfo['type'],             // тип данных
                'class'  => $fieldInfo['class'] ?? 'c',     // класс (выравнивание)
                'select' => $fieldInfo['select'] ?? false,  // выбор из справочника
            ];
        }
        $this->tableTitle = $tableInfo['title'];
        $this->primaryKey = $tableInfo['primary_key'];
    }

    public function getRowCount()
    {
        // Количество записей в таблице
        $sql = "SELECT COUNT(*) AS 'count' FROM \"{$this->tableName}\"";
        $request = Db::query($sql);
        $result = $request->fetch(PDO::FETCH_ASSOC);
        $this->rowCount = $result['count'];
        return $this->rowCount;
    }

    static function log($text)
    {
        $logFile = $_SERVER['DOCUMENT_ROOT'] . '/log/Table_' . date('Y-m-d') . '.log';
        file_put_contents($logFile, date('Y-m-d H:i:s ') . $text . PHP_EOL, FILE_APPEND);
    }

    static function arrayOfTable($tableName, $primaryKey = false, $params = [])
    {
        $limit = (int) ($params['limit'] ?? self::$maxLimit);
        $offset = (int) ($params['offset'] ?? 0);
        $sql = "SELECT * FROM \"{$tableName}\" LIMIT {$limit} OFFSET {$offset}";
        $request = Db::query($sql);
        if ($request === false) {
            self::log(PHP_EOL . $sql . PHP_EOL . implode(PHP_EOL, DB::$errorMessage));
            return false;
        }
        if ($primaryKey === false) {
            return $request -> fetchAll(PDO::FETCH_ASSOC);
        }
        $data = [];
        // чтение первой записи
        $row = $request -> fetch(PDO::FETCH_ASSOC);
        if (!isset($row[$primaryKey])) {
            self::log("Ключевое поле $primaryKey не найдено");
            return false;
        }
        $data[$row[$primaryKey]] = $row;
        // чтение остальных записей
        while ($row = $request -> fetch(PDO::FETCH_ASSOC)) {
            $data[$row[$primaryKey]] = $row;
        }
        return $data;
    }

    public function getRows($start = 0, $count = false)
    {
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
            $logFile = $_SERVER['DOCUMENT_ROOT'] . '/log/Table_' . date('Y-m-d') . '.log';
            file_put_contents($logFile, date('Y-m-d H:i:s ') . PHP_EOL . $sql . PHP_EOL . implode(PHP_EOL, DB::$errorMessage), FILE_APPEND);
            return false;
        }
    }

    public function getRow($rowId = false)
    {
        if ($rowId == false) {
            //$app::$errorMessage[] = "getRow(): Не указано значение ключевого поля.";
            return false;
        }
        $request = Db::query("SELECT * FROM \"{$this->tableName}\" WHERE \"{$this->primaryKey}\"=$rowId");
        $data = $request->fetch(PDO::FETCH_ASSOC);
        if ($data == false) {
            //$app::$errorMessage[] = "getRow(): Ошибка выполнения запроса.";
            return false;
        }
        return $data;
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
            //$rowData = Db::rowData($this->tableName, $rowId);
            $rowData = $this->getRow($rowId);
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
        $columnsInfo = Db::tableInfo($tableName);
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
