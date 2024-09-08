<?php
$mainMenu = [
    ['title' => 'Главная'               , 'link' => '/'],
    ['title' => 'Заказы'                , 'link' => '/orders'],
    ['title' => 'Спецификации'          , 'link' => '/specifications'],
    ['title' => 'Материалы'             , 'link' => '/materials'],
    ['title' => 'Справочники'           , 'link' => '/selectlists', 'second-menu' => 'selectlistMenu'],
    ['title' => 'База данных'           , 'link' => '/db/info'],
    ['title' => 'App info'              , 'link' => '/app-info/test?d=456'],
];

$tableMenu = [
    ['title' => 'Пользователи'      , 'link' => '/db_table_edit.php?table=users'],
    ['title' => 'Материалы'         , 'link' => '/db_table_edit.php?table=materials'],
];

$selectlistMenu = [
    ['title' => 'Единицы измерения'     , 'link' => '/specifications/list_measure_units'],
    ['title' => 'Количество створок'    , 'link' => '/specifications/list_flaps_count'],
    ['title' => 'Типы запирания'        , 'link' => '/specifications/list_locking_type'],
    ['title' => 'Цвета'                 , 'link' => '/specifications/list_colors'],
];

// запрашиваемая страница с параметрами
$requestUri = $_SERVER['REQUEST_URI'];
$requestPage = cutParams($requestUri);
$secondMenuName = false;

// Вывод основного меню
echo '<div class="main-menu">';
    foreach ($mainMenu as $item) {
        if (cutParams($item['link']) == $requestPage) {
            // Пункт меню текущей страницы
            echo '<div><span class="menu-active-item">' . $item['title'] . '</span></div>';
            // Проверка наличия дополнительного меню
            $secondMenuName = $item['second-menu'] ?? false;
        } else {
            echo '<div><a href="' . $item['link'] . '">' . $item['title'] . '</a></div>';
        }
    }
    //echo '<div>' . $requestUri . '</div>';
    //echo '<div>' . $requestPage . '</div>';
echo '</div>';

// Вывод дополнительного меню
if (($secondMenuName != false) && isset($$secondMenuName)) {
    echo '<div class="main-menu">';
    foreach ($$secondMenuName as $item) {
        if ($item['link'] == $requestUri) {
            // Пункт меню текущей страницы
            echo '<div><span class="menu-active-item">' . $item['title'] . '</span></div>';
        } else {
            echo '<div><a href="' . $item['link'] . '">' . $item['title'] . '</a></div>';
        }
    }
    echo '</div>';
}

// Обрезает параметры ссылки
function cutParams($link)
{
    $url = explode('?', $link);
    return $url[0];
}
