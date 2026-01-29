<?php
/**
 * SwaggerController.php
 * @author cdyun(121625706@qq.com)
 * @date 2025/11/3 1:30
 */

declare(strict_types=1);

namespace Cdyun\ThinkphpSwagger;

use OpenApi\Annotations\Info;
use OpenApi\Generator;

class SwaggerController
{
    /**
     * Swagger主页
     * @author cdyun(121625706@qq.com)
     * @date 2025/11/3 1:30
     */
    public function index()
    {        
        $template = '
<html lang="zh-cn">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="SwaggerUI" />
    <title>SwaggerUI</title>
    <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@5.31.0/swagger-ui.css" />
  </head>
<body>
  <div id="swagger-ui"></div>
  <script src="https://unpkg.com/swagger-ui-dist@5.31.0/swagger-ui-bundle.js" crossorigin></script>
  <script src="https://unpkg.com/swagger-ui-dist@5.31.0/swagger-ui-standalone-preset.js" crossorigin></script>
  <script>
    window.onload = () => {
      window.ui = SwaggerUIBundle({
        url: "/swagger/openapi.json",
        dom_id: "#swagger-ui",
        deepLinking: true,
        presets: [
        SwaggerUIBundle.presets.apis,
        SwaggerUIStandalonePreset
    ],
        plugins: [
        SwaggerUIBundle.plugins.DownloadUrl
    ],
        layout: "StandaloneLayout"
      });
};
</script>
</body>
</html>
';
        return $template;
    }

    /**
     * openapi
     * @author cdyun(121625706@qq.com)
     * @date 2025/11/3 1:30
     */
    public function openapi()
    {
        $openapi = (new Generator())->generate([app()->getRootPath() . 'app/v2']);
        $openapi->info = new Info([
            'title' => 'V1应用接口文档',
            'description' => '让开发更简单、更通用、更流行。',
        ]);
        header('Content-Type: application/json');
        return json($openapi);
    }
}
