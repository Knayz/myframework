<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 26.08.16
 * Time: 15:12
 */
namespace Brevis\Controllers;

use Brevis\Controller as Controller;


class Home extends  Controller{

    /**
     * Основной рабочий метод
     *
     * @return string
     */

    public function run()
    {
        //Метод getFenom() может вернуть false или объект
        //Так что нужно проверять, что именно приходит
        return $this->template('home', array(
            'pagetitle' => 'Тестовый сайт',
            'longtitle' => 'Третий курс обучения',
            'content' => 'Текст главной страницы курса обучения на bezumkin.ru'
        ), $this);
    }

    public function initialize(array $params = array())
    {
        if(!empty($_REQUEST['q'])){
            $this->redirect('/');
        }
        return true;
    }

}