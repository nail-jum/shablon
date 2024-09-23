<?php

// Основной файл (точка входа)

session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/autoload.php';

/**
 * Инициализация приложения. Заполнение массивов:
 * $app->user;       // информация о пользователе
 * $app->url;        // информация о запрошенном адресе
 * $app->script;     // информация о выполняемом скрипте
 */

$app = new Application;

// Массив сегментов адреса страницы
$urlArray = $app->url['pathArray'];

// Первый сегмент запрашиваемого адреса
//$urlFirstSegment = $app->url['pathArray'][0];

//switch ($urlFirstSegment) {
switch ($urlArray[0]) {
    //----------------------------------------
    case 'api':                 // Запросы API
        $router = 'api.php';
        break;

    //----------------------------------------
    default:                    // Веб-запросы
        $router = 'web.php';
        break;
};

// Подключение соответствующего роутера
require_once $_SERVER['DOCUMENT_ROOT'] . '/content/routers/' . $router;
