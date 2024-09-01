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
            'code'  => ['title' => 'Код'          , 'type' => 'text'],
            'name'  => ['title' => 'Наименование' , 'type' => 'text'],
        ],
    ],
    'list_flaps_count' => [
        'title' => 'Количество створок',
        'fields' => [
            'code'  => ['title' => 'Код'          , 'type' => 'text'],
            'name'  => ['title' => 'Наименование' , 'type' => 'text'],
        ],
    ],
    'list_locking_type' => [
        'title' => 'Типы запирания',
        'fields' => [
            'code'  => ['title' => 'Код'          , 'type' => 'text'],
            'name'  => ['title' => 'Наименование' , 'type' => 'text'],
        ],
    ],
    'list_colors' => [
        'title' => 'Цвета',
        'fields' => [
            'code'  => ['title' => 'Код'          , 'type' => 'text'],
            'name'  => ['title' => 'Наименование' , 'type' => 'text'],
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
        'fields' => ['code', 'name'],
        'data' => [
            ['пог. м', 'погонный метр'],
            ['м2', 'квадратный метр'],
            ['шт', 'штука'],
            ['кг', 'килограмм'],
        ],
    ],

    'list_flaps_count' => [
        'fields' => ['code', 'name'],
        'data' => [
            ['1', 'одностворчатый люк'],
            ['2', 'двустворчатый люк'],
        ],
    ],

    'list_locking_type' => [
        'fields' => ['code', 'name'],
        'data' => [
            ['1', 'нажимные защёлки'],
            ['2', 'с трёхгранным замком'],
            ['3', 'с почтовым замком'],
        ],
    ],
];

