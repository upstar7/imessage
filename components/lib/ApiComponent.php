<?php

namespace imessage\components\lib;

use imessage\App;
use imessage\components\Response;
use yii\base\Behavior;
use Yii;
use Exception;
use yii\base\Component;
use yii\base\InvalidRouteException;

class ApiComponent  extends Component
{

    /**
     * 注册Restful 风格api
     * @param string $moduleId
     */
    public  function registerRestfulApi($moduleId = '')
    {
        /** @var App $app */
        $app = Yii::$app;
        /**
         *  * - `'PUT,PATCH users/<id>' => 'user/update'`: update a user
         * - `'DELETE users/<id>' => 'user/delete'`: delete a user
         * - `'GET,HEAD users/<id>' => 'user/view'`: return the details/overview/options of a user
         * - `'POST users' => 'user/create'`: create a new user
         * - `'GET,HEAD users' => 'user/index'`: return a list/overview/options of users
         * - `'users/<id>' => 'user/options'`: process all unhandled verbs of a user
         * - `'users' => 'user/options'`: process all unhandled verbs of user collection
         */
        if (empty($moduleId)) {
            // indexController注册
            register_rest_route($app->id, "api/(?P<id>[\d]+)", [
                'methods' => "PUT,PATCH",
                'callback' => [$this, "renderRestfulApi"],
                'permission_callback' => function () {
                    return '';
                },
            ]);
            register_rest_route($app ->id, "api/(?P<id>[\d]+)", [
                'methods' => "DELETE",
                'callback' => [$this, "renderRestfulApi"],
                'permission_callback' => function () {
                    return '';
                },
            ]);
            register_rest_route($app ->id, "api/(?P<id>[\d]+)", [
                'methods' => "GET,HEAD",
                'callback' => [$this, "renderRestfulApi"],
                'permission_callback' => function () {
                    return '';
                },
            ]);
            register_rest_route($app ->id, "api/(?P<id>[\d]+)", [
                'methods' => "OPTIONS",
                'callback' => [$this, "renderRestfulApi"],
                'permission_callback' => function () {
                    return '';
                },
            ]);
            register_rest_route($app ->id, "api", [
                'methods' => "POST",
                'callback' => [$this, "renderRestfulApi"],
                'permission_callback' => function () {
                    return '';
                },
            ]);
            register_rest_route($app ->id, "api", [
                'methods' => "GET,HEAD",
                'callback' => [$this, "renderRestfulApi"],
                'permission_callback' => function () {
                    return '';
                },
            ]);
            register_rest_route($app ->id, "api", [
                'methods' => "OPTIONS",
                'callback' => [$this, "renderRestfulApi"],
                'permission_callback' => function () {
                    return '';
                },
            ]);
            // 模块默认控制器
            register_rest_route($app->id, "api/(?P<controller>[\w]+)/(?P<id>[\d]+)", [
                'methods' => "PUT,PATCH",
                'callback' => [$this, "renderRestfulApi"],
                'permission_callback' => function () {
                    return '';
                },
            ]);
            register_rest_route($app->id, "api/(?P<controller>[\w]+)/(?P<id>[\d]+)", [
                'methods' => "DELETE",
                'callback' => [$this, "renderRestfulApi"],
                'permission_callback' => function () {
                    return '';
                },
            ]);
            register_rest_route($app->id, "api/(?P<controller>[\w]+)/(?P<id>[\d]+)", [
                'methods' => "GET,HEAD",
                'callback' => [$this, "renderRestfulApi"],
                'permission_callback' => function () {
                    return '';
                },
            ]);
            register_rest_route($app->id, "api/(?P<controller>[\w]+)/(?P<id>[\d]+)", [
                'methods' => "OPTIONS",
                'callback' => [$this, "renderRestfulApi"],
                'permission_callback' => function () {
                    return '';
                },
            ]);
            register_rest_route($app->id, "api/(?P<controller>[\w]+)", [
                'methods' => "POST",
                'callback' => [$this, "renderRestfulApi"],
                'permission_callback' => function () {
                    return '';
                },
            ]);
            register_rest_route($app->id, "api/(?P<controller>[\w]+)", [
                'methods' => "GET,HEAD",
                'callback' => [$this, "renderRestfulApi"],
                'permission_callback' => function () {
                    return '';
                },
            ]);
            register_rest_route($app->id, "api/(?P<controller>[\w]+)", [
                'methods' => "OPTIONS",
                'callback' => [$this, "renderRestfulApi"],
                'permission_callback' => function () {
                    return '';
                },
            ]);

        } else {
            register_rest_route($app->id, "api/(?P<module>([^/]+))/(?P<controller>([^/]+))/(?P<id>[\d]+)", [
                'methods' => "PUT,PATCH",
                'callback' => [$this, "renderRestfulApi"],
                'permission_callback' => function () {
                    return '';
                },
            ]);
            register_rest_route($app->id, "api/(?P<module>([^/]+))/(?P<controller>([^/]+))/(?P<id>[\d]+)", [
                'methods' => "DELETE",
                'callback' => [$this, "renderRestfulApi"],
                'permission_callback' => function () {
                    return '';
                },
            ]);
            register_rest_route($app->id, "api/(?P<module>([^/]+))/(?P<controller>([^/]+))/(?P<id>[\d]+)", [
                'methods' => "GET,HEAD",
                'callback' => [$this, "renderRestfulApi"],
                'permission_callback' => function () {
                    return '';
                },
            ]);
            register_rest_route($app->id, "api/(?P<module>([^/]+))/(?P<controller>([^/]+))/(?P<id>[\d]+)", [
                'methods' => "OPTIONS",
                'callback' => [$this, "renderRestfulApi"],
                'permission_callback' => function () {
                    return '';
                },
            ]);
            register_rest_route($app->id, "api/(?P<module>([^/]+))/(?P<controller>([^/]+))", [
                'methods' => "POST",
                'callback' => [$this, "renderRestfulApi"],
                'permission_callback' => function () {
                    return '';
                },
            ]);
            register_rest_route($app->id, "api/(?P<module>([^/]+))/(?P<controller>([^/]+))", [
                'methods' => "GET,HEAD",
                'callback' => [$this, "renderRestfulApi"],
                'permission_callback' => function () {
                    return '';
                },
            ]);
            register_rest_route($app->id, "api/(?P<module>([^/]+))/(?P<controller>([^/]+))", [
                'methods' => "OPTIONS",
                'callback' => [$this, "renderRestfulApi"],
                'permission_callback' => function () {
                    return '';
                },
            ]);

            register_rest_route($app->id, "api/(?P<module>([^/]+))/(?P<id>[\d]+)", [
                'methods' => "PUT,PATCH",
                'callback' => [$this, "renderRestfulApi"],
                'permission_callback' => function () {
                    return '';
                },
            ]);
            register_rest_route($app->id, "api/(?P<module>([^/]+))/(?P<id>[\d]+)", [
                'methods' => "DELETE",
                'callback' => [$this, "renderRestfulApi"],
                'permission_callback' => function () {
                    return '';
                },
            ]);
            register_rest_route($app->id, "api/(?P<module>([^/]+))/(?P<id>[\d]+)", [
                'methods' => "GET,HEAD",
                'callback' => [$this, "renderRestfulApi"],
                'permission_callback' => function () {
                    return '';
                },
            ]);
            register_rest_route($app->id, "api/(?P<module>([^/]+))/(?P<id>[\d]+)", [
                'methods' => "OPTIONS",
                'callback' => [$this, "renderRestfulApi"],
                'permission_callback' => function () {
                    return '';
                },
            ]);
            register_rest_route($app->id, "api/(?P<module>([^/]+))", [
                'methods' => "POST",
                'callback' => [$this, "renderRestfulApi"],
                'permission_callback' => function () {
                    return '';
                },
            ]);
            register_rest_route($app->id, "api/(?P<module>([^/]+))", [
                'methods' => "GET,HEAD",
                'callback' => [$this, "renderRestfulApi"],
                'permission_callback' => function () {
                    return '';
                },
            ]);
            register_rest_route($app->id, "api/(?P<module>([^/]+))", [
                'methods' => "OPTIONS",
                'callback' => [$this, "renderRestfulApi"],
                'permission_callback' => function () {
                    return '';
                },
            ]);
        }
    }

    /**
     * 获取RestfulApi路由并执行控制器
     * @param $request
     * @throws InvalidRouteException
     * @throws \yii\console\Exception
     */
    public function renderRestfulApi($request)
    {
        /** @var App $app */
        $app = Yii::$app;
        $module = $controller = $route = '';
        $params = $request->get_params();
        if(isset($params['id'])){
            $action = self::getActionByHttpMethod(true);
        }else{
            $action = self::getActionByHttpMethod(false);
        }

        // +----------------------------------------------------------------------
        // ｜获取路由$route,$params
        // +----------------------------------------------------------------------
        if (isset($params['module'])) {
            $module = $params['module'];
            unset($params['module']);
        }
        if (isset($params['controller'])) {
            $controller = $params["controller"];
            unset($params['controller']);
        }

        $this->runApi($module, $controller, $action, $route, $params);
    }



    /**
     * 注册普通api
     * @param string $moduleId
     */
    public  function registerApi($moduleId = '')
    {
        /** @var App $app */
        $app = Yii::$app;
        if (empty($moduleId)) {
            register_rest_route($app->id, "api/(?P<controller>([^/]+))/(?P<action>([^/]+))/(?P<id>[\d]+)", [
                'methods' => "GET,POST,HEAD,PUT,PATCH,OPTIONS,DELETE,OPTIONS",
                'callback' => [$this, "renderApi"],
                'permission_callback' => function () {
                    return '';
                },
            ]);
            register_rest_route($app ->id, "api/(?P<controller>([^/]+))/(?P<action>([^/]+))", [
                'methods' => "GET,POST,HEAD,PUT,PATCH,OPTIONS,DELETE,OPTIONS",
                'callback' => [$this, "renderApi"],
                'permission_callback' => function () {
                    return '';
                },
            ]);
            register_rest_route($app ->id, "api/(?P<controller>([^/]+))", [
                'methods' => "GET,POST,HEAD,PUT,PATCH,OPTIONS,DELETE,OPTIONS",
                'callback' => [$this, "renderApi"],
                'permission_callback' => function () {
                    return '';
                },
            ]);
            register_rest_route($app ->id, "api", [
                'methods' => "GET,POST,HEAD,PUT,PATCH,OPTIONS,DELETE,OPTIONS",
                'callback' => [$this, "renderApi"],
                'permission_callback' => function () {
                    return '';
                },
            ]);
        } else {
            register_rest_route($app ->id, "api/(?P<module>([^/]+))/(?P<controller>([^/]+))/(?P<action>([^/]+))/(?P<id>[\d]+)", [
                'methods' => "GET,POST,HEAD,PUT,PATCH,OPTIONS,DELETE,OPTIONS",
                'callback' => [$this, "renderApi"],
                'permission_callback' => function () {
                    return '';
                },
            ]);
            register_rest_route($app ->id, "api/(?P<module>([^/]+))/(?P<controller>([^/]+))/(?P<action>([^/]+))", [
                'methods' => "GET,POST,HEAD,PUT,PATCH,OPTIONS,DELETE,OPTIONS",
                'callback' => [$this, "renderApi"],
                'permission_callback' => function () {
                    return '';
                },
            ]);
            register_rest_route($app ->id, "api/(?P<module>([^/]+))/(?P<controller>([^/]+))", [
                'methods' => "GET,POST,HEAD,PUT,PATCH,OPTIONS,DELETE,OPTIONS",
                'callback' => [$this, "renderApi"],
                'permission_callback' => function () {
                    return '';
                },
            ]);
            register_rest_route($app ->id, "api/(?P<module>([^/]+))", [
                'methods' => "GET,POST,HEAD,PUT,PATCH,OPTIONS,DELETE,OPTIONS",
                'callback' => [$this, "renderApi"],
                'permission_callback' => function () {
                    return '';
                },
            ]);
        }
    }

    /**
     * 获取Api路由并执行控制器
     *
     * @param $request
     * @throws InvalidRouteException
     * @throws \yii\console\Exception
     */
    public function renderApi($request)
    {

        /** @var App $app */
        $app = Yii::$app;
        $module = $controller = $action = $route =  '';
        $params = $request->get_params();

        // +----------------------------------------------------------------------
        // ｜获取路由$route,$params
        // +----------------------------------------------------------------------
        if (isset($params['module'])) {
            $module = $params['module'];
            unset($params['module']);
        }
        if (isset($params['controller'])) {
            $controller = $params["controller"];
            unset($params['controller']);
        }
        if (isset($params['action'])) {
            $action = $params["action"];
            unset($params['action']);
        }
        $this->runApi($module, $controller, $action, $route, $params);
    }

    /**
     * 检查路由并执行控制器
     * @param $moduleId
     * @param $controller
     * @param $action
     * @param $route
     * @param $params
     */
    public function runApi($moduleId, $controller, $action, $route, $params)
    {
        if ( $this->checkApiRoute($moduleId, $controller, $action, $route)) {
            try{
                if ($moduleId) {
                    $data = Yii::$app->getModule($moduleId)->runAction($route, $params);
                } else {
                    $data = Yii::$app->runAction($route, $params);
                }

            }catch (Exception $exception){
                Yii::$app->response->format = Response::FORMAT_JSON;
                $data =[
                    'code'=>0,
                    'message'=>$exception->getMessage()];
                if(YII_DEBUG){
                    $data['trace']=$exception->getTrace();
                }
            }
        }else{
            Yii::$app->response->format = Response::FORMAT_JSON;
            $data=[
                'code'=>0,
                'message'=>"\"". $route."\"".'方法未定义',
                'data'=>''
            ];
        }
        if (!empty($data)) {
            Yii::$app->response->data = $data;
            Yii::$app->response->send();
        }
    }


    /**
     * @param boolean $bool
     * @return string
     */
    public static function getActionByHttpMethod($bool=false)
    {
        $method = Yii::$app->request->method;

        switch ($method) {
            case 'GET':
            case 'HEAD':
                $action = $bool ? "view" : "index";
                break;
            case 'POST':
                $action = "create";
                break;
            case 'PATCH':
            case 'PUT':
                $action = "update";
                break;
            case 'DELETE':
                $action = "delete";
                break;
            case 'OPTIONS':
                $action = "options";
                break;
            default:
                $action = "index";
        }

        return $action;
    }


    /**
     * 检查api路由
     *
     * @param $controller
     * @param $action
     * @param $route
     * @param $moduleId
     *
     * @return bool
     */
    public function checkApiRoute(&$moduleId, $controller, $action, &$route)
    {
        /** @var App $app */
        $app = Yii::$app;
        $modules = array_keys($app->modules);
        $controller = empty($controller) ? 'index' : $controller;
        $action = empty($action) ? 'index' : $action;
        if (empty($moduleId)) {
            if (in_array($controller, $modules)) {
                $moduleId = $controller;
                $controller = $action;
                $route = "api/$controller/$action";
                $defaultControllerNamespace = Yii::$app->getModule($moduleId)->controllerNamespace;
            } else {
                $route = "api/$controller/$action";
                $defaultControllerNamespace = Yii::$app->controllerNamespace;
            }

        } else {
            $route = "api/$controller/$action";
            $defaultControllerNamespace = Yii::$app->getModule($moduleId)->controllerNamespace;
        }

        $str = explode('/', $route);
        if (count($str) == 3) {
            $controllerNamespace = $defaultControllerNamespace . "\\" . $str[0] . '\\' .toScoreUnder( ucfirst($str[1]),"-") . "Controller";
            $actionName = 'action' . toScoreUnder(ucfirst($str[2]), "-");
        } else {
            $controllerNamespace = $defaultControllerNamespace . "\\". toScoreUnder(ucfirst($str[2]),"-") . "Controller";
            $actionName = 'action' . toScoreUnder(ucfirst($str[3]), "-");
        }
        return $app->checkRoute($controllerNamespace, $actionName);

    }

}