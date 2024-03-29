<?php
/** @var $this yii\web\View */
/** @var $content string */

use  yii\helpers\Html;
use imessage\assets\WebSlidesAsset;


WebSlidesAsset::register($this);

?>

<?php $this->beginPage() ?>
    <!doctype html>
    <html lang="<?= Yii::$app->language ?>" prefix="og: http://ogp.me/ns#">
    <head>
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="theme-color" content="#333333">
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <body>
    <?php $this->beginBody() ?>
    <header role="banner">
        <nav role="navigation">
            <p class="logo"><a href="/" title="WebSlides">home</a></p>
            <ul>

            </ul>
        </nav>
    </header>
    <main role="main">
        <article id="webslides" class="vertical">
            <?= $content ?>
        </article>
    </main>

    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage() ?>