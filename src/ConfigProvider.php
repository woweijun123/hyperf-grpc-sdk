<?php

namespace GrpcSdk;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            "dependencies"=>[
            ],
            // 组件默认配置文件，即执行命令后会把 source 的对应的文件复制为 destination 对应的的文件
            'publish' => [
                [
                    'id' => 'config',
                    'description' => 'Sdk 配置文件，直接获取环境变量数据', // 描述
                    // 建议默认配置放在 publish 文件夹中，文件命名和组件名称相同
                    'source' => dirname(__DIR__) . '/publish/grpc.php',  // 对应的配置文件路径
                    'destination' => BASE_PATH . '/config/autoload/grpc.php', // 复制为这个路径下的该文件
                ],
            ],
            // 亦可继续定义其它配置，最终都会合并到与 ConfigInterface 对应的配置储存器中
        ];
    }
}
