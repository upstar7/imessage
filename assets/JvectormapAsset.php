<?php

namespace imessage\assets;

class JvectormapAsset extends WpAsset
{
    public $sourcePath =  "@bower/jvectormap-maps-data/dist";
    public $css = [
        "css/jquery-jvectormap.css"
    ];
    public $js = [
        'js/jquery-jvectormap.js',
        'js/jquery-jvectormap-1.2.2.js'
    ];
    public $jsOptions=[];
    public $depends = [
        'yii\web\JqueryAsset'
    ];
}