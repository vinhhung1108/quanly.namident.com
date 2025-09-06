<?php
/**
 * @link http://www.yiiframework.com/
 * @ 
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css?v=0.6',
		'css/thuchi.css',
        [
            'href' => '/images/IMG_8504.PNG',
            'rel' => 'icon',
            'sizes' => '512x512',
        ],
    ];
    public $js = [	
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
