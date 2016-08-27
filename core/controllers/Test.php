<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 26.08.16
 * Time: 15:15
 */

if (!class_exists('Controller')) {
    require_once dirname(dirname(__FILE__)) . '/Controller.php';
}

class Controllers_Test extends Controller{

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