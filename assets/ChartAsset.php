<?php

namespace imessage\assets;


use imessage\assets\WpAsset;

class ChartAsset extends WpAsset {
//    public $sourcePath =  "@bower/Chart.js";
//    public $css = [];
    public $js = ['https://cdn.jsdelivr.net/npm/echarts@5.4.2/dist/echarts.min.js'];
//    public $jsOptions=[];
    public $depends = ['yii\web\JqueryAsset'];
}