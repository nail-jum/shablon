<?php

// Маршрутизатор запросов API
// запросы начинаются с /api/
//$urlFirstSegment = $urlArray[0] ?? '';
$urlSecondSegment = $urlArray[1] ?? '';

// --------------------------------------------
// Обработка полученного запроса
// --------------------------------------------
$contentData = [];      // результаты, полученных от контроллера
$template = '';         // шаблон для вывода

switch ($urlSecondSegment) {

    //----------------------------------------
    case 'db-load':                 // Главная страница
        ControllerDb::loadFromExcel();
        /*
        $app->contentData = [
            'status' => 'error',
            'data' => $app->url['path'],
            'info' => ['Не удалось прочитать файл'],
        ];
        */
        break;

    //----------------------------------------
    default:
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/content/' . $urlFirstSegment . '.php')) {
            // если найден одноименный php-файл
            $template = $urlFirstSegment . '.php';
        }
        break;
};


//---------------------------------------------------
// Возвращение результатов
//---------------------------------------------------
/*
$app->contentData = [
    'status' => 'error',
    'data' => $app->url['path'],
    'info' => ['Не удалось прочитать файл'],
];
*/
echo json_encode($app->contentData);
