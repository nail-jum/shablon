<?php

/**
 * Контроллер подготовки данных для главной страницы
 */

class MainPage
{
    static function prepare()
    {
        global $app;
        $testMessage = 'Test message';
        $orderList = [
            1 => [
                'date' => '01.09.2024',
                'name' => 'Заказ 1',
                'products' => [
                    'Изделие 1',
                    'Изделие 2',
                    'Изделие 3',
                ],
            ],
            2 => [
                'date' => '02.09.2024',
                'name' => 'Заказ 2',
                'products' => [
                    'Изделие 2',
                    'Изделие 3',
                    'Изделие 4',
                    'Изделие 5',
                ],
            ],
            3 => [
                'date' => '04.09.2024',
                'name' => 'Заказ 3',
                'products' => [
                    'Изделие 1',
                    'Изделие 2',
                    'Изделие 3',
                    'Изделие 4',
                    'Изделие 5',
                ],
            ],
        ];

        $app->contentData = [
            'orderList' => $orderList,
        ];
        $app->template = 'main_page.php';
    }

}
