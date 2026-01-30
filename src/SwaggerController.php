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
    public function index(Swagger $wagger)
    {
        $urls = json_encode($wagger->getUrls(), JSON_UNESCAPED_UNICODE);
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
        urls: ' . $urls . ',
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
    public function openapi(Swagger $wagger)
    {
        $name = request()->param('urls_primaryName');
        $info = $wagger->getUrlInfo($name);
        $openapi = (new Generator())->generate([app()->getRootPath() . 'app/'. $info['name']], null, true);
        $openapi->info = new Info([
            'title' => $info['title'],
            'description' => $info['description'],
        ]);
        header('Content-Type: application/json');
        return json($openapi);
    }
}
