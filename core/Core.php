<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 26.08.16
 * Time: 14:36
 */

class Core{
    public $config = array();

    /**
     * Конструктор класса
     *
     * @param array $config
     */

    public function __construct(array $config = array())
    {
        $this->config = array_merge(
            array(
                'controllersPath' => __DIR__ . '/controllers/',
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
}