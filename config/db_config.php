<?php

const DB_CONF = [
    'users' => [
        'title' => 'Пользователи',
        'fields' => [
            'user_id'   => ['title' => 'id'                , 'type' => 'integer'],
            'name'      => ['title' => 'Имя пользователя'  , 'type' => 'text', 'notnull' => 1],
            'passwd'    => ['title' => 'Пароль'            , 'type' => 'text'],
        ],
        'primary_key' => 'user_id',
    ],
    'materials' => [
        'title' => 'Материалы (сырье)',
        'fields' => [
            'material_id'  => ['title' => 'id'                , 'type' => 'integer'],
            'name'         => ['title' => 'Наименование'      , 'type' => 'text'],
            'measure_unit' => ['title' => 'Единица измерения' , 'type' => 'text'],
            'description'  => ['title' => 'Описание'          , 'type' => 'text', 'default' => 'test'],
        ],
        'primary_key' => 'material_id',
    ],

    // - - - - - - - - - - - - - - - -
    // Справочники
    // - - - - - - - - - - - - - - - -

    'list_measure_units' => [
        'title' => 'Единицы измерения',
        'fields' => [
            'item_id'     => ['title' => 'id'           , 'type' => 'integer'],
            'name'        => ['title' => 'Код'          , 'type' => 'text'],
            'description' => ['title' => 'Наименование' , 'type' => 'text'],
        ],
        'primary_key' => 'item_id',
    ],
    'list_flaps_count' => [
        'title' => 'Количество створок',
        'fields' => [
            'item_id'     => ['title' => 'id'           , 'type' => 'integer'],
            'name'        => ['title' => 'Код'          , 'type' => 'text'],
            'description' => ['title' => 'Наименование' , 'type' => 'text'],
        ],
        'primary_key' => 'item_id',
    ],
    'list_locking_type' => [
        'title' => 'Типы запирания',
        'fields' => [
            'item_id'     => ['title' => 'id'           , 'type' => 'integer'],
            'name'        => ['title' => 'Код'          , 'type' => 'text'],
            'description' => ['title' => 'Наименование' , 'type' => 'text'],
        ],
        'primary_key' => 'item_id',
    ],
    'list_colors' => [
        'title' => 'Цвета',
        'fields' => [
            'item_id'     => ['title' => 'id'           , 'type' => 'integer'],
            'name'        => ['title' => 'Код'          , 'type' => 'text'],
            'description' => ['title' => 'Наименование' , 'type' => 'text'],
        ],
        'primary_key' => 'item_id',
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
        'fields' => ['name', 'description'],
        'data' => [
            ['пог. м', 'погонный метр'],
            ['м2', 'квадратный метр'],
            ['шт', 'штука'],
            ['кг', 'килограмм'],
        ],
    ],

    'list_flaps_count' => [
        'fields' => ['name', 'description'],
        'data' => [
            ['1', 'одностворчатый люк'],
            ['2', 'двустворчатый люк'],
        ],
    ],

    'list_locking_type' => [
        'fields' => ['name', 'description'],
        'data' => [
            ['1', 'нажимные защёлки'],
            ['2', 'с трёхгранным замком'],
            ['3', 'с почтовым замком'],
        ],
    ],
];

