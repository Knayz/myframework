<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 26.08.16
 * Time: 15:15
 */

class Controllers_Test{
    /**
     * @var Core core
     */
    public $core;

    /**
     * Конструктор, требует передачи Core
     *
     * @param Core $core
     */

    public function __construct(Core $core)
    {
        $this->core = $core;
    }

    /**
     * Основной рабочий метод
     *
     * @return string
     */

    public function run()
    {
        return "Мы выводим страницу <b>Test</b>";
    }

}