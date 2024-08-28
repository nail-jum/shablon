<?php

/**
 * Глобальные константы
 */
const INTERFACE_PATH = 'interfaces/';

// путь к каталогу с библиотеками
define('LIBRARY_PATH', $_SERVER['DOCUMENT_ROOT'] . '/engine/');

// путь к каталогу с контроллерами
define('CONTROLLER_PATH', $_SERVER['DOCUMENT_ROOT'] . '/content/controllers/');

// путь к каталогу с моделями
define('MODELS_PATH', $_SERVER['DOCUMENT_ROOT'] . '/content/models/');

// путь к каталогу с маршрутизаторами
define('ROUTERS_PATH', $_SERVER['DOCUMENT_ROOT'] . '/content/routers/');

// Каталог для лог файлов
define('_LOG_DIR_', $_SERVER['DOCUMENT_ROOT'] . '/log');

// Каталог для рабочих файлов
define('_FILE_DIR_', $_SERVER['DOCUMENT_ROOT'] . '/files');
define('_FILE_DIR_URL_', '/files');

// Проверка существования каталогов
checkDir(_LOG_DIR_);
checkDir(_FILE_DIR_);
// ---------------------------------------------------
// время, доступное для работы скрипта (в секундах)
define('SCRIPT_TIME_LIMIT', 600);
//set_time_limit(SCRIPT_TIME_LIMIT);

// максимально допустимый размер массивов
define('ARRAY_SIZE_LIMIT', 100000);

// ---------------------------------------------------
// Автозагрузка классов
function libraryLoader($className)
{
    $filename1 = LIBRARY_PATH . $className . '.php';
    $filename2 = CONTROLLER_PATH . $className . '.php';
    $filename3 = MODELS_PATH . $className . '.php';
    if (file_exists($filename1)) {
        require_once $filename1;
    } elseif (file_exists($filename2)) {
        require_once $filename2;
    } elseif (file_exists($filename3)) {
        require_once $filename3;
    }
}

function interfaceLoader($interfaceName)
{
    $filename = INTERFACE_PATH . $interfaceName . '.php';
    if (file_exists($filename)) {
        require_once $filename;
    }
}

spl_autoload_register('libraryLoader');
spl_autoload_register('interfaceLoader');

//include_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
// ---------------------------------------------------

function scriptTimeOut() {
    return false;   // для тестирования
    // Функция контроля времени работы скрипта
    if (time() - $_SERVER['REQUEST_TIME'] > SCRIPT_TIME_LIMIT) {
        return true;
    }
    return false;
}

// Проверка наличия каталога и попытка его создания
function checkDir($path)
{
    if (file_exists($path)) {
        return true;
    }
    $path = trim( mb_substr($path, mb_strlen($_SERVER['DOCUMENT_ROOT'])), '/');
    $pathArray = explode('/', $path);
    $pathStep = $_SERVER['DOCUMENT_ROOT'] . '/' . $pathArray[0];
    for ($ii = 1; $ii < count($pathArray); ++$ii) {
        $pathStep .= '/' . $pathArray[$ii];
        if (!file_exists($pathStep)) {
            if (mkdir($pathStep) === false) {
                return false;
            }
        }
    }
    return true;
}
