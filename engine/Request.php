<?php

// --------------------------------------------
// Параметры полученного запроса
// --------------------------------------------
class Request
{
    public $user;
    public $url;
    public $script;

    public function __construct()
    {
        // IP-адрес клиента
        $this->user['ip']       = $_SERVER['REMOTE_ADDR'];
        // ОС и браузер клиента
        $this->user['agent']    = $_SERVER['HTTP_USER_AGENT'];
        // адрес страницы, с которой был направлен клиент
        $this->user['referer']  = $_SERVER['HTTP_REFERER'] ?? '';

        $this->url['full']      = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        // протокол запроса (http, https)
        $this->url['scheme']    = $_SERVER['REQUEST_SCHEME'];
        // имя хоста
        $this->url['host']      = $_SERVER['HTTP_HOST'];
        // запрашиваемый адрес от корня домена с параметрами
        $this->url['uri']       = $_SERVER['REQUEST_URI'];
        // запрашиваемый адрес от корня домена без параметров
        $this->url['path']      = $_SERVER['REQUEST_URI'];
        $paramPosition = strpos($_SERVER['REQUEST_URI'], '?');
        if ($paramPosition !== false) {
            $this->url['path']  = substr($this->url['path'], 0, $paramPosition);
        }
        // массив сегментов запрашиваемого адреса (для маршрутизации)
        $this->url['pathArray'] = explode('/', trim($this->url['path'], '/'));
        // метод запроса (GET, POST и т.п.)
        $this->url['method']    = $_SERVER['REQUEST_METHOD'];
        // параметры, переданные скрипту в адресной строке
        $this->url['query']     = $_SERVER['QUERY_STRING'];
        // данные json-запроса, передаваемого методом POST
        $this->url['postContent']  = file_get_contents('php://input');
        // декодированный массив json-запроса
        $this->url['postArray'] = json_decode($this->url['postContent'], true) ?? [];

        // каталог корня сайта
        $this->script['root']       = $_SERVER['DOCUMENT_ROOT'];
        // путь к выполняемому скрипту от корня домена
        $this->script['name']       = $_SERVER['SCRIPT_NAME'];
        // абсолютный путь к основному файлу скрипта от корня диска
        $this->script['filename']   = $_SERVER['SCRIPT_FILENAME'];
        // каталог расположения выполняемого подключенного файла
        $this->script['dir']        = __DIR__;
        // абсолютный путь к подключенному файлу скрипта от корня диска
        $this->script['file']       = __FILE__;
    }

    static function url2array($url)
    // - - - - - - - - - - - - - - - - - - - -
    // Выделяет из url часть до знака ? и параметры, разделенные знаками &
    // Параметры после знака ? возвращает в виде массива ключ:значение
    // Если параметр без значения (без знака =), возвращается ключ:null
    // - - - - - - - - - - - - - - - - - - - -
    {
        $pos1 = mb_strpos($url, '?');
        //if ($pos1 === false || $pos1 === (mb_strlen($url) - 1)) {
        if ($pos1 === false) {
            // параметры отсутствуют
            return [
                //'link' => rtrim($url, '?'),
                'link' => $url,
                'params' => [],
                'paramString' => '',
            ];
        }
        // отделяем параметры
        list($urlLink, $urlParams) = explode('?', $url, 2);
        $params = explode('&', $urlParams);
        $paramList = [];
        foreach ($params as $param) {
            if (strpos($param, '=') != false) {
                list($key, $value) = explode('=', $param, 2);
            } else {
                $key = $param;
                $value = null;
            }
            $key = trim($key);
            if (strlen($key) == 0) {
                continue;
            }
            $paramList[$key] = $value;

        }
        return [
            'link' => $urlLink,
            'paramString' => $urlParams,
            'params' => $paramList,
        ];
    }

    // - - - - - - - - - - - - - - - - - - - -
    // Формирует url из массива, подобного возвращаемой функцией url2array()
    // - - - - - - - - - - - - - - - - - - - -
    static function array2url($array)
    {
        if (!isset($array['link'])) {
            return false;
        }
        if (!isset($array['params']) || count($array['params']) == 0) {
            return $array['link'];
        }
        $paramList = [];
        foreach ($array['params'] as $key => $value) {
            if (is_null($value)) {
                $paramList[] = $key;
            } else {
                $paramList[] = $key . '=' . $value;
            }
        }
        return $array['link'] . '?' . implode('&', $paramList);
    }
}

/*
// Пример работы класса Request
{
    "user": {
        "ip": "127.0.0.1",
        "agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/115.0",
        "referer": "http://vergusutils.local/ms-api-test/?param=2"
    },
    "request": {
        "full": "http://vergusutils.local/api/request-info/test?param=value&order=1",
        "scheme": "http",
        "host": "vergusutils.local",
        "uri": "/api/request-info/test?param=value&order=1",
        "path": "/api/request-info/test",
        "pathArray": [
            "api",
            "request-info",
            "test"
        ],
        "method": "GET",
        "query": "param=value&order=1",
        "postContent": "",
        "postArray": []
    },
    "script": {
        "name": "/api/index.php",
        "filename": "D:/xampp/htdocs/vergusutils/api/index.php",
        "dir": "D:\\xampp\\htdocs\\vergusutils\\api\\lib",
        "file": "D:\\xampp\\htdocs\\vergusutils\\api\\lib\\Request.php"
    }
}
*/