<?php
//应用公共配置文件
return [
    'auth_key' => 'xoll-lximf-elxe',
    'userauthid' => 'lbauth',
    'url_default_action' => 'adminbase/System/Index/index',
    'system_name' => '后台管理系统',
    'cmlframework_system_route' => [
        'cmlframeworkstaticparse' => '\\Cml\\Tools\\StaticResource::parseResourceFile',
        'cml_calc_veryfy_code' => '\\Cml\\Vendor\\VerifyCode::calocVerify'
    ],
    'page_num' => 15,
    //'static__path' => '/',
    'html_theme' => 'kit_admin',
    'administratorid' => 1,//后台超级管理员id
    'url_html_suffix' => '.shtml',
    'url_model' => 1,
    'oss_login' => true,
    'code_msg' => require __DIR__ . DIRECTORY_SEPARATOR . 'code.php',
    'upload_path' => CML_PROJECT_PATH . DIRECTORY_SEPARATOR . 'public/uploads',
];
