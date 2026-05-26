<?php

namespace app\widgets;

use Yii;
use yii\helpers\Html;

/**
 * Alert widget renders a message from session flash. All flash messages are displayed
 * in the sequence they were assigned using setFlash. You can set message as following:
 *
 * ```php
 * Yii::$app->session->setFlash('error', 'This is the message');
 * Yii::$app->session->setFlash('success', 'This is the message');
 * Yii::$app->session->setFlash('info', 'This is the message');
 * ```
 *
 * Multiple messages could be set as follows:
 *
 * ```php
 * Yii::$app->session->setFlash('error', ['Error 1', 'Error 2']);
 * ```
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @author Alexander Makarov <sam@rmcreative.ru>
 */
class Alert extends \yii\bootstrap\Widget
{
    public $alertTypes = [
        'error'   => 'uk-alert-danger',
        'danger'  => 'uk-alert-danger',
        'success' => 'uk-alert-success',
        'info'    => 'uk-alert-primary',
        'warning' => 'uk-alert-warning',
    ];

    public $options;

    public $type;

    public $closeButton = [];

    public function init()
    {
        parent::init();

        $session = \Yii::$app->getSession();

        if (!empty($this->options['data']) && is_array($this->options['data'])) {
            $this->options['data']['uk-alert'] = true;
        }
        else {
            $this->options['data'] = ['uk-alert'=>''];
        }

        $this->options['id'] = $this->getId();

        if (isset($this->options['class'])) {
            $this->options['class'] .= ' uk-alert';
        }
        else {
            $this->options['class'] = 'uk-alert';
        }

        if ($this->type === null) {

            $flashes = $session->getAllFlashes();

            foreach ($flashes as $type => $data) {
                if (isset($this->alertTypes[$type])) {
                    $data = (array) $data;
                    foreach ($data as $message) {
                        $this->options['class'] = $this->alertTypes[$type];
                        echo Html::beginTag('div',$this->options);
                        echo Html::a('', $url = null, ['class' => 'uk-alert-close uk-close','uk-icon' => 'uk-close','uk-close'=>'']);
                        echo $message;
                        echo Html::endTag('div');
                    }

                    $session->removeFlash($type);
                }
            }

        }
        elseif(($message = $session->getFlash($this->type)) !== null) {
            echo Html::beginTag('div',$this->options);
            echo Html::a('', $url = null, ['class' => 'uk-alert-close uk-close','uk-icon' => 'uk-close','uk-close'=>'']);
            echo $message;
            echo Html::endTag('div');
        }
    }
}
