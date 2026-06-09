<?php

namespace app\Presentation\Yii\Widget;

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
