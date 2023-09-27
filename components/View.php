<?php
namespace imessage\components;

use Yii;
use yii\helpers\Html;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;
use yii\helpers\ArrayHelper;
use yii\web\View as YiiView;
use yii\base\InvalidConfigException;

class View extends YiiView
{

    /**
     * @param string $css
     * @param array $options
     * @param null $key
     */
    public function registerCss($css, $options = [], $key = null)
    {
        global $wp_styles;
        $handles =$wp_styles->queue;
        if(is_array($handles) and !empty($handles)){
            $handle  =$handles[count($handles)-1];
            wp_add_inline_style($handle, $css);
        }
        $key = $key ?: md5($css);
        $this->css[$key] = Html::style($css, $options);

    }

    /**
     * @param string $js
     * @param int $position
     * @param null $key
     */
    public function registerJs($js, $position = self::POS_READY, $key = null){
        $key = $key ?: md5($js);
        $this->js[$position][$key] = $js;
        if ($position === self::POS_READY || $position === self::POS_LOAD) {
            JqueryAsset::register($this);
        }
    }

    /**
     * 打印js
     * @param $position
     * @return void
     */
    public function adminPrintFooterScripts($position=self::POS_READY){
        global $wp_scripts;
        $handles =$wp_scripts->queue;
        if(is_array($handles) and !empty($handles)){
            $handle  =$handles[count($handles)-1];
            if(!empty(($this->js)[$position])){
                $nonce = wp_create_nonce('custom_ajax_nonce');
                $js_code = implode("\n", $this->js[self::POS_READY]);
                $csrfToken= Yii::$app->request->csrfToken;
                $js =<<<JS
jQuery(function ($) {
    $.ajaxSetup({
        beforeSend: function(xhr) {
            xhr.setRequestHeader('X-CSRF-Token', '{$csrfToken}');
        }
    });
    {$js_code}
    
});
JS;
                wp_add_inline_script(   $handle , $js);
            }

        }
    }
}
