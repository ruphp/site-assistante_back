<?php
/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 10.04.2016
 * Time: 10:34
 */

namespace app\components;

use yii\base\Widget;

class MyWidget extends Widget
{

    public $name;

    public function init()
    {
        parent::init();
//        if( $this->name === null ) $this->name = 'Гость';
        ob_start();// буферизируем
    }

    public function run()
    {
        $content = ob_get_clean(); // поместим данные из буфера
        $content = mb_strtoupper($content, 'utf-8');
        return $this->render('my', compact('content'));
//        return $this->render('my', ['name' => $this->name]);
    }

} 