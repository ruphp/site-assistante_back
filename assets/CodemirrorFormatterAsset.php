<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2021
 * @package yii2-editors
 * @version 1.0.1
 */

namespace app\assets;

use kartik\editors\assets;
/**
 * Asset bundle for loading code mirror code formatter assets from CodeMirror CDN.
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class CodemirrorFormatterAsset extends assets\BaseAsset
{
    /**
     * @inheritdoc
     */
    public $depends = [
        'app\assets\CodemirrorAsset'
    ];
    //public $bsDependencyEnabled = false;

    /**
     * @inheritdoc
     */
    public $baseUrl = '@web/2.36.0';

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->setupAssets('js', ['formatting']);
        parent::init();
    }
}
