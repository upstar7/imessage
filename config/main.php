<?php

return [
        'id' => 'imessage',
        'name' => 'imessage',
        'basePath' => dirname(__DIR__),
        'defaultRoute' => "index",
        'controllerNamespace' => 'imessage\controllers',
        'bootstrap' => ['log'],
        'language' => 'zh-CN',
        'aliases' => [
            '@bower' => '@vendor/bower-asset',
            '@npm'   => '@vendor/npm-asset',
        ],
        'vendorPath' => dirname(__DIR__). '/vendor',
        'components' => [
            // 不在需要layout,视图布局文件由wordpress决定
            'controller'=>[
                'class'=>" yii\web\Controller",
                'layout'=>false,
            ],

            "okx"=>[
                'class'=>"imessage\components\OkxComponent",
                'APIKey'=>get_option('imessage_group_okx_APIKey',''),
                'SecretKey'=>get_option('imessage_group_okx_SecretKey',''),
                'Passphrase'=>get_option('imessage_group_okx_Passphrase',''),
            ],
            "view"=>[
                "class"=>"imessage\components\View"
            ],
            'response'=>[
                'class'=>"imessage\components\Response"
            ],
            "cache" => [
                'class' => "yii\caching\FileCache",
                "cachePath" => "@runtime/cache"
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
            "urlManager" => [
                "routeParam"=>"page",
                "rules" => [
                    "<controller:[\w-]+>/<action:[\w-]+>/<id:\d+>"=>"<controller>/<action>",
                    "<controller:[\w-]+>/<action:[\w-]+>"=>"<controller>/<action>",
                ]
            ],
            'errorHandler' => [
                "class"=>"imessage\components\ErrorHandler",
                'errorAction' => 'index/error',
            ],
            "assetManager"=>[
                // 定义资源包发布目录， you project/wp-content/uploads/assets
                'class' => 'yii\web\AssetManager',
                "basePath" => '@uploads/assets',
                "baseUrl" => '/wp-content/uploads/assets',
            ],
        ],
        'params' => yii\helpers\ArrayHelper::merge(
            require __DIR__ . '/params.php',
            require __DIR__ . '/params-local.php'
        ),
    ];


