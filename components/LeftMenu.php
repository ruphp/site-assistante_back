<?php

namespace app\components;

use Yii;
use yii\base\Widget;

class LeftMenu extends Widget
{

    public array $list = [];
    public array $lists = [];


    public function run(): void
    {

        $this->render('left_menu', ['list' => $this->list,'lists' => $this->lists,'pathInfo' =>Yii::$app->request->pathInfo]);

    }



}
