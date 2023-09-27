<?php

namespace imessage\assets;

class VueAsset extends WpAsset
{
    public $sourcePath =  "@bower/vue/dist";
    public $css = [];
    public $js = [
        (YII_DEBUG)?  'vue.min.js':"vue.js"
    ];
    public $jsOptions=[];
    public $depends = ['yii\web\JqueryAsset'];

}