<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2021
 * @package yii2-editors
 * @version 1.0.1
 */

namespace app\assets;

use kartik\editors\assets;
/**
 * Asset bundle for loading code mirror core library assets from Codemirror CDN.
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class CodemirrorAsset extends assets\BaseAsset
{
    /**
     * @inheritdoc
     */
    public $baseUrl = '@web/5.61.1';
    public $js = [
        'codemirror.js',
    ];
    public $css = [
        'codemirror.css',
    ];
    /**
     * @inheritdoc
     */
    public function includeLibraries($files = [])
    {
        if (!empty($files)) {
            foreach ($files as $file) {
                if (substr($file, -3) === '.js') {
                    $this->js[] = $file;
                } elseif (substr($file, -4) === '.css') {
                    $this->css[] = $file;
                }
            }
        }
        return $this;
    }

    public $bsDependencyEnabled = false;
}
