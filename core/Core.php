<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 26.08.16
 * Time: 14:36
 */

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
                'controllersPath' => __DIR__ . '/Controllers/',
                'templatesPath' => __DIR__ . '/Templates/',
                'cachePath' => __DIR__ . '/Cache/',
                'fenomOptions' => array(
                    'auto_load' => true
                )
            ), $config
        );
    }

    public function handleRequest($uri)
    {
        //Если запрос не пуст - проверяем, есть ли он в массиве наших страниц
        $request = explode('/', $uri);
        //Имена контроллера с большой буквы
        $name = ucfirst(array_shift($request));
        //Полный путь до запрошенного контроллера
        $file = $this->config['controllersPath'] . $name . '.php';
        // Если нужного контроллера нет, то используем контроллер Home
        if(!file_exists($file)){
            $file = $this->config['controllersPath'] . 'Home.php';
            // Определяем имя класса, согласно принятым у нас правилам
            $class = 'Controllers_Home';
        }
        else{
            $class = 'Controllers_' . $name;
        }

        // Если контроллер еще не был загружен - загружаем его
        if(!class_exists($class))
            require_once $file;

        // И запускаем
        /** @var Controllers_Home|Controllers_Test $controller */
        $controller = new $class($this);
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
                //Подключаем класс загрузки
                if(!class_exists('Fenom')){
                    require 'Fenom.php';
                    //Регистрируем остальные классы его методом
                    Fenom::registerAutoload();
                }
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