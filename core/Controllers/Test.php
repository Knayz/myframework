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
        //Метод getFenom() может вернуть false или объект
        //Так что нужно проверять, что именно приходит
        if($fenom = $this->core->getFenom()){
            return $fenom->fetch('home.tpl', array(
                'pagetitle' => 'Тестовая страница',
                'longtitle' => '',
                'content' => 'Текст тестовой страницы курса обучения на bezumkin.ru'
            ));
        }
        else{
            return '';
        }
    }

}