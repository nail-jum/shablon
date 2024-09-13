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
$pagePathArray = $app->url['pathArray'];
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
    case 'app-info':            // Информация о запросе для теста
        //$app->contentData = $pageRequest;
        $app->template = 'app_debug.php';
        break;

    //----------------------------------------
    case 'main':                    // Главная страница
        MainPage::prepare();
        break;

    //----------------------------------------
    case 'db':                      // Работа с базой данных
        ControllerDb::run();
        break;

    //----------------------------------------
    case 'selectlists':             // Список справочников
        /*
        $pathSegment_2 = $pagePathArray[1] ?? '';
        if (strlen($pathSegment_2) == 0) {
            $filter = "\"name\" LIKE 'list%'";
            $contentData = Db::getDbStructureInfo($filter);
            $template = 'selectlists.php';
            break;
        } else {

        }
        */
        ControllerSelectlist::run();
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
/*
if (strlen($app->template) == 0) {
    // не указан шаблон для вывода
    $app->template = 'page_not_exist.php';
}
*/
// Вывод результатов
//include_once ($_SERVER['DOCUMENT_ROOT'] . "/content/templates/{$app->template}");

include_once $app->templatePath($template);
