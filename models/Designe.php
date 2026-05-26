<?php

namespace app\models;

use app\helpers\ManagerHelpers;
use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "params".
 *
 * @property int $id
 * @property int $public_key
 * @property string $design
 * @property string $domain
 * @property int $run
 * @property int $tab_tickets
 * @property int $leftbutton
 * @property string $default_answer
 * @property int $timeout
 * @property int $is_uuid
 */


class Designe extends Model
{

    public string $custom_css;
    public string $logo_svg;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['logo_svg','custom_css'], 'string'],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'logo_svg'         => 'Загрузка логотипа (svg - максимальный размер 40*40 )',
            'custom_css'         => 'Дополнительные стили (css)'
        ];
    }



    public function getCustomCss(): false|string
    {
        $this->custom_css =  ManagerHelpers::getCustomCssCode(Yii::$app->user->identity->getPublicKey())??'';
        return $this->custom_css;
    }

    public function getLogoSvg(): false|string
    {
        $this->logo_svg = ManagerHelpers::getLogoSvgCode(Yii::$app->user->identity->getPublicKey())??'';
        return $this->logo_svg;
    }

    public function setCustomCss(string $custom_css): void
    {
        Yii::$app->session->addFlash('warning', 'css');
        $this->custom_css = $custom_css;
        $custom_css_file = __DIR__ . '/../web/custom/custom_'.Yii::$app->user->identity->getPublicKey().'.css';
        if(!empty(trim($this->custom_css))){
            file_put_contents($custom_css_file, $this->custom_css);
        }
    }

    public function setLogoSvg(string $logo_svg): void
    {
        Yii::$app->session->addFlash('warning', 'svg');
        $this->logo_svg = $logo_svg;
        $logo_svg_file = __DIR__ . '/../web/custom/logo'.Yii::$app->user->identity->getPublicKey().'.svg';
        if (!self::isSvg($this->logo_svg)) {
            Yii::$app->session->addFlash('warning', 'не верный формат svg');
        }
        else if(!empty(trim($this->logo_svg))){
            file_put_contents( $logo_svg_file, $this->logo_svg);
        }
    }

    public static function isSvg($content): bool
    {
        if (str_starts_with($content, '<svg') !== false && str_ends_with($content, '</svg>') !== false) {
            return true;
        } else {
            return false;
        }
    }

}
