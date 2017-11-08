<?php
// [ 应用入口文件 ]
namespace think;

// 加载基础文件
require __DIR__ . '/../../framework/base.php';

// 执行应用并响应
Container::get('app')->run()->send();