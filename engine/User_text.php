<?php

/**
 * Данные о пользователе, отправившем запрос
 */

class User
{
    public $isInit = false;
    public $name = 'guest';
    public $groups = [];

    private $users = [
        'admin' => [
            'name'      => 'admin',
            'password'  => 'admin',
            'token'     => '111111',
            'groups'    => ['user', 'admin'],
        ],
        'user'  => [
            'name'      => 'user',
            'password'  => 'user',
            'token'     => '222222',
            'groups'    => ['user'],
        ],
    ];

    public function __construct()
    {
        // Проверка инициализации пользователя в сессии
        $this->isInit = $_SESSION['user']['isinit'] ?? false;
        if ($this->isInit != false) {
            $token = $_SESSION['user']['token'] ?? false;
        } else {
            $token = false;
        }
        // Проверка токена в куках
        if ($token == false) {
            $token = $_COOKIE['token'] ?? false;
        }
        // Получение данных о пользователе
        if ($token != false) {
            $this->logonByToken($token);
        }
    }

    public function logonByToken($token)
    {
        $user = false;
        foreach ($this->users as $row) {
            if ($row['token'] == $token) {
                $user = $row;
                break;
            }
        }
        $this->saveUser($user);
    }

    public function logon($login, $password)
    {
        $user = false;
        foreach ($this->users as $row) {
            if (($row['name'] == $login) && ($row['password'] == $password)) {
                $user = $row;
                break;
            }
        }
        $this->saveUser($user);
    }

    public function saveUser($user)
    {
        // Сохранение данных о пользователе
        if ($user != false) {
            $this->name   = $user['name'];
            $this->groups = $user['groups'];
            $this->isInit = true;
            // сохранение в сессии
            $_SESSION['user'] = [
                'isinit' => true,
                'token' => $user['token'],
            ];
            // установка токена в куках
            setcookie('token', $user['token'], time() + 259200);   // на 3 суток
        } else {
            $this->isInit = false;
        }
    }

    public function logout()
    {
        // удаление данных пользователя в сессии
        unset($_SESSION['user']);
        // удаление токена в куках
        setcookie('token', '0', time() - 1);
    }
}
