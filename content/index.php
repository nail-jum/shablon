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

// Инициализация приложения
//global $app;
$app = new Application;

/*
// Инициализация пользователя
//$user = new User();

// Имя вызываемой страницы
$pageName = $app->url['pathArray'][0] ?? '';
if ($pageName == '') {
    $pageName = 'index';
}
//$app->debug($pageName);
$app->debug('<pre>' . print_r($app, true) . '</pre>');

if ($pageName != 'user_logon') {
    // Перенаправление на страницу авторизации неинициализированного пользователя
    if (!$app->user->isInit) {
        header('Location: https://' . $_SERVER['SERVER_NAME'] . '/user_logon' . '?prevUrl=' . $_SERVER['REQUEST_URI']);
    }
}
*/

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
    case 'user_logon':                 //
        require_once $_SERVER['DOCUMENT_ROOT'] . '/content/user_logon.php';
        exit;

    //----------------------------------------
    default:                    // Веб-запросы
        $router = 'web.php';
        break;
};

// Подключение соответствующего роутера
require_once $_SERVER['DOCUMENT_ROOT'] . '/content/routers/' . $router;
