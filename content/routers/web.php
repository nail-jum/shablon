<?php

// Маршрутизатор запросов веб-страниц

$urlFirstSegment = $urlArray[0] ?? '';
if ($urlFirstSegment == '') {
    $urlFirstSegment = 'main';
}
/*
if ($urlFirstSegment != 'user_logon') {
    // Проверка авторизации пользователя
    //require_once $_SERVER['DOCUMENT_ROOT'] . '/autorization.php';
}
*/
// --------------------------------------------
// Обработка полученного запроса
// --------------------------------------------
$contentData = [];      // результаты, полученных от контроллера
$template = '';         // шаблон для вывода

switch ($urlFirstSegment) {

    //----------------------------------------
    case 'main':                    // Главная страница
        ControllerMainPage::run();
        break;

    //----------------------------------------
    case 'db':                      // Работа с базой данных
        ControllerDb::run();
        break;

    //----------------------------------------
    case 'app-info':                // Информация о запросе для теста
        //$app->contentData = $pageRequest;
        $app->template = 'app_debug.php';
        break;

    //----------------------------------------
    case 'test':                    // Страница для тестов
        //$app->contentData = $pageRequest;
        $app->template = 'test.php';
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
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/content/' . $urlFirstSegment . '.php')) {
            // если найден одноименный php-файл
            $template = $urlFirstSegment . '.php';
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
