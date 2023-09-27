<?php

namespace imessage\components\lib;

use imessage\App;
use Yii;
use imessage\models\Menu;
use yii\base\Behavior;
use yii\base\Component;
use yii\base\InvalidRouteException;
use Exception;
class PageComponent extends Component
{

    /**
     * 注册菜单和页面
     *
     * @param string $moduleId
     */
    public function registerPage($moduleId = '')
    {
        /** @var App $app */
        $app = Yii::$app;
        if (empty($moduleId)) {
            $menus = $app->params["menus"];
        } else {
            $module = Yii::$app->getModule($moduleId);
            $menus = $module->params["menus"];
        }

        foreach ($menus as $menu) {
            $menuModel = new Menu($menu);
            $menuModel->registerMenu();
        }
    }

    /**
     * 调用控制器显示视图
     * @throws InvalidRouteException
     */
    public function renderView()
    {
        /** @var App $app */
        $app = Yii::$app;
        $request =  $app->request;
        $query = $request->queryParams;
        $route = $query["page"];
        $route = ($route == Yii::$app->id) ? "index" :$route;
        unset($query['page']);
        if ($this->checkAdminPageRoute($route, $moduleId)) {
            try {
                if (empty($moduleId)) {
                    $data = $app->runAction($route, $query);
                } else {
                    $data = $app->getModule($moduleId)->runAction($route, $query);
                }
            } catch (Exception $exception) {
                $data =  $app->errorHandler->wpAdminRenderException($exception);
            }
        } else {
            $data = $app->runAction("index/error", new  Exception('找不到路由' . $route));
        }
        if (!empty($data)) {
            $app->response->data = $data;
            $app->response->send();
        }
    }

    /**
     * 检查后台页面路由是否存在
     *
     * @param $route
     * @param $moduleId
     * @return bool
     */
    public function checkAdminPageRoute(&$route, &$moduleId = '')
    {
        // +----------------------------------------------------------------------
        // | 未启用模块情况
        // | index => backend\controllers\IndexController::actionIndex
        // ｜index/test =>backend\controllers\IndexController::actionTest
        // ｜test/test/test =>backend\controllers\test\TestController::actionTest
        // ｜启用模块情况
        // | wp => crud\modules\wp\controllers\IndexController::actionIndex
        // ｜wp/index => backend\controllers\IndexController::actionIndex
        // | wp/index/test => crud\modules\wp\controllers\IndexController::actionTest
        // | wp/test/index/index => crud\modules\wp\controllers\test\IndexController::actionIndex
        // +----------------------------------------------------------------------
        /** @var App $app */
        $app = Yii::$app;
        $arr = explode('/', $route);
        $count = count($arr);
        $modules =array_keys($app->modules);
        $is_module = in_array($arr[0], $modules);
        if ($is_module) {
            $moduleId = $arr[0];
            $module = Yii::$app->getModule($moduleId);
            $route = trim(str_replace($moduleId.'/', "", $route), "/");
            switch ($count) {
                case 1:
                    $controllerNamespace = $module->controllerNamespace.'\\IndexController';
                    $actionName = "actionIndex";
                    break;
                case 2:
                    $controllerNamespace = $module->controllerNamespace.'\\' . toScoreUnder(ucfirst($arr[1]) ,'-') . 'Controller';
                    $actionName = "actionIndex";
                    break;
                case 3:
                    $controllerNamespace = $module->controllerNamespace.'\\' . toScoreUnder(ucfirst($arr[1]),'-') . 'Controller';
                    $actionName = "action" .toScoreUnder( ucfirst($arr[2]),'-');
                    break;
                default:
                    unset($arr[0]);
                    $controllerId = $arr[$count - 2];
                    unset($arr[$count - 2]);
                    $actionId = $arr[$count - 1];
                    unset($arr[$count - 1]);
                    $namespace = trim(join("\\", $arr));
                    $controllerNamespace = $module->controllerNamespace.'\\' .
                        ($namespace != "" ? $namespace . "\\" : "") . ucfirst($controllerId) . 'Controller';
                    $actionName = "action" .toScoreUnder( ucfirst($actionId),'-');
            }

        } else {
            switch ($count) {
                case 1:
                    $controllerNamespace = $app->controllerNamespace.'\\' . ucfirst($arr[0]) . "Controller";
                    $actionName = "actionIndex";
                    break;
                case 2:
                    $controllerNamespace = $app->controllerNamespace.'\\' . ucfirst($arr[0]) . "Controller";
                    $actionName = "action" . ucfirst($arr[1]);
                    break;
                default:
                    $controllerId = $arr[$count - 2];
                    unset($arr[$count - 2]);
                    $actionId = $arr[$count - 1];
                    unset($arr[$count - 1]);
                    $namespace = trim(join("\\", $arr));
                    $controllerNamespace =$app->controllerNamespace.'\\' .
                        ($namespace != "" ? $namespace . "\\" : "")
                        . ucfirst($controllerId) . 'Controller';
                    $actionName = "action" . ucfirst($actionId);
            }
        }
        return $app->checkRoute($controllerNamespace, $actionName);
    }
}