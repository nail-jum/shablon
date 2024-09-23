<?php

/**
 * Класс для работы с базой данных
 */

// https://prowebmastering.ru/category/sqlite

include_once ($_SERVER['DOCUMENT_ROOT'] . '/config/db_config.php');

class Db
{
    const SQLITE_FILE = 'db/test.db';
    static $pdo;                    // объект PDO

    // свойства для вывода сообщений об ошибке
    static $connected = false;      // признак подключения
    static $errorMessage = [];      // сообщение об ошибке


    static function connect($new = false)
    {
        if (self::$connected == true) {
            return true;
        }
        $dbFile = $_SERVER['DOCUMENT_ROOT'] . '/' . self::SQLITE_FILE;
        // файл не найден и нет флага на создание нового
        if (!file_exists($dbFile) && !$new) {
            self::$errorMessage[] = 'Файл базы данных не найден.';
            return false;
        }
        // открытие существующей или создание новой базы
        if (self::$pdo == null) {
            try {
                self::$pdo = new \PDO("sqlite:" . $dbFile);
            } catch (PDOException $e) {
                self::$errorMessage[] = 'Ошибка создания базы данных.';
                return false;
            }
        }
        self::$connected = true;
        return true;
    }

    static function query($sql = '', $showError = true)
    {
        if (strlen($sql) == 0) {
            self::$errorMessage[] = 'Пустая строка запроса к базе данных.';
            return false;
        }
        if (!self::connect()) {
            self::$errorMessage[] = 'Ошибка подключения к базе данных.';
            return false;
        }
        try {
            $result = self::$pdo->query($sql);
        } catch (Exception $e) {
            if ($showError) {
                self::$errorMessage[] = 'Ошибка выполнения запроса: ';
                self::$errorMessage[] = $e->getMessage();
            }
            return false;
        }
        if ($result == false) {
            if ($showError) {
                self::$errorMessage[] = 'Ошибка выполнения запроса: ';
            }
            return false;
        }
        return $result;
    }

    static function exec($sql = '')
    {
        self::connect();
        try {
            $result = self::$pdo->exec($sql);
        } catch (Throwable $e) {
            self::$errorMessage[] = 'Ошибка выполнения запроса: ';
            self::$errorMessage[] = $e->getMessage();
            $result = false;
        }
        return $result;
    }

    static function preparedExec($sql = '', $data = [], $showError = true)
    {
        if (!self::connect()) {
            self::$errorMessage[] = 'Ошибка подключения к базе данных.';
            return false;
        }
        try {
            $request = self::$pdo->prepare($sql);
            $result = $request->execute($data);
        } catch (Exception $e) {
            if ($showError) {
                self::$errorMessage[] = 'Ошибка выполнения запроса: ';
                self::$errorMessage[] = $e->getMessage();
            }
            return false;
        }
        if ($result == false) {
            if ($showError) {
                self::$errorMessage[] = 'Ошибка выполнения запроса: ';
            }
            return false;
        }
        return $result;
    }

    static function tableExists($table)
    {
        $result = self::query("SELECT 1 FROM {$table} LIMIT 1", false);
        if ($result == false) {
            self::$errorMessage[] = "Результат проверки: таблица $table не существует";
            return false;
        } else {
            return true;
        }
    }

    static function errorInfo()
    {
        return [
            'status' => 'error',
            'connected' => self::$connected,
            'filename' => self::SQLITE_FILE,
            'message' => self::$errorMessage,
        ];
    }

    static function warningInfo()
    {
        return [
            'status' => 'warning',
            'message' => self::$errorMessage,
        ];
    }

    // Описание конфигурации базы данных
    static function dbConfig($filter = '')
    {
        return DB_CONF;
    }

    // Физическая (реальная) структура базы данных
    static function dbStructure($filter = '')
    {
        // sql-запрос
        $sql = "SELECT * FROM \"sqlite_master\" WHERE type='table'";
        // дополнительный фильтр
        if (strlen($filter) > 0) {
            $sql .= ' AND ' . $filter;
        }
        // схема базы данных
        $baseSchema = self::query($sql);

        if ($baseSchema == false) {
            self::$errorMessage[] = 'Ошибка получения схемы базы.';
            return false;
        } else {
            //$baseInfo = $baseSchema->fetchAll(PDO::FETCH_ASSOC);
            $baseInfo = [];
            while ($baseRow = $baseSchema->fetch(PDO::FETCH_ASSOC)) {
                $tableName = $baseRow['name'];
                // схема таблицы
                $tableSchema = self::query("pragma table_info('$tableName');");
                //$tableInfo = $tableSchema->fetchAll(PDO::FETCH_ASSOC);
                $tableInfo = [];
                while ($row = $tableSchema->fetch(PDO::FETCH_ASSOC)) {
                    $tableInfo[$row['name']] = $row;
                }
                $baseInfo[$tableName] = [
                    'name' => $tableName,
                    'sql' => $baseRow['sql'],
                    'fields' => $tableInfo,
                ];
            }
            if (count($baseInfo) == 0) {
                $result = ['status' => 'Таблицы не обнаружены', 'info' => self::$pdo->errorInfo()];
            }
        }
        return $baseInfo;
    }

    static function dbCreate()
    {
        // Создание базы данных
        self::connect(true);
        if (self::$connected == false) {
            self::$errorMessage[] = 'Ошибка создания базы данных';
            return false;
        }
        // Создание/добавление таблиц
        foreach (DB_CONF as $tableName => $tableInfo) {
            $primaryKeyField = $tableInfo['primary_key'] ?? false;
            $sqlArray = [];
            foreach ($tableInfo['fields'] as $fieldName => $field) {
                $notNull = (bool) ($field['notnull'] ?? false);
                $defaulValue = $field['default'] ?? false;
                $sqlArray[] =
                    "\"{$fieldName}\" {$field['type']}"
                    . ($notNull ? "  NOT NULL" : "")
                    . (($fieldName == $primaryKeyField) ? ' PRIMARY KEY' : '')
                    . ($defaulValue ? " DEFAULT '$defaulValue'" : '');
            }
            $sql = "CREATE TABLE IF NOT EXISTS \"$tableName\" (" . implode(',', $sqlArray) . ")";
            self::exec($sql);
        }
        // Заполнение таблиц данными по умолчанию
        foreach (DB_DEFAULT_DATA as $tableName => $defaultData) {
            self::dbMultiInsert($tableName, $defaultData['fields'], $defaultData['data']);
        }
    }

    // - - - - - - - - - - - - - - - - - - - - - - - -
    // Обновление данных записи таблицы
    // - - - - - - - - - - - - - - - - - - - - - - - -
    static function rowUpdate($tableName, $fieldValues, $rowId)
    {
        // строка подстановок для запроса
        $placeholders = [];
        $values =[];
        foreach ($fieldValues as $field) {
            $placeholders[] = "\"{$field['name']}\" = ?";
            $values[] = $field['value'];
        }
        $sql = "UPDATE \"$tableName\" SET " . implode(',', $placeholders) . " WHERE \"id\" = {$rowId};";
        // выполнение подготовленного запроса
        $result = self::preparedExec($sql, $values);
        if ($result == false) {
            return Db::errorInfo();
        }
        return [
            'status' => 'ok',
            'data' => [
                'sql' => $sql,
                'lastInsertId' => self::$pdo->lastInsertId(),
            ],
        ];
    }

    // - - - - - - - - - - - - - - - - - - - - - - - -
    // Вставка данных в таблицу одной записи
    // - - - - - - - - - - - - - - - - - - - - - - - -
    static function rowInsert($tableName, $fieldValues)
    {
        // строка подстановок для запроса
        //$placeholders = str_repeat('?,', count($fields) - 1) . '?';
        $placeholders = [];
        $fields =[];
        $values =[];
        foreach ($fieldValues as $field) {
            $placeholders[] = '?';
            $fields[] = $field['name'];
            $values[] = $field['value'];
        }
        $sql = "INSERT INTO \"$tableName\"(\"" . implode('","', $fields) . "\") VALUES(" . implode(',', $placeholders) . ');';
        // выполнение подготовленного запроса
        $result = self::preparedExec($sql, $values);
        if ($result == false) {
            //return Db::errorInfo();
            return false;
        }
        return self::$pdo->lastInsertRowid();
        /*
        return [
            'status' => 'ok',
            'data' => [
                'sql' => $sql,
                'lastInsertId' => self::$pdo->lastInsertId(),
            ],
        ];
        */
    }

    // - - - - - - - - - - - - - - - - - - - - - - - -
    // Вставка данных в таблицу одной записи
    // - - - - - - - - - - - - - - - - - - - - - - - -
    static function dbInsert_0($tableName, $fields, $values)
    {
        // строка подстановок для запроса
        $placeholders = str_repeat('?,', count($fields) - 1) . '?';
        // имена переменных для подстановки в запрос
        $valNames = [];
        foreach ($fields as $field) {
            $valNames[] = ':_' . $field;
        }
        $sql = "INSERT INTO \"$tableName\"(\"" . implode('","', $fields) . "\") VALUES(" . implode(',', $valNames) . ');';
        $request = self::$pdo->prepare($sql);

        $data = [];
        for ($ii = 0; $ii < count($fields); $ii++) {
            $data[$valNames[$ii]] = $values[$ii];
        }
        $result = $request->execute($data);
        if ($result == false) {
            return Db::errorInfo();
        }
        return [
            'status' => 'ok',
            'data' => [
                'sql' => $sql,
                'lastInsertId' => self::$pdo->lastInsertId(),
            ],
        ];
    }

    // - - - - - - - - - - - - - - - - - - - - - - - -
    // Вставка в таблицу массива записей
    // - - - - - - - - - - - - - - - - - - - - - - - -
    static function dbMultiInsert($tableName, $fields, $valueArray)
    {
        // имена переменных для подстановки в запрос
        $valNames = [];
        foreach ($fields as $field) {
            $valNames[] = ':_' . $field;
        }
        $sql = "INSERT INTO \"$tableName\"(\"" . implode('","', $fields) . "\") VALUES(" . implode(',', $valNames) . ');';
        $request = self::$pdo->prepare($sql);

        foreach ($valueArray as $row) {
            $data = [];
            for ($ii = 0; $ii < count($fields); $ii++) {
                $data[$valNames[$ii]] = $row[$ii];
            }
            $result = $request->execute($data);
            if ($result == false) {
                return Db::errorInfo();
            }
        }
        return [
            'status' => 'ok',
            'data' => [
                'sql' => $sql,
                'lastInsertId' => self::$pdo->lastInsertId(),
            ],
        ];
    }

    // - - - - - - - - - - - - - - - - - - - - - - - -
    // Параметры столбцов для вывода данных на экран
    // - - - - - - - - - - - - - - - - - - - - - - - -
    static function tableColumnsInfo($tableName)
    {
        $columnsInfo = [];
        $tableInfo = DB_CONF[$tableName];
        foreach ($tableInfo['fields'] as $fieldName => $fieldInfo) {
            $columnsInfo[$fieldName] = [
                'name'  => $fieldName,                  // имя поля
                'title' => $fieldInfo['title'],         // шапка столбца
                'type'  => $fieldInfo['type'],          // тип данных
                'class' => $fieldInfo['class'] ?? 'c',  // класс (выравнивание)
            ];
        }
        return [
            'title' => $tableInfo['title'],
            'primary_key' => $tableInfo['primary_key'],
            'fields' => $columnsInfo,
        ];
    }

    // - - - - - - - - - - - - - - - - - - - - - - - -
    // Вывод информации о структуре базы и ее таблиц
    // - - - - - - - - - - - - - - - - - - - - - - - -
    static function getDbStructure($filter = '')
    {
        $dbConfig = self::dbConfig();
        $result = self::dbStructure($filter);
        if ($result === false) {
            return self::errorInfo();
        } elseif (count($result) == 0) {
            self::$errorMessage[] = 'Пустой список таблиц';
            return self::warningInfo();
        } else {
            return [
                'status' => 'ok',
                'data' => $result,
            ];
        }
    }

    // - - - - - - - - - - - - - - - - - - - - - - - -
    // Вывод информации о структуре базы и ее таблиц
    // совмещенной с описанием конфигурации
    // - - - - - - - - - - - - - - - - - - - - - - - -
    static function getDbStructureInfo($filter = '')
    {
        $dbConfig = self::dbConfig();           // описание конфигурации
        $result = self::dbStructure($filter);   // структура базы данных
        if ($result === false) {
            return self::errorInfo();
        } elseif (count($result) == 0) {
            self::$errorMessage[] = 'Пустой список таблиц';
            return self::warningInfo();
        } else {
            $structure = [];
            foreach ($result as $tableName => $tableStructure) {
                $title = $dbConfig[$tableName]['title'] ?? '';
                $structure[$tableName] = $tableStructure;
                $structure[$tableName]['title'] = $title;   // добавление имени таблицы
            }
            return [
                'status' => 'ok',
                'data' => $structure,
            ];
        }
    }

    // - - - - - - - - - - - - - - - - - - - - - - - -
    // Вывод данных таблицы
    // - - - - - - - - - - - - - - - - - - - - - - - -
    static function selectTable($tableName, $start = 0, $count = false)
    {
        if (strlen($tableName) == 0) {
            self::$errorMessage[] = "Не указано имя таблицы.";
            return false;
        }
        if (!self::tableExists($tableName)) {
            return false;
        }
        // выполнение подготовленного запроса
        //$request = self::query("SELECT * FROM `$tableName`");
        $sql = "SELECT * FROM :tableName";
        $result = self::preparedExec($sql, ['tableName' => $tableName]);
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    static function getTableData($tableName, $start = 0, $count = false)
    {
        if (strlen($tableName) == 0) {
            self::$errorMessage[] = "Не указано имя таблицы.";
            return Db::errorInfo();
        }
        if (!self::tableExists($tableName)) {
            //self::$errorMessage[] = "Таблица $tableName в базе данных не найдена.";
            return Db::errorInfo();
        }

        // Параметры столбцов таблицы для вывода на экран
        $columnsInfo = self::tableColumnsInfo($tableName);
        // Данные таблицы
        $request = self::query("SELECT * FROM `$tableName`");
        $data = [];
        while ($row = $request->fetch(PDO::FETCH_ASSOC)) {
            //$data[$row['id']] = $row;
            $data[] = $row;
        }

        return [
            'status' => 'ok',
            'data' => [
                'name' => $tableName,
                //'title' => $columnsInfo['title'],
                'columns' => $columnsInfo['fields'],
                'primary_key' => $columnsInfo['primary_key'],
                'data' => $data,
            ],
        ];
    }

    // - - - - - - - - - - - - - - - - - - - - - - - -
    // Вывод данных записи
    // - - - - - - - - - - - - - - - - - - - - - - - -
    static function rowData($tableName, $rowId = false)
    {
        if (strlen($tableName) == 0) {
            self::$errorMessage[] = "rowData(): Не указано имя таблицы.";
            return false;
        }
        if (self::tableExists($tableName) == false) {
            self::$errorMessage[] = "rowData(): Таблица $tableName в базе данных не найдена.";
            return false;
        }
        if ($rowId == false) {
            self::$errorMessage[] = "rowData(): Не указан rowId.";
            return false;
        }
        $request = self::query("SELECT * FROM \"$tableName\" WHERE \"id\"=$rowId");
        $data = $request->fetch(PDO::FETCH_ASSOC);
        if ($data == false) {
            self::$errorMessage[] = "rowData(): Ошибка выполнения запроса.";
            return false;
        }
        return $data;
    }

    // - - - - - - - - - - - - - - - - - - - - - - - -
    // Вывод данных записи
    // - - - - - - - - - - - - - - - - - - - - - - - -
    static function getRowData($tableName, $rowId = false)
    {
        // Данные таблицы
        $rowData = self::rowData($tableName, $rowId);
        if ($rowData == false) {
            return Db::errorInfo();
        }
        // Параметры столбцов таблицы для вывода на экран
        $columnsInfo = self::tableColumnsInfo($tableName);

        return [
            'status' => 'ok',
            'data' => [
                'tableName' => $tableName,
                'rowId'     => $rowId,
                'columns'   => $columnsInfo['fields'],
                'data'      => $rowData,
                //'columns' => $columnsInfo,
                //'data' => $data,
            ],
        ];
    }

}


