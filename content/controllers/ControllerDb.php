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
    static function loadFromExcel()
    {
        global $app;
        $array = MyFuncExcel::readExcelFromPost('file');
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
            $dateCreate = strlen($row[2]) > 0 ? date('Y-m-d', dateExcelToUnix($row[2])) : '';
            $dateEndPlan = strlen($row[3]) > 0 ? date('Y-m-d', dateExcelToUnix($row[3])) : '';
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

        // Справочник типов люков
        $result = Db::selectTable('list_product_type');
        $hatchTypes = [];
        foreach ($result as $row) {
            $hatchTypes[$row['name']] = $row['item_id'];
        }

        // Заполнение базы данных
        foreach ($orders as $order) {
            $orderId = Db::rowInsert('orders', $order['fields']);
            foreach ($order['products'] as $product) {
                $fields = [
                    ['name' => 'number'         , 'value' => $product['number'] ],
                    ['name' => 'door_code'      , 'value' => $product['door_code'] ],
                    ['name' => 'width'          , 'value' => $product['width'] ],
                    ['name' => 'height'         , 'value' => $product['height'] ],
                    ['name' => 'count'          , 'value' => $product['count'] ],
                    ['name' => 'flaps_count'    , 'value' => $product['flaps_count'] ],
                    ['name' => 'hatch_type'     , 'value' => $product[''] ],
                    ['name' => 'color'          , 'value' => $product['color'] ],
                    ['name' => 'locking_type'   , 'value' => $product['locking_type'] ],
                    ['name' => 'comment'        , 'value' => $product['comment'] ],

                ];

            }
        }

        $app->contentData = [
            'status' => 'ok',
            //'data' => $array,
            'data' => [$productTypes, $orders],
        ];
        return true;
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

function dateExcelToUnix($dateExcel)
// https://stackoverflow.com/questions/50366682/phpspreadsheets-formating-the-date-importing-from-xlsx
{
    return ($dateExcel - 25569) * 86400;
}
