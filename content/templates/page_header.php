<?php

$weekDays = [ 1 => 'понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота', 'воскресенье' ];

?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <title>Тестовый сайт</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="/content/css/main.css?9">
    <link rel="stylesheet" type="text/css" href="/content/css/menu.css?4">
    <script src="/content/js/jquery-3.6.3.min.js"></script>
    <link rel="icon" href="/favicon.ico">
</head>

<body>
    <header class="common">
        <div class="header-logo">
            <a href="/"><img src="/img/logo.gif" alt=""></a>
        </div>
        <div class="header-title"></div>
        <div class="header-info">
            <?= date('d.m.Y') . '<br>' . $weekDays[ date('N') ]; ?>
        </div>
    </header>

    <main>
        <?php
        // Подключение главного меню
        include_once('templates/page_menu.php');
        ?>