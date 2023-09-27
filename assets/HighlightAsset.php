<?php

namespace imessage\assets;

use yii\web\View;

class HighlightAsset extends WpAsset
{
    public $sourcePath = "@bower/highlight";
    public $css = [
        'mac.css'
    ];
    public $js = [
        'highlight.pack.js', "mac.js"
    ];
    public $jsOptions = [
        "position" => View::POS_HEAD,
    ];
    public $depends = ['yii\web\JqueryAsset'];
}