<?php

/**
 * Контроллер подготовки данных для главной страницы
 */

class ControllerOrders
{
    static function run()
    {
        global $app;
        $app->user['access'] = true;
        $app->template = 'order_list.php';

        // Заказы и изделия
        $orderList    = Table::arrayOfTable('orders', 'order_id');
        $productList  = Table::arrayOfTable('products', 'product_id');

        // Справочные таблицы
        $hatchTypes   = Table::arrayOfTable('list_hatch_types', 'item_id');
        $lockingTypes = Table::arrayOfTable('list_locking_type', 'item_id');
        $colors       = Table::arrayOfTable('list_colors', 'item_id');

        // Формирование массива для вывода на экран
        foreach ($productList as $product) {
            $orderId = $product['order_id'];
            if (!isset($orderList[$orderId]['products'])) {
                // добавление пустого списка изделий в заказ
                $orderList[$orderId]['products'] = [];
                $orderList[$orderId]['flaps_count'] = 0;
            }
            //$product['color'] = $colors
            $orderList[$orderId]['products'][] = $product;
            $orderList[$orderId]['flaps_count'] += (int) $product['flaps_count'] * $product['count'];
        }


        $app->contentData = [
            'orderList' => $orderList,
            //'productList' => $productList,
            'hatchTypes' => $hatchTypes,
            'lockingTypes' => $lockingTypes,
            'colors' => $colors,
        ];
    }

}
