<?php

// Основной файл (точка входа)

session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/autoload.php';

// Параметры полученного запроса
$pageRequest = new Request();
// Массив сегментов адреса страницы
$pagePathArray = $pageRequest->url['pathArray'];
// Имя вызываемой страницы (первый сегмент адреса)
$pageName = $pagePathArray[0] ?? '';
if ($pageName == '') {
    $pageName = 'main';
}

if ($pageName != 'user_logon') {
    // Проверка авторизации пользователя
    //require_once $_SERVER['DOCUMENT_ROOT'] . '/autorization.php';
}

// --------------------------------------------
// Обработка полученного запроса
// --------------------------------------------
$contentData = [];      // результаты, полученных от контроллера
$template = '';         // шаблон для вывода

switch ($pageName) {

    //----------------------------------------
    case 'main':                    // Главная страница
        $contentData = MainPage::prepare();
        $template = 'main_page.php';
        break;

    //----------------------------------------
    case 'db':                      // Работа с базой данных
        $contentData = ControllerDb::run($pageRequest);
        $template = 'db_info.php';
        break;

    //----------------------------------------
    case 'db_info':                 // Информация о базе данных
        $contentData = Db::getDbInfo();
        $template = 'db_info.php';
        break;

    //----------------------------------------
    case 'db_create':                 // Создание базы данных по умолчанию
        $contentData = Db::dbCreate();
        $template = 'result_info.php';
        break;

    //----------------------------------------
    case 'example.html':            // образец страницы (временно)
        //include $_SERVER['DOCUMENT_ROOT'] . '/content/example.html';
        break;

    //----------------------------------------
    default:
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/content/' . $pageName . '.php')) {
            // если найден одноименный php-файл
            $template = $pageName . '.php';
        }
        break;
};

// Обработка результатов, полученных от контроллера
//$content = $data;

if (strlen($template) == 0) {
    // не указан шаблон для вывода
    $template = 'page_not_exist.php';
}

// Вывод результатов
include_once ($_SERVER['DOCUMENT_ROOT'] . "/content/templates/{$template}");
