<?php

/**
 * Контроллер для управления базой данных
 */

class ControllerDb
{
    static function run($pageRequest)
    {
        // Массив сегментов адреса страницы
        $pagePathArray = $pageRequest->url['pathArray'];
        if ($pagePathArray[0] != 'db') {
            return [
                'status' => 'error',
                'data' => $pageRequest->url['path'],
                'info' => ["Неверный выбор обработчика для запроса $pagePathArray[0]"],
            ];
        }

        $testMessage = 'Test message';
        return $testMessage;
        /*
        $template = 'main_page.php';
        return [
            'template' => $template,
            'content' => $testMessage,
        ];
        */
    }

}
