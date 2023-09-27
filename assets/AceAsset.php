<?php

namespace imessage\assets;

class AceAsset extends WpAsset {
    public $sourcePath = YII_DEBUG ? "@bower/ace/src":"@bower/ace/src-min";
    public $css = [];
    public $js = [
        'ace.js'
    ];
    public $jsOptions=[
    ];
    public $depends =['yii\web\JqueryAsset'];
}