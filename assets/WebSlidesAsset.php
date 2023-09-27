<?php
namespace imessage\assets;

use imessage\assets\WpAsset;

class WebSlidesAsset extends WpAsset {
    public $sourcePath =  "@bower/webslibes/static";
    public $css = ['css/svg-icons.css','css/webslides.css'];
    public $js = ['js/webslides.js','js/svg-icons.js'];
}