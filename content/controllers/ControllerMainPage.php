<?php

/**
 * Контроллер подготовки данных для главной страницы
 */

class ControllerMainPage
{
    static function run()
    {
        global $app;
        $app->user['access'] = true;
        $app->template = 'main_page.php';

        $testMessage = 'Test message';
        $orderList = Db::getTableData('orders');

        $app->contentData = [
            'orderList' => $orderList,
        ];
    }

}
