<?php
/**
 *
 * @package crud
 */

return [
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'QYQnE3E_DrHDTD6VTtn7XnlJAcGPlw35',
            "enableCsrfValidation"=>false,
//                "enableCsrfValidation" => true,
        ],
        'db' => [
            'class' => '\yii\db\Connection',
            'dsn' => 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME,
            'username' => DB_USER,
            'password' => DB_PASSWORD,
            'charset' => DB_CHARSET,
            "tablePrefix"=>DB_TABLE_PREFIX,
        ],
    ],
];