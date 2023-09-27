<?php
/** @var $this yii\web\View */
/** @var $myText  */
/** @var $submenu string */

use yii\helpers\Markdown;
use imessage\assets\HighlightAsset;
use imessage\assets\Tutorial;

HighlightAsset::register($this);
HighlightAsset::addCssFile($this,"/styles/monokai_sublime.css");

$baseUrl = Tutorial::publishUrl();
$html =Markdown::process(file_get_contents(__DIR__.'/iMessage 使用教程.md'), 'gfm-comment');
$html = str_replace('@/',$baseUrl."/",$html);

$this->registerJs("hljs.initHighlightingOnLoad();");
Tutorial::register($this);
?>


<div class="wrap">
    <h1 class="wp-heading-inline">教程</h1>
    <ul id="app" class="nav-tab-wrapper wp-clearfix">
        <a href="/wp-admin/admin.php?page=imessage" class="nav-tab ">iMessage</a>
        <a href="/wp-admin/admin.php?page=index/message" class="nav-tab">消息</a>
        <a href="/wp-admin/admin.php?page=index/saolan" class="nav-tab ">扫蓝</a>
        <a href="/wp-admin/admin.php?page=index/token" class="nav-tab ">令牌</a>
        <a href="/wp-admin/admin.php?page=index/apple" class="nav-tab ">Apple ID</a>
        <a href="/wp-admin/admin.php?page=index/mail" class="nav-tab ">邮箱</a>
        <a href="/wp-admin/admin.php?page=index/phone" class="nav-tab ">接码</a>
        <a href="/wp-admin/admin.php?page=index/tutorial" class="nav-tab nav-tab-active">教程</a>
        <a href="/wp-admin/admin.php?page=index/rules" class="nav-tab">路由</a>
        <a href="/wp-admin/admin.php?page=index/book" class="nav-tab">记事本</a>
        <a href="/wp-admin/admin.php?page=index/setting" class="nav-tab">设置</a>
    </ul>

    <div id="doc" style="background-color: white;padding: 10px">
        <?=  $html ?>
    </div>
</div>
