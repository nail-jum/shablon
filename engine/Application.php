<?php

class Application
{
    public $user;       // информация о пользователе
    public $url;        // информация о запрошенном адресе
    public $script;     // информация о выполняемом скрипте

    public $contentData = [];       // массив данных для передачи в шаблон
    public $template = '';          // html-шаблон для вывода в браузер

    public $errorMessages = [];     // массив сообщений об ошибках
    public $warningMessages = [];   // массив предупреждений
    public $debugMessages = [];     // массив сообщений для отладки

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

    public function templatePath()
    {
        if (strlen($this->template) == 0) {
            // не указан шаблон для вывода
            $this->template = 'page_not_exist.php';
        }
        return $_SERVER['DOCUMENT_ROOT'] . "/content/templates/{$this->template}";
    }

    public function debug($text)
    {
        // стек вызова
        $backtrace = debug_backtrace();
        $file = $backtrace[0]['file'];
        $function = $backtrace[0]['function'];

        $this->debugMessages[] = $function . ': ' . $text;
    }

    public function debugInfo()
    {
        return implode(PHP_EOL, $this->debugMessages);
    }

}
