<?php


return [
    "menus" => [
        [
            "page_title" => "iMessage",
            "menu_title" => "iMessage",
            "capability" => 'manage_options',
            "menu_slug" => "index",
            "icon_url" => 'dashicons-align-full-width'
        ],
        [
            "parent_slug" => "index/index",
            "page_title" => "消息",
            "menu_title" => "消息",
            "menu_slug" => "index/message",
        ],

        [
            "parent_slug" => "index/index",
            "page_title" => "扫蓝",
            "menu_title" => "扫蓝",
            "menu_slug" => "index/saolan",
        ],
        [
            "parent_slug" => "index/index",
            "page_title" => "Apple ID",
            "menu_title" => "Apple ID",
            "menu_slug" => "index/apple",
        ],
        [
            "parent_slug" => "index/index",
            "page_title" => "邮箱",
            "menu_title" => "邮箱",
            "menu_slug" => "index/mail",
        ],
        [
            "parent_slug" => "index/index",
            "page_title" => "接码",
            "menu_title" => "接码",
            "menu_slug" => "index/phone",
        ],
        [
            "parent_slug" => "index/index",
            "page_title" => "令牌",
            "menu_title" => "令牌",
            "menu_slug" => "index/token",
        ],
        [
            "parent_slug" => "index/index",
            "page_title" => "路由",
            "menu_title" => "路由",
            "menu_slug" => "index/rules",
        ],
        [
            "parent_slug" => "index/index",
            "page_title" => "教程",
            "menu_title" => "教程",
            "menu_slug" => "index/tutorial",
        ],
        [
            "parent_slug" => "index/index",
            "page_title" => "记事本",
            "menu_title" => "记事本",
            "menu_slug" => "index/book",
        ],
        [
            "parent_slug" => "index/index",
            "page_title" => "任务",
            "menu_title" => "任务",
            "menu_slug" => "index/task",
        ],
        [
            "parent_slug" => "index/index",
            "page_title" => "设置",
            "menu_title" => "设置",
            "menu_slug" => "index/setting",
        ],
    ],
    'settings' => [
        "auth" => [
            'option_group' => 'imessage_group',
            'page' => 'imessage_group_auth',
            'section_id' => 'auth',
            "section_description" => '通过授权开启功能',
            'fields' => [
                [
                    'id' => 'code',
                    "title" => "code",
                    'args' => [
                        "tag" => "text",
                        "defaultValue" => "秘文",
                        "description" => "",
                        'options' => [
                            "class" => "regular-text code"
                        ]
                    ],
                ],
                [
                    'id' => 'PrivateKey',
                    "title" => "SecretKey",
                    'args' => [
                        "tag" => "text",
                        "defaultValue" => "",
                        "description" => "私钥",
                        'options' => [
                            "class" => "regular-text code"
                        ]
                    ],
                ],
                [
                    'id' => 'PublicKey',
                    "title" => "PublicKey",
                    'args' => [
                        "tag" => "text",
                        "defaultValue" => "",
                        "description" => "公钥",
                        'options' => [
                            "class" => "regular-text code"
                        ]
                    ],
                ],
            ]
        ],
        "okx" => [
            'option_group' => 'imessage_group',
            'page' => 'imessage_group_okx',
            'section_id' => 'okx',
            "section_description" => 'USDT支付api密钥配置',

            'fields' => [
                [
                    'id' => 'APIKey',
                    "title" => "APIKey",

                    'args' => [
                        "tag" => "password",
                        "defaultValue" => "",
                        "description" => "",
                        'options' => [
                            "class" => "regular-text code"
                        ]
                    ],
                ],
                [
                    'id' => 'SecretKey',

                    "title" => "SecretKey",
                    'args' => [
                        "tag" => "password",
                        "defaultValue" => "",
                        "description" => "",
                        'options' => [
                            "class" => "regular-text code"
                        ]
                    ],
                ],
                [
                    'id' => 'Passphrase',
                    "title" => "Passphrase",
                    'args' => [
                        "tag" => "password",
                        "defaultValue" => "",
                        "description" => "",
                        'options' => [
                            "class" => "regular-text code"
                        ]
                    ],
                ],
            ]
        ],
        'mail' => [
            'option_group' => 'imessage_group',
            'page' => 'imessage_group_mail',
            'section_id' => 'mail',
            "section_description" => 'SMTP服务设置,配置后可接收邮件通知',
            'fields' => [
                [
                    'id' => 'host',
                    "title" => "服务器地址",
                    'args' => [
                        "tag" => "text",
                        "defaultValue" => "",
                        "description" => "smtp服务器地址",
                        'options' => [
                            "placeholder" => 'smtp.qq.com',
                            "class" => "regular-text code"
                        ]
                    ],
                ],
                [
                    'id' => 'port',
                    "title" => "端口号",
                    'args' => [
                        "tag" => "text",
                        "defaultValue" => "465",
                        'options' => [
                            "placeholder" => "465",
                            "class" => "regular-text code"
                        ]
                    ],
                ],
                [
                    'id' => 'encryption',
                    "title" => "加密方式",
                    'args' => [
                        "tag" => "dropDownList",
                        "items" => [
                            'ssl' => "SSL",
                            'tls' => "TLS",
                            'STARTTLS' => "STARTTLS"
                        ],
                        "defaultValue" => "ssl",
                        "options" => [
                            "class" => "regular-text code"
                        ]
                    ],
                ],
                [
                    'id' => 'username',
                    "title" => "用户名",
                    'args' => [
                        "tag" => "text",
                        "description" => "登录smtp服务的用户名",
                        'options' => [
                            "placeholder" => 'xxxx@qq.com',
                            "class" => "regular-text code"
                        ],
                    ],
                ],
                [
                    'id' => 'password',

                    "title" => "密码",
                    'args' => [
                        "tag" => "password",
                        "defaultValue" => "",
                        "description" => "登录smtp服务的密码",
                        'options' => [
                            "class" => "regular-text code"
                        ]
                    ],
                ],
                [
                    'id' => 'nickname',

                    "title" => "发件人昵称",
                    'args' => [
                        "tag" => "text",
                        'options' => [
                            "placeholder" => get_option("blogname"),
                            "class" => "regular-text code"
                        ],
                    ],
                ],
                [
                    'id' => 'from',
                    "title" => "发件人邮箱",
                    'args' => [
                        "tag" => "text",
                        'options' => [
                            "placeholder" => get_option("blogname"),
                            "class" => "regular-text code"
                        ],
                    ],
                ],
            ]
        ],
        'ipinfo' => [
            'option_group' => 'imessage_group',
            'page' => 'imessage_group_ipinfo',
            'section_id' => 'ipinfo',
            "section_description" => '通过ip解析无理地址',
            'fields' => [
                [
                    'id' => 'token',
                    "title" => "服务器地址",
                    'args' => [
                        "tag" => "text",
                        "defaultValue" => "",
                        "description" => "ipinfo.io的密钥",
                        'options' => [
                            "placeholder" => '密钥',
                            "class" => "regular-text code"
                        ]
                    ],
                ],
            ]
        ],
    ],
];