<?php

// Основной файл (точка входа)

session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/autoload.php';

// Параметры полученного запроса
$pageRequest = new Request();
// Массив сегментов адреса страницы
$pagePathArray = $pageRequest->url['pathArray'];
// Имя вызываемой страницы (первый сегмент адреса)
//$pageName = $pagePathArray[0] ?? '';
$pageName = $pageRequest->url['pathArray'][0] ?? '';
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
    case 'request-info':            // Информация о запросе для теста
        $contentData = $pageRequest;
        $template = 'page_debug.php';
        break;

    //----------------------------------------
    case 'main':                    // Главная страница
        $contentData = MainPage::prepare();
        $template = 'main_page.php';
        break;

    //----------------------------------------
    case 'db':                      // Работа с базой данных
        list($contentData, $template) = ControllerDb::run($pageRequest);
        //$contentData = ControllerDb::run($pageRequest);
        //$template = 'db_info.php';
        break;

    //----------------------------------------
    case 'table':                   // Работа с базой данных
        list($contentData, $template) = ControllerTable::run($pageRequest);
        //$contentData = ControllerDb::run($pageRequest);
        //$template = 'db_info.php';
        break;

    //----------------------------------------
    case 'materials':               // Материалы
        // вывод списка записей таблицы
        $contentData = Db::getTableData('materials');
        $template = 'table_edit.php';
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
