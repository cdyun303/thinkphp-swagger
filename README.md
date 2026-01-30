Thinkphp Swagger 扩展
=====

### 安装

```
composer require cdyun/thinkphp-swagger
```

### - 服务注入
在扩展的composer.json文件中增加如下定义：
```
"extra": {
    "think": {
        "services": [
            "Cdyun\\ThinkphpSwagger\\SwaggerService"
        ]
    }
},
```
实现系统初始化过程中自动注册SwaggerService服务：
```
<?php
namespace Cdyun\ThinkphpSwagger;

use think\Route;
use think\Service;

class SwaggerService extends Service
{
    public function boot()
    {
        // 注册路由
        $this->registerRoutes(function (Route $route) {
            // 获取swagger Json信息，http://xxx.com/swagger/openapi.json
            $route->get('swagger/openapi', '\\Cdyun\\ThinkphpSwagger\\SwaggerController@openapi')->ext('json');
            // 访问swagger页面，http://xxx.com/swagger
            $route->get('swagger', '\\Cdyun\\ThinkphpSwagger\\SwaggerController@index');
        });
    }
}
```

### - （配置文件）config/swagger.php
```PHP
<?php

return [
    // 应用分组
    'groups'=>[
        // 应用名称
        'default'=>[
            // 标题，会替换OA\Info中标题
            'title'=>'通用接口文档',
            // 描述，会替换OA\Info中描述
            'description'=>'让开发变得更简单、更通用、更流行。',
        ],
    ]
];
```

### - 使用注意
###### 1、每个应用的OA\Info信息，必须且只能存在一个，所以建议写在每个应用控制器继承的 BaseController.php 上；
###### 2、OA\Info信息会被 config/swagger.php 中信息替换掉；
例： 应用V1
```
<?php
namespace app\v1\controller;

use support\base\BaseController;
use OpenApi\Attributes as OA;

#[OA\Info(version: '1.0.0', title: 'V1')]
class V1BaseController extends BaseController
{
    ...其他
}
```
例： 应用V2
```
<?php
namespace app\v2\controller;

use support\base\BaseController;
use OpenApi\Attributes as OA;

#[OA\Info(version: '2.0.0', title: 'V2')]
class V2BaseController extends BaseController
{
    ...其他
}
```