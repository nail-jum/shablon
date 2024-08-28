<?php

const DB_CONF = [
    'users' => [
        'title' => 'Пользователи',
        'fields' => [
            'name'   => ['title' => 'Имя пользователя'  , 'type' => 'text', 'notnull' => 1],
            'passwd' => ['title' => 'Пароль'            , 'type' => 'text'],
        ],
    ],
    'materials' => [
        'title' => 'Материалы (сырье)',
        'fields' => [
            'name'        => ['title' => 'Наименование' , 'type' => 'text'],
            'unit'        => ['title' => 'Единица измерения' , 'type' => 'text'],
            'description' => ['title' => 'Описание'     , 'type' => 'text', 'default' => 'test'],
        ],
    ],

    // - - - - - - - - - - - - - - - -
    // Справочники
    // - - - - - - - - - - - - - - - -

    'list_measure_units' => [
        'title' => 'Единицы измерения',
        'fields' => [
            'name'        => ['title' => 'Наименование' , 'type' => 'text'],
        ],
    ],
    'list_flaps_count' => [
        'title' => 'Количество створок',
        'fields' => [
            //'name'        => ['title' => 'Наименование' , 'type' => 'int'],
            'name'        => ['title' => 'Наименование' , 'type' => 'text'],
        ],
    ],
    'list_locking_type' => [
        'title' => 'Типы запирания',
        'fields' => [
            'name'        => ['title' => 'Наименование' , 'type' => 'text'],
        ],
    ],
    'list_colors' => [
        'title' => 'Цвета',
        'fields' => [
            'name'        => ['title' => 'Наименование' , 'type' => 'text'],
            'description' => ['title' => 'Описание'     , 'type' => 'text'],
        ],
    ],
];

//measure unit

const DB_DEFAULT_DATA = [
    'users' => [
        'fields' => ['name', 'passwd'],
        'data' => [
            ['admin', 'admin'],
            ['user', 'user'],
        ],
    ],

    'list_measure_units' => [
        'fields' => ['name'],
        'data' => [
            ['пог. м'],
            ['м2'],
            ['шт'],
            ['кг'],
        ],
    ],

    'list_flaps_count' => [
        'fields' => ['name'],
        'data' => [
            ['одностворчатый люк'],
            ['двустворчатый люк'],
        ],
    ],

    'list_locking_type' => [
        'fields' => ['name'],
        'data' => [
            ['нажимные защёлки'],
            ['с трёхгранным замком'],
            ['с почтовым замком'],
        ],
    ],
];

