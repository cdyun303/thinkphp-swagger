<?php
/**
 * SwaggerService.php
 * @author cdyun(121625706@qq.com)
 * @date 2025/11/3 1:30
 */

declare(strict_types=1);

namespace Cdyun\ThinkphpSwagger;

use think\Route;
use think\Service;

class SwaggerService extends Service
{
    public function boot()
    {
        $this->registerRoutes(function (Route $route) {
            $route->get('swagger/openapi', '\\Cdyun\\ThinkphpSwagger\\SwaggerController@openapi')->ext('json');
            $route->get('swagger', '\\Cdyun\\ThinkphpSwagger\\SwaggerController@index');
        });
    }
}
