<?php

namespace imessage\controllers\frontend;

use yii\web\Controller;

class IndexController extends Controller
{

    public $layout ='webslides';
    public function actionIndex(){
        return $this->render('index');
    }
}