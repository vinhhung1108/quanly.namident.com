<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
	'name' => 'Quản lý nha khoa',
	'defaultRoute' => 'site/index',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],	
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],	
    'charset'  => 'UTF-8',
'language' => 'vi-VN',
    'components' => [
          'response' => ['charset' => 'UTF-8'],
			'formatter' => [
			'class' => 'yii\i18n\Formatter',
			'dateFormat' => 'dd/mm/yyyy',
			'datetimeFormat' => 'php:d/m/Y H:i:s',
			'timeFormat' => 'php:H:i:s',
            'thousandSeparator' => ',',
		],
		'authManager' => [
			'class' => 'yii\rbac\DbManager',
		],
	
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'd13ba6742a5f1591fe1d8f44a4db3c4ecb73f885e583468ff03a88c747bf27fa',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => false,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],

        'imageProcessor' => [
            'class' => '\phtamas\yii2\imageprocessor\Component',
            'imagine' => new \Imagine\Imagick\Imagine(),
            // Mặc định áp cho mọi lần save()
            // Default for all JPEG images
            'jpegQuality' => 92,
            // Default for all PNG images
            'pngCompression' => 9,
            
            // Create named image categories with their own configuration.
            // You can refer them by name in application code.
            'define' => [
            
              'userAvatar' => [
                // Add transformations. They will be applied in the order they were defined.
                'process' => [
                  // Fix images with embedded orientation metadata
                  ['autorotate'],
                  // Preapre image to crop by resizing it to cover a 160*160 square
                  ['resize', 'width' => 160, 'height' => 160, 'scaleTo' => 'cover'],
                  // Crop it
                  ['crop', 'x' => 'center - 80', 'y' => 'center - 80', 'width' => 160, 'height' => 160],
                ],
              ],
              
              'galleryImage' => [
                 // Override default to save some disk space and bandwidth
                'process' => [
                  // Resize proportionally to fit a 700*600 square but only if too large
                  ['resize', 'width' => 1080,'height'=>1080, 'scaleTo' => 'fit', 'only' => 'down'],
                  // Mark your property
                //   ['watermark', 'path' => '@path/to/wmark.png', 'align' => 'top-left', 'margin' => 20],
                ],
                
              ],
              'logo' => [
                    'process' => [
                        ['resize', 'width' => 512, 'height' => 512, 'scaleTo' => 'fit', 'only' => 'down'],
                    ],
                    'options' => [
                        'jpeg_quality' => 90,
                        'png_compression_level' => 9,
                    ],
                ],
          
            ],
          ],
        
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['127.0.0.1', '::1','*'],
    ];
}

return $config;
