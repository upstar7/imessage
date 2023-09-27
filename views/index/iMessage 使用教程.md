## iMessage 使用教程

   - iMessage 是一个管理多个手机,Mac客户端插件支持一对n

  - 通过在服务器端新增任务，自动分配到多个客户端

  - 插件并不能解决ID封号限制

  - 使用本插件需要提供服务器和(域名可选)

  - 获取`插件`和`快捷指令`请联系管理员

    

### 使用



#### 安装`wordpress`

   ~~~~sh
#假设你的网站根目录为例/var/www/html/wordpress
cd /var/www/html
sudo wget https://cn.wordpress.org/latest-zh_CN.zip
sudo unzip latest-zh_CN.zip
# 创新资源发布目录和运行缓存目录
cd wordpress
sudo mkdir wp-content/uploads/assets
sudo mkdir wp-content/uploads/runtime
sudo chmod -R 777 wp-content/uploads/assets
sudo chmod -R 777 wp-content/uploads/runtime
# 创建htaccess
sudo touch .htaccess
# 将下面复制到.htaccess文件中
#<IfModule mod_rewrite.c>
#	RewriteEngine On
#	RewriteBase /
#	RewriteRule ^wp-content/plugins/imessage - [R=404,L]
#	RewriteCond %{REQUEST_FILENAME} !-f
#	RewriteCond %{REQUEST_FILENAME} !-d
#	RewriteRule . /index.php [L]
#</IfModule>
   ~~~~


#### 上传iMessage插件

   ~~~sh
# 将插件上传到中/var/www/html/wordpress/wp-content/plugins目录中
# 解压
sudo unzip imessage
cd imessage
sudo chmod -R 777 runtime
   ~~~

#### 启用插件

   ~~~sh
# 打开浏览器输入你的域名，例如http://xxx.com
# 并安装向导完成网站数据库，管理员目录等基本信息
# 登陆后台 入口url 例如: http://xxx.com/wp-admin
   ~~~

##### 如图点击启用

   <img src="@/截屏2023-05-26 20.10.45.png" alt="截屏2023-05-26 20.10.45" style="width: 50%"/>

##### 进入消息

   <img src="@/截屏2023-05-26 20.14.50.png" alt="截屏2023-05-26 20.14.50" style="width: 50%"/>


##### 创建任务

   <img src="@/截屏2023-05-26 20.20.23.png" alt="截屏2023-05-26 20.20.23" style="width: 50%"/>

##### 获取token

   <img src="@/截屏2023-05-26 20.27.12.png" alt="截屏2023-05-26 20.27.12" style="width: 50%"/>

#### 多种启动方式

- 名称为`iMessage`的快捷指令是核心，运行一次执行一个任务。
- 为了让快捷指令长时间稳定运行而无需人工干预,可选三种方式启动`iMessage`的快捷指令
- 方式一: 使用一个无限重复的快捷指令,重复启动核心快捷指令`iMessage`
   - 缺点: 任何一次重复意外的退出,将导致客户端离线无法再次起启动.
   - 建议:强烈不建议
   - 例如:
     <img src="@/IMG_0022.PNG" alt="IMG_0023.PNG" style="zoom: 50%;" />
- 方式二: 使用自动化定时启动核心快捷指令`iMessage`(推荐使用这种方式启动)
   - 优点: 能够保证核心快捷指令`iMessage`正常运行，且不会干扰下一次核心快捷指令`iMessage`启动
   - 缺点：在第一次操作时比较花时间。
   - 建议：核心快捷指令`iMessage`速率大概为:15个任务每分钟,建议每隔5分钟启动一次，一次执行70-80个任务
     <img src="@/IMG_0024.PNG" alt="IMG_0024" style="zoom:50%;" /><img src="@/IMG_0025.PNG" alt="IMG_0025" style="zoom:50%;" />
- 方式三:使用闹钟启动.由闹钟启动核心快捷指令`iMessage`,并在启动核心快捷指令`iMessage`执行结束时,创建一个新的闹钟，用来启动下一次启动核心快捷指令`iMessage`
   - 优点：相对于第二中启动方式,无效创建多个自动化，减少第一次重复操作
   - 缺点：回出现频繁的闹钟提示
   - 建议：核心快捷指令`iMessage`速率大概为:15个任务每分钟,建议每隔5分钟启动一次，一次执行70-80个任务
     <img src="@/IMG_0026.PNG" alt="IMG_0026" style="zoom:50%;" /><img src="@/IMG_0027.PNG" alt="IMG_0027" style="zoom:50%;" />
- 说明：方式二和方式三运行效果上是一样的,不会出现一次任务意外退出,无法再次启动.请根据你的情况自行选择

#### 核心快捷指令`iMessage`如图：
   <img src="@/IMG_0028.PNG" alt="IMG_0028" style="zoom:50%;" />

## Mac OS 脚本教程

### 环境配置

 获取脚本文件请联系管理员

   ~~~sh
# +-------------------------------------------------------------------------------------------------
# | 1.运行下面命令 安装homebrew
# |   /bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
# | 2. 安装结束 根据提示将添加环境变量
# | 3.运行下面命令 安装jq
# |   brew install jq
# | 4.将jq添加到环境变量
# |   nano ~/.zshrc
# |   在打开的文本编辑器中，将以下行添加到文件的末尾：
# |   export PATH="/usr/local/bin:$PATH"
# |   输入以下命令来使配置文件生效：
# |   source ~/.zshrc
# | 5.修改iMessage.sh 变量$host和$token 值 
# | 6.给iMessage.sh 添加执行权限
# |     sudo chmod +x /<你的存放路径>/iMessage.sh
# | 7. 运行一次测试 
# |    sudo bash /<你的存放路径>/iMessage.sh
# +-------------------------------------------------------------------------------------------------
   ~~~

### 配置定时启动

   ~~~sh
# +-------------------------------------------------------------------------------------------------
# | 1.添加定时启动
# |   which cron
# |   /usr/sbin/cron 的cron文件拖动到 "设置" -> "隐私与安全" -> "完全磁盘访问权限" 中并开启权限
# | 2.配置定时启动规则
# |   crontab -e(注意"crontab -e"和"sudo crontab -e"两个命令创建的是不同定时任务推荐使用用户级命令"crontab -e")
# |   按i输入下面一行规则, 按esc输入":wq"+"回车" (保存退出)
# |   */1 0-23 * * * /bin/bash /<你的存放路径>/iMessage.sh
# |   表示:每天0点至23点，每个1分钟运行一次"/<你的存放路径>/iMessage.sh"
# | 3. 取消定时任务
# |    crontab -e
# |   按i删除规则,或者在规则前添加"#"注释调, 按esc输入":wq"+"回车" (保存退出)
# +-------------------------------------------------------------------------------------------------
   ~~~


### 接收第三方数据的说明

   ~~~php
<?php
# +--------------------------------------------------
# | 关于imessage后台,如何接收第三方数据的说明
# | 只是需要第三程序,携带token向后台发送POST请求即可
# | 下面已PHP为例简单例子
# +--------------------------------------------------

// 你的服务器url
$host = "http://127.0.0.1";
// 你的访问令牌,从你的后台获取
$token = "63ebc8a2-42a5-11ee-8d22-00163e006044";
// 你要传递的数据
$data = [
    'apple_id' => "必须",
    'first_name' => "姓(可选)",
    'last_name' => "名(可选)",
    'date_of_birth' => "出生日期(可选)",
    'country' => "账号地区(可选)",
    'apple_password' => "密码",
    'phone' => "手机号(可选)",
    'phone_country' => "手机号地区(可选)",
    'phone_url' => "解码url",
    'email' => "邮箱(可选)",
    'email_password' => '邮箱密码(可选)'
];

$headers = [
    'token: ' . $token,
    'Content-Type: application/x-www-form-urlencoded'
];

$options = [
    'http' => [
        'header' => implode("\r\n", $headers),
        'method' => 'POST',
        'timeout' => 10,// 超时时间
        'content' => http_build_query($data)
    ]
];

$context = stream_context_create($options);
$response = file_get_contents($host . '/wp-json/imessage/api/apple', false, $context);

if ($response === false) {
    echo 'Error: ' . error_get_last()['message'];
} else {
    echo $response;
}
   ~~~