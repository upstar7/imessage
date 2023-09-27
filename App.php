<?php

namespace imessage;



use imessage\components\Imap;
use PHPMailer\PHPMailer\PHPMailer as SMTP;
use Yii;
use imessage\components\Response;
use imessage\components\View;
use imessage\models\AjaxAction;
use imessage\models\Menu;
use imessage\models\Settings;
use Exception;
use yii\base\InvalidConfigException;
use yii\base\InvalidRouteException;
use yii\helpers\ArrayHelper;
use yii\web\Application;
use imessage\components\ErrorHandler;
use imessage\components\lib\ApiComponent;
use imessage\components\lib\AjaxComponent;
use imessage\components\lib\PageComponent;
use imessage\components\lib\SettingsComponent;
use imessage\components\lib\FrontendPageComponent;


/**
 * @property Imap $imap
 * @property SettingsComponent $_settings
 * @property PageComponent $_page
 * @property AjaxComponent $_ajax
 * @property FrontendPageComponent $_frontendPage
 * @property ApiComponent $_api
 * @property ErrorHandler $errorHandler
 * @package imessage
 */
class App extends Application
{
    /**
     * 创建app对象
     * App constructor.
     * @throws InvalidConfigException
     */
    public function __construct($config=[])
    {
        require __DIR__ . '/config/bootstrap.php';
        $behaviors = [
            'components' => [
                '_settings' => ['class' => SettingsComponent::class],
                '_page' => ['class' => PageComponent::class],
                '_frontendPage' => ['class' => FrontendPageComponent::class],
                '_ajax' => ['class' => AjaxComponent::class],
                '_api' => ['class' => ApiComponent::class],
            ]
        ];
        $config = ArrayHelper::merge(
            $config,
            $behaviors,
            require __DIR__ . '/config/main.php',
            require __DIR__ . '/config/main-local.php'
        );
        parent::__construct($config);
    }

    public static function loadModulesConfig()
    {
        return ;
    }

    /**
     * 核心:将Yii2操作、事件等挂载到wordpress钩子上
     */
    public function run()
    {
        add_action('phpmailer_init', [$this, "smtp"]);
        date_default_timezone_set('Asia/Shanghai');
        // +----------------------------------------------------------------------
        // ｜后台页面、设置、菜单，挂载到wordpress钩子中
        // +----------------------------------------------------------------------
        add_action("admin_menu", [$this->_page, "registerPage"]);
        add_action("admin_init", [$this->_settings, "registerSettings"]);
        // +----------------------------------------------------------------------
        // ｜Ajax、RestfulApi、路由配置、解析规则，挂载到wordpress钩子中
        // +----------------------------------------------------------------------
        add_action("init", [$this->_ajax, "registerAjax"]);
        add_action("init", [$this, "registerAjax"]);
        add_action("init", function (){
            $this->_frontendPage->route();
        });
        add_action("rest_api_init", function (){
            $this->_api->registerRestfulApi();
        });
        $this->_frontendPage->route();
        add_filter('avatar_defaults',  [$this,'avatarUrlDefaults']);

        // +----------------------------------------------------------------------
        // ｜过滤评论
        // +----------------------------------------------------------------------
        //add_action('preprocess_comment', [$this, 'preprocessComment']);

        // +----------------------------------------------------------------------
        // ｜JS和css注册和排队钩子
        // +----------------------------------------------------------------------
        //add_filter('plugin_action_links', [$this, 'addSettingsButton'], 10, 2);
        add_action('admin_print_scripts', [$this, 'printScripts']);
        add_action("admin_print_footer_scripts", [$this, "printFooterScripts"]);
        add_action('wp_head', [$this, 'printScripts']);
        add_action("wp_footer", [$this, "printFooterScripts"]);
        add_action('phpmailer_init', [$this, "smtp"]);
        // +----------------------------------------------------------------------
        // ｜静止自动更新
        // +----------------------------------------------------------------------
        add_filter('pre_site_transient_update_core', function () {
            return null;
        }); // 关闭核心提示
        add_filter('pre_site_transient_update_plugins', function () {
            return null;
        }); // 关闭插件提示
        add_filter('pre_site_transient_update_themes', function () {
            return null;
        }); // 关闭主题提示
        remove_action('admin_init', '_maybe_update_core');      // 禁止 WordPress 检查更新
        remove_action('admin_init', '_maybe_update_plugins');   // 禁止插件更新插件
        remove_action('admin_init', '_maybe_update_themes');    // 禁止主题更新更新插件

        add_action('lost_password_html_link', [$this, 'custom_login_page_button']);
        add_action('login_enqueue_scripts', function (){
            wp_enqueue_script('jquery');
        });

        add_filter('auth_cookie_expiration', [$this,'cookieExpiration'], 10, 3);
    }

    /**
     * 打印yii2容器中注册的css
     */
    public function printScripts()
    {
        /** @var View  $view */
        $view = Yii::$app->getView();
        $view->adminPrintFooterScripts(View::POS_HEAD);
    }

    public function avatarUrlDefaults($avatarUrlDefaults){
        $src = '/favicon.ico';//图文url路径
        $avatarUrlDefaults[$src] = "默认头像";//图片的描述名称
        return $avatarUrlDefaults;
    }
    /**
     * 配置发送邮箱
     *
     * @param SMTP $mail
     */
    public function smtp($mail)
    {
        // 发件人呢称
        $mail->FromName = get_option('crud_group_mail_nickname', 'admin');
        // 发件人邮箱
        $mail->From = get_option('imessage_group_mail_from', '');
        // smtp 服务器地址
        $mail->Host = get_option('imessage_group_mail_host', "smtp.qq.com");
        // 端口号
        $mail->Port = get_option('imessage_group_mail_port', 587);
        // 账户
        $mail->Username =get_option('imessage_group_mail_username', '');
        $mail->CharSet = 'UTF-8';
        $mail->Timeout = 30 ;
        // 密码
        $mail->Password =get_option('imessage_group_mail_password', '');
        $mail->SMTPAuth = true;
        //starttls
        $mail->SMTPSecure = get_option('imessage_group_mail_encryption', "tls");
        $mail->isSMTP();
    }

    /**
     * 打印yii2容器中注册的Javascript
     */
    public function printFooterScripts()
    {
        /** @var View  $view */
        $view = Yii::$app->getView();
        $view->adminPrintFooterScripts();
    }

    /**
     * 检查路由是否存在
     *
     * @param $controllerNamespace
     * @param $actionName
     *
     * @return bool
     */
    public function checkRoute($controllerNamespace, $actionName)
    {

        if (!class_exists($controllerNamespace)) {
            return false;
        }
        if (!method_exists($controllerNamespace, $actionName)) {
            return false;
        }
        return true;
    }

    public function registerAjax(){
        $ajax =[
            [ "menu_slug" =>  "console/install"],
            [ "menu_slug" =>  "console/delete"],
            [ "menu_slug" =>  "console/select"],
            [ "menu_slug" =>  "console/update"],
            [ "menu_slug" =>  "console/query"],
            [ "menu_slug" =>  "console/cache"],
            [ "menu_slug" =>  "console/ip"],
            [ "menu_slug" =>  "console/get-apple-id"],
        ];
        foreach ($ajax as $menu) {
            $menuModel = new AjaxAction($menu);
            $menuModel->registerAjaxAction();
        }
    }

    public function custom_login_page_button($html_link){
        $html =<<<HTML
<div  style="width: 500px;min-height: 300px;">
      <div style="width: 100%;margin-top: 30px">
        <h1><a href="/" style="background-image:url('/logo.jpg')"></a></h1>
      </div>
      <div>
        <h1>请填写Script Uncle验证码:<span id="time" style="color: red"></span></h1>
       </div>
      <div id="code" style="display: flex;justify-content: space-between;padding: 30px 100px;">
            <input class="jump-input" style="width: 50px;border-width: 2px;border-radius: 10px;"  type="text"  />
            <input class="jump-input" style="width: 50px;border-width: 2px;border-radius: 10px;"  type="text"  />
            <input class="jump-input" style="width: 50px;border-width: 2px;border-radius: 10px;"  type="text"  />
            <input class="jump-input" style="width: 50px;border-width: 2px;border-radius: 10px;"  type="text"  />
      </div>
      <div style="padding: 10px 100px;display: flex;justify-content: space-between">
        <button class="button "  id="restCode">没有收到验证码</button>
        <button class="button button-primary button-large" id="code_login">登陆</button>
    </div>
</div>

HTML;

        $js=<<<JS
jQuery(document).ready(function($) {
  $('#login>h1>a').css('background-image', 'url("/logo.jpg")');
  const showDialogBtn = $('#showDialogBtn');
  const overlay = $('<div id="overlay"></div>').appendTo('body');
  overlay.css({
    display: 'none',
    position: 'fixed',
    top: 0,
    left: 0,
    width: '100%',
    height: '100%',
    backgroundColor: '#f0f0f1',
    //backgroundColor: 'rgba(0, 0, 0, 0.5)',
    zIndex: 1000
  });
  document.addEventListener('click', function(event) {
      event.stopPropagation();
    });
  const dialog = $('<div id="dialog"></div>').appendTo('body');
  dialog.css({
    display: 'none',
    position: 'fixed',
    top: '50%',
    left: '50%',
    transform: 'translate(-50%, -50%)',
    backgroundColor: 'white',
    padding: '20px',
    borderRadius: '20px',
    boxShadow: '0px 5px 15px rgba(0, 0, 0, 0.3)',
    zIndex: 1001
  });
  dialog.on('click',function (event){
      event.stopPropagation()
  })
  dialog.html(`{$html}`);
  const input_t = $('#code input');
  // 波浪动画
  const  animateWave =(index, times) =>{
    if (times === 0) {
      return;
    }
  
    input_t.eq(index).addClass('error');
    input_t.eq(index).delay(100).queue(function(next) {
      $(this).removeClass('error');
      next();
      animateWave((index + 1) % input_t.length, times - 1);
    });
  }
  const inputs =document.getElementById('code').querySelectorAll('input[type="text"]')
  inputs.forEach((input, index) => {
    input.addEventListener('input', function() {
      if (this.value.length > 1) {
        this.value = this.value.slice(-1); 
      }
      if (this.value.length === 1) {
        if (index < inputs.length - 1) {
          inputs[index + 1].focus();
        }
      }
      if (this.value.length === 0 && index > 0) {
        inputs[index - 1].focus();
      }
     if (index === inputs.length - 1) {
        let result = ''; // 清空之前的结果
        inputs.forEach(input => {
          result += input.value; // 将每个输入框的值拼接到结果字符串中
        });
        if(result.length ===4){
           jQuery.ajax({
                url: "/wp-json/imessage/api/login",
                type: 'POST',
                dataType: 'json',
                data: {code:result},
                success: (res) => {
                    console.log(res)
                    if(res.code ==1){
                        window.location.href = res.data.redirect;
                    }else {
                        animateWave(0,20);
                    }
                },
                error: (res) => {
                  console.log(res);
                }
            })  
        }    
     }
    });
  });

  showDialogBtn.on('click', function() {
    overlay.css('display', 'block');
    dialog.css('display', 'block');
    jQuery.ajax({
      url: "/wp-json/imessage/api/login",
      type: 'GET',
      dataType: 'json',
      success: (res) => {
          if (res.code===1){
              const countdownElement = document.getElementById('time');
              // 设置初始倒计时时间（秒）
              let countdown = 60;
              // 更新倒计时显示并启动倒计时
              function updateCountdown() {
                countdownElement.textContent = countdown;
                countdown--;
                if (countdown < 0) {
                  clearInterval(interval); // 倒计时结束后停止计时器
                  countdownElement.textContent = '';
                }
              }
             // 更新间隔为1秒
            const interval = setInterval(updateCountdown, 1000);
          }else {
            alert(res.message);
          }
        },
      error: (res) => {
        console.log(res)
      }
    })
  });

  overlay.on('click', function() {
    overlay.css('display', 'none');
    dialog.css('display', 'none');
  });
  $('#restCode').on('click', function() {
    overlay.css('display', 'block');
    dialog.css('display', 'block');
    // 重新获取验证码
    jQuery.ajax({
      url: "/wp-json/imessage/api/login",
      type: 'GET',
      dataType: 'json',
      success: (res) => {
          console.log(res)
          if (res.code===1){
              const countdownElement = document.getElementById('time');
              // 设置初始倒计时时间（秒）
              let countdown = 60;
              // 更新倒计时显示并启动倒计时
              function updateCountdown() {
                countdownElement.textContent = countdown;
                countdown--;
                if (countdown < 0) {
                  clearInterval(interval); // 倒计时结束后停止计时器
                  countdownElement.textContent = '';
                }
              }
             // 更新间隔为1秒
            const interval = setInterval(updateCountdown, 1000);
          }else {
            alert(res.message);
          }
        },
      error: (res) => {
        console.log(res)
      }
    })
  });
  // 提交
  $('#code_login').on('click',()=>{
   let inputs =   document.getElementById('code').querySelectorAll('input[type="text"]');
   let result = ''; // 清空之前的结果
      inputs.forEach(input => {
          result += input.value; // 将每个输入框的值拼接到结果字符串中
        });
      jQuery.ajax({
        url: "/wp-json/imessage/api/login",
        type: 'POST',
        dataType: 'json',
        data: {code:result},
        success: (res) => {
            console.log(res)
            if(res.code ==1){
                window.location.href = res.data.redirect;
            }else {
                animateWave(0,20);
            }
        },
        error: (res) => {
          console.log(res);
        }
    })
  });

  const closeDialogBtn = $('#closeDialogBtn');
  closeDialogBtn.on('click', function() {
    overlay.css('display', 'none');
    dialog.css('display', 'none');
  });
});
JS;
        $css =<<<CSS
    .jump-input {
          width: 50px;
          height: 50px;
          text-align: center;
          line-height: 50px;
          font-size: 35px;
          border-width: 2px;
          border-radius: 10px;
          transition: border-color 0.1s ease-in-out;
    }
    .jump-input.error {
      border-color: red;
      animation: shake 0.5s ease-in-out;
    }
    
    @keyframes shake {
      0%, 100% {
        transform: translateY(0);
      }
      25%, 75% {
        transform: translateY(-20px);
      }
      50% {
        transform: translateY(20px);
      }
    }

CSS;
        return $html_link." | <a type='button' id='showDialogBtn' >验证码登陆</a>"."<script>$js</script><style>$css</style>";
    }


    public function cookieExpiration($expiration, $user_id, $remember){
        return  3* 60 * MINUTE_IN_SECONDS;
    }
}
