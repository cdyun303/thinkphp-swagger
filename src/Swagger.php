<?php
/**
 * Swagger.php
 * @author cdyun(121625706@qq.com)
 * @date 2026/1/30 01:08
 */

declare (strict_types=1);

namespace Cdyun\ThinkphpSwagger;

use think\Config;

class Swagger
{
    /**
     * @var Config|null
     */
    private $config = null;

    /**
     * 架构方法 设置参数
     * @access public
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * 获取Swagger API文档的URL列表
     * @return array
     */
    public function getUrls(): array
    {
        $groups = $this->configure('groups');
        $urls = [];
        foreach ($groups as $key => $vo) {
            $urls[] = ['url' => '/swagger/openapi.json?urls.primaryName=' . $vo['title'], 'name' => $vo['title']];
        }
        return $urls;
    }

    /**
     * 配置Swagger
     * @param string|null $config
     * @return mixed
     */
    protected function configure(?string $config = null): mixed
    {
        if (is_null($config)) {
            $val = $this->config->get('swagger', []);
        } else {
            $val = $this->config->get('swagger.' . $config, []);
        }
        return $val;
    }


    /**
     * 获取Swagger 应用API信息
     * @param string|null $name
     * @return array
     */
    public function getUrlInfo($name = null): array
    {
        $info = [];
        $groups = $this->configure('groups');
        if (empty($groups)) {
            return $info;
        }
        foreach ($groups as $key => $vo) {
            if ($name == null || $vo['title'] == $name) {
                $info = [
                    'title' => $vo['title'],
                    'description' => $vo['description'],
                    'name' => $key,
                ];
                break;
            }
        }
        return $info;
    }
}