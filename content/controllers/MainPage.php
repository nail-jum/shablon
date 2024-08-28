<?php

/**
 * Контроллер подготовки данных для главной страницы
 */

class MainPage
{
    static function prepare()
    {
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
