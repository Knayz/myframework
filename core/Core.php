<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 26.08.16
 * Time: 14:36
 */
namespace Brevis;

use \Fenom as Fenom;
use \Exception as Exception;
use \xPDO\xPDO as xPDO;

class Core{
    public $config = array();
    public $fenom;
    public $xpdo;

    /**
     * Конструктор класса
     *
     * @param array $config
     */

    public function __construct($config = 'config')
    {
        if(is_string($config)){
            $config = __DIR__ . "/Config/{$config}.inc.php";
            if(file_exists($config)){
                require_once $config;
                /** @var string $database_dsn */
                /** @var string $database_user */
                /** @var string $database_password */
                /** @var array $database_options */
                try{
                    $this->xpdo = new xPDO($database_dsn, $database_user, $database_options);
                    $this->xpdo->setPackage(PROJECT_NAME, PROJECT_MODEL_PATH);
                    $this->xpdo->startTime = microtime(true);
                }
                catch(Exception $e){
                    exit($e->getMessage());
                }
            }
            else{
                exit('Не могу загрузить файл конфигурации');
            }
        }
        else{
            exit('Неправильное имя файла конфигурации');
        }


    }

    public function handleRequest($uri)
    {
        //Если запрос не пуст - проверяем, есть ли он в массиве наших страниц
        $request = explode('/', $uri);

        //Имена контроллера с большой буквы
        $className = '\Brevis\Controllers\\' . ucfirst(array_shift($request));

        // Если нужного контроллера нет, то используем контроллер Home
        /** @var Controller $controller */
        if(!class_exists($className)){
            $controller = new Controllers\Home($this);
        }
        else{
            $controller = new $className($this);
        }

        $initialized = $controller->initialize($request);
        if($initialized === true){
            $response = $controller->run();
        }
        elseif(is_string($initialized)){
            $response = $initialized;
        }
        else
            $response = "Возникла неведомая ошибка при загрузке страницы";

        echo $response;
    }

    public function getFenom()
    {
        //Работает если переменная класса пуста
        if(!$this->fenom){
            //Пробуем загрузить шаблонизатор
            //Все выброшенные исклы внутри этого блока будут пойманы в следующем
            try{

                //Проверяем и создаем директорию для кэширования скомпилированных шаблонов
                if(!file_exists(PROJECT_CACHE_PATH)){
                    mkdir(PROJECT_CACHE_PATH);
                }

                //Запускаем Fenom
                $this->fenom = Fenom::factory(PROJECT_TEMPLATES_PATH, PROJECT_CACHE_PATH);
            }
            //Ловим исключения и отправляем их в лог
            catch(Exception $e){
                $this->log($e->getMessage());
                //Возвращаем false
                return false;
            }
        }
        return $this->fenom;
    }

    /**
     * Метод удаления директории с кэшем
     *
     */
    public function clearCache() {
        self::rmDir(PROJECT_CACHE_PATH);
        mkdir(PROJECT_CACHE_PATH);
    }
    /**
     * Рекурсивное удаление директорий
     *
     * @param $dir
     */
    public static function rmDir($dir) {
        $dir = rtrim($dir, '/');
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != '.' && $object != '..') {
                    if (is_dir($dir . '/' . $object)) {
                        self::rmDir($dir . '/' . $object);
                    }
                    else {
                        unlink($dir . '/' . $object);
                    }
                }
            }
            rmdir($dir);
        }
    }

    /**
     * Логирование. Пока просто выводит ошибку на экран.
     *
     * @param $message
     * @param $level
     */
    public function log($message, $level = E_USER_ERROR)
    {
        trigger_error($message, $level);
    }

    /**
     * Удаление ненужных файлов в пакетах, установленных через Composer
     *
     * @param mixed $base
     */
    public static function cleanPackages($base = '') {
        if (!is_string($base)) {
            $base = dirname(dirname(__FILE__)) . '/vendor/';
        }
        if ($dirs = @scandir($base)) {
            foreach ($dirs as $dir) {
                if (in_array($dir, array('.', '..'))) {
                    continue;
                }
                $path = $base . $dir;
                if (is_dir($path)) {
                    if (in_array($dir, array('tests', 'test', 'docs', 'gui', 'sandbox', 'examples', '.git'))) {
                        Core::rmDir($path);
                    }
                    else {
                        Core::cleanPackages($path . '/');
                    }
                }
                elseif (pathinfo($path, PATHINFO_EXTENSION) != 'php') {
                    unlink($path);
                }
            }
        }
    }
}