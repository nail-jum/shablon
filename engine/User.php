<?php

/**
 * Данные о пользователе, отправившем запрос
 * Идентификация - получение идентификатора (имени, токена и т.п.)
 * Аутентификация - подтверждение идентификатора
 * Авторизация - определение прав пользователя на контент
 *
 * Алгоритм идентификации
 * 1) При инициализации объекта проверяется наличие токена в сессии
 * 2) При отсутствии токена в сессии проверяется наличие токена в куках
 * 3) Если токен найден
 *  3.1) по нему делается запрос к таблице пользователей
 *  3.2) Запрос возвращает имя пользователя, соответствующего токену
 *       (добавить перегенерацию токена и его проверку на срок действия)
 *  3.4) Если пользователь найден, делается запрос списка групп, в которых он состоит
 * 4) Если токен не найден или не найден пользователь, соответствующий токену
 *  4.1) Ошибка идентификации
 *
 * При ошибке идентификации:
 * а) сохранение информации о запрошенной странице
 * б) перенаправление на страницу по умолчанию (может быть страницей входа в систему)
 *
 * Алгоритм аутентификации (обработка страницы входа в систему)
 * 1) Запрос к таблице пользователей с логином и хешированным паролем
 * 2) Если запись пользователя найдена:
 *  2.1) Записать полученный токен в сессию и в куки
 *  2.2) Запрос списка групп, в которых состоит пользователь
 *
 * Перед обработкой запрошенной страницы ее группа доступа сверяется с группами пользователя
 */

class User
{
    public $isInit = false;
    public $name = 'guest';
    public $groups = [];

    public function __construct($mode = 'web')
    {
        switch ($mode) {
            case 'web':
                // Для веб-запросов
                $token = $_SESSION['user']['token'] ?? $token = $_COOKIE['user-token'] ?? false;
                break;
            case 'api':
                // Для запросов API
                $token = $_POST['user-token'] ?? false;
                break;
        }
        // Получение данных о пользователе
        if ($token != false) {
            $this->userByToken($token);
        }
    }

    public function userByToken($token)
    {
        $sql = "SELECT * FROM \"users\" WHERE \"token\" = '$token'";
        $request = Db::query($sql);
        if ($request != false) {
            $user = $request->fetch(PDO::FETCH_ASSOC);
            $this->saveUser($user);
        } else {
            //$app->debug($sql);
            $this->log("Токен $token не найден.");
        }
    }

    public function logon($login, $password)
    {
        global $app;
        $sql = "SELECT * FROM \"users\" WHERE \"login\" = '$login' AND \"password\" = $password";
        $request = Db::query($sql);
        if ($request != false) {
            $user = $request->fetch(PDO::FETCH_ASSOC);
            $this->saveUser($user);
        } else {
            //$app->debug($sql);
            $this->log("Пользователь $login/$password не найден.");
        }

    }

    public function saveUser($user)
    {
        // Сохранение данных о пользователе
        if ($user != false) {
            $this->name   = $user['name'];
            //$this->groups = $user['groups'];
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

    private function log($text)
    {
        $logFile = _LOG_DIR_ . '/user_' . date('Y-m-d');
        file_put_contents($logFile, date('d.m.Y H:i ') . $text);
    }
}
