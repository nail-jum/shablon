<?php

/**
 * Контроллер для управления базой данных
 */
        global $app;

class ControllerDb
{

    /**
     * Загрузка начальных данных из файла Excel
     */
    static function loadOrdersFromExcel()
    {
        global $app;
        $array = MyFuncExcel::readExcelFromPost('file-source');
        if ($array == false) {
            $app->contentData = [
                'status' => 'error',
                'data' => $app->url['path'],
                'info' => ['Не удалось прочитать файл'],
            ];
            return false;
        }
        // Создание массива заказов
        $orders = [];
        $productTypes = [];

        foreach ($array as $row) {
            $orderName = $row[0];
            if ( (strlen($orderName) == 0) || ($orderName == 'Зак.в произв.') ) {
                continue;
            }
            //$dateCreate = strlen($row[2]) > 0 ? date('Y-m-d', dateExcelToUnix($row[2])) : '';
            //$dateEndPlan = strlen($row[3]) > 0 ? date('Y-m-d', dateExcelToUnix($row[3])) : '';
            $dateCreate = dateExcelToSql($row[2]);
            $dateEndPlan = dateExcelToSql($row[3]);
            $hatchType = $row[5];
            $color = (int) $row[6];
            $lockingType = (int) $row[7];
            if (!isset($orders[$orderName])) {
                // добавление нового заказа
                $orders[$orderName] = [
                    'fields' => [
                        ['name' => 'name'          , 'value' => $orderName],
                        ['name' => 'manager'       , 'value' => $row[1]],
                        ['name' => 'date_create'   , 'value' => $dateCreate],
                        ['name' => 'date_end_plan' , 'value' => $dateEndPlan],
                    ],
                    'products'      => [],
                ];
            }
            // добавление изделий в заказ
            $orders[$orderName]['products'][] = [
                'number'        => count($orders[$orderName]['products']) + 1,
                'door_code'     => $row[14],
                'width'         => $row[8],
                'height'        => $row[10],
                'count'         => $row[11],
                'flaps_count'   => $row[12],
                'hatch_type'    => $hatchType,
                'color'         => $color,
                'locking_type'  => $lockingType,
                'comment'       => $row[34],
            ];
            // формирование массива типов люка
            //$productTypes[$hatchType] = ($productTypes[$hatchType] ?? 0) + 1;
        }

        $hatchTypes   = Table::arrayOfTable('list_hatch_types', 'name');
        $colors       = Table::arrayOfTable('list_colors', 'name');

        // Очистка таблиц
        Db::exec('DELETE FROM "orders"');
        Db::exec('DELETE FROM "products"');

        // Заполнение базы данных
        foreach ($orders as $order) {
            // добавление заказа
            $orderId = Db::rowInsert('orders', $order['fields']);
            // добавление изделий в заказе
            foreach ($order['products'] as $product) {
                $hatchTypeId = $hatchTypes[$product['hatch_type']]['item_id'] ?? 0;
                $colorId = $colors[$product['color']]['item_id'] ?? 0;
                $fields = [
                    ['name' => 'order_id'       , 'value' => $orderId ],
                    ['name' => 'number'         , 'value' => $product['number'] ],
                    ['name' => 'door_code'      , 'value' => $product['door_code'] ],
                    ['name' => 'width'          , 'value' => $product['width'] ],
                    ['name' => 'height'         , 'value' => $product['height'] ],
                    ['name' => 'count'          , 'value' => $product['count'] ],
                    ['name' => 'flaps_count'    , 'value' => $product['flaps_count'] ],
                    ['name' => 'hatch_type_id'  , 'value' => $hatchTypeId ],
                    ['name' => 'color_id'       , 'value' => $colorId ],
                    ['name' => 'lock_type_id'   , 'value' => $product['locking_type'] ],
                    ['name' => 'comment'        , 'value' => $product['comment'] ],
                ];
                $productId = Db::rowInsert('products', $fields);
            }
        }

       return [
            'status' => 'ok',
            //'data' => $array,
            'data' => [
                'productTypes' => $productTypes,
                'orders' => $orders,
            ],
        ];
        //return true;
    }

    /**
     * Загрузка материалов
     */
    static function loadMaterialsFromExcel()
    {
        global $app;
        $array = MyFuncExcel::readExcelFromPost('file-source');
        if ($array == false) {
            $app->contentData = [
                'status' => 'error',
                'data' => $app->url['path'],
                'info' => ['Не удалось прочитать файл'],
            ];
            return false;
        }
        Db::exec('DELETE FROM "materials"');     // очистка таблицы
        foreach ($array as $row) {
            $fields = [
                ['name' => 'name'       , 'value' => $row[0]],
            ];
            Db::rowInsert('materials', $fields);
        }
        return [
            'data' => $array,
        ];
        //return true;
    }

    /**
     * Загрузка полуфабрикатов
     */
    static function loadSemiproductsFromExcel()
    {
        global $app;
        $array = MyFuncExcel::readExcelFromPost('file-source');
        if ($array == false) {
            $app->contentData = [
                'status' => 'error',
                'data' => $app->url['path'],
                'info' => ['Не удалось прочитать файл'],
            ];
            return false;
        }
        Db::exec('DELETE FROM "semiproducts"');     // очистка таблицы
        foreach ($array as $row) {
            $fields = [
                ['name' => 'name'       , 'value' => $row[0]],
                ['name' => 'code'       , 'value' => $row[0]],
            ];
            Db::rowInsert('semiproducts', $fields);
        }
        return true;
    }

    static function deleteTable($tableName)
    {
        if (strlen($tableName) == 0) {
            return false;
        }
        Db::exec("DROP TABLE IF EXISTS \"{$tableName}\"");     // очистка таблицы
    }

    static function run()
    {
        global $app;
        // Массив сегментов адреса страницы
        $pagePathArray = $app->url['pathArray'];
        if ($pagePathArray[0] != 'db') {
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
                Db::loadDefault();
                //$template = 'result_info.php';
                $template = 'db_info.php';
                break;

            //----------------------------------------
            case 'update':                      // Создание базы данных по умолчанию
                $contentData = Db::dbUpdate();
                //$template = 'result_info.php';
                $template = 'db_info.php';
                break;

            //----------------------------------------
            case 'delete-table':                 // Удаление таблицы (тестовая)
                $tableName = $pagePathArray[2] ?? '';
                $contentData = self::deleteTable($tableName);
                $template = 'db_load_result.php';
                break;

            //----------------------------------------
            case 'load':                        // Загрузка данных из файла (тестовая)
                //$contentData = self::loadOrdersFromExcel();
                $template = 'db_load.php';
                break;

            //----------------------------------------
            case 'load-orders':                 // Загрузка данных из файла (тестовая)
                $contentData = self::loadOrdersFromExcel();
                $template = 'db_load_result.php';
                break;

            //----------------------------------------
            case 'load-materials':                 // Загрузка данных из файла (тестовая)
                $contentData = self::loadMaterialsFromExcel();
                $template = 'db_load_result.php';
                break;

            //----------------------------------------
            case 'load-semiproducts':                 // Загрузка данных из файла (тестовая)
                $contentData = self::loadSemiproductsFromExcel();
                $template = 'db_load_result.php';
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
        $app->contentData = $contentData ?? [];
        $app->template = $template;
    }
}

function dateExcelToUnix($dateExcel)
// https://stackoverflow.com/questions/50366682/phpspreadsheets-formating-the-date-importing-from-xlsx
{
    return ($dateExcel - 25569) * 86400;
}

function dateExcelToSql($dateExcel)
// https://stackoverflow.com/questions/50366682/phpspreadsheets-formating-the-date-importing-from-xlsx
{
    if (strlen($dateExcel) == 0) {
        return '';
    }
    return date('Y-m-d', ($dateExcel - 25569) * 86400);
}
