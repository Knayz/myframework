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

class Core{
    public $config = array();
    public $fenom;

    /**
     * Конструктор класса
     *
     * @param array $config
     */

    public function __construct(array $config = array())
    {
        $this->config = array_merge(
            array(
                'templatesPath' => __DIR__ . '/Templates/',
                'cachePath' => __DIR__ . '/Cache/',
                'fenomOptions' => array(
                    'auto_load' => true,
                    'force_verify' => true,
                )
            ), $config
        );

    }

    public function handleRequest($uri)
    {
        //Если запрос не пуст - проверяем, есть ли он в массиве наших страниц
        $request = explode('/', $uri);

        //Имена контроллера с большой буквы
        $className = '\Brevis\Controllers\\' . ucfirst(array_shift($request));

        // Если нужного контроллера нет, то используем контроллер Home
        if(!class_exists($className)){
            $controller = new Controller\Home($this);
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
                if(!file_exists($this->config['cachePath'])){
                    mkdir($this->config['cachePath']);
                }

                //Запускаем Fenom
                $this->fenom = Fenom::factory($this->config['templatesPath'], $this->config['cachePath']);
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
        $this->rmDir($this->config['cachePath']);
        mkdir($this->config['cachePath']);
    }
    /**
     * Рекурсивное удаление директорий
     *
     * @param $dir
     */
    public function rmDir($dir) {
        $dir = rtrim($dir, '/');
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != '.' && $object != '..') {
                    if (is_dir($dir . '/' . $object)) {
                        $this->rmDir($dir . '/' . $object);
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
}