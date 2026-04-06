# Grpc-SDK

相关使用场景请查看：https://w3nu1yaadv.feishu.cn/docs/doccnTUy4gmxzlMeYu0jrjmJoAh
编写规范请查看：https://w3nu1yaadv.feishu.cn/docs/doccnBtW3ej4qRPLhQQZX5kgG9g

## 前言
- 使用该包,由于需要对proto文件动态build,你开发环境中必须要有以下依赖
```bash
- protoc                  ##官方protoc
- Protobuf                ##PHP 扩展（如果需要使用客户端的gRPC Server）
- protoc-gen-hyperf 插件   ##使用说明：https://w3nu1yaadv.feishu.cn/docs/doccnWUXmXMQXUGuom70zC9HXSp

```

### 运行命令
```bash
# 安装包
composer  require  riven/hyperf-grpc-sdk -v

```

### 添加构建文件和命令(可选，若框架已存在，可忽略)

- 拷贝`创建build.php` 文件到项目根目录，用于构建proto文件生成代码

```php
//build.php
<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
$path = __DIR__ . '/vendor/riven/hyperf-grpc-sdk';
if (is_dir($path)) {
    shell_exec("php {$path}/build.php");
}

```

- composer.json 添加脚本命令
```json
 "scripts": {
    ......
    "pre-autoload-dump": [
      "php build.php"              
    ]
  },
 "extra": {
    "scripts-dev": {
      "post-autoload-dump": [
        ......
        "php bin/hyperf.php vendor:publish riven/hyperf-grpc-sdk -f"
      ]
    }
  }
```


## 使用说明

1. 首先执行composer dump-autoload 检查是否构建代码
2. 调用GrpcClient进行调用(注意参数类型)

```php
    /**
     * User: ZeMing Shao
     * @return string
     * @throws \Exception
     */
    public function hello()
    {
        $client = new \Proto\Example\UserGrpcClient();
        $user = new \Proto\Example\HiUser();
        $user->setName('小明');
        $user->setSex(1);
        $reply = $client->sayHello($user);
        return $reply->serializeToJsonString();

    }
```



## 本包开发注意事项

- 示例文件编译代码前：
```yaml
.
├── LICENSE
├── README.md
├── composer.json
├── composer.lock
├── format_config.php                                
├── phpunit.xml
├── publish
│   └── grpc.php                               #各个服务配置文件，会根据各个服务的目录名称在构建后自动写入配置文件
├── src
│   ├── ConfigProvider.php                     
│   └── protobuf
│       └── proto                              #该目录为所有proto总目录，所有服务proto文件都应该写在其中
│           └── example                 #各个服务之间应该目录隔离(注意目录名称：多个单词请使用小驼峰写法书写，禁止使用下划线(包名需和目录名称一致))
│               ├── message.proto              #各个服务业务的proto文件，命名尽量以业务功能命名
│               └── user.proto
└── vendor

```

- 执行命令`composer dump-autoload` 编译命令后,具体命令可查看composer.json

- 示例文件编译代码后
```yaml
.
├── LICENSE
├── README.md
├── composer.json
├── composer.lock
├── format_config.php
├── phpunit.xml
├── publish
│   └── grpc.php
├── src
│   ├── ConfigProvider.php
│   ├── GPBMetadata                                   //生成的文件，不允许提交到git
│   │   └── Proto
│   │       └── Example
│   │           ├── Message.php
│   │           └── User.php
│   ├── Proto                                          //生成的文件，不允许提交到git
│   │   └── Example
│   │       ├── HiReply.php
│   │       ├── HiUser.php
│   │       ├── MessageGrpcClient.php
│   │       ├── MessageGrpcInterface.php
│   │       ├── MessageGrpcRoute.php
│   │       ├── MsgData.php
│   │       ├── MsgResp.php
│   │       ├── UserGrpcClient.php
│   │       ├── UserGrpcInterface.php
│   │       └── UserGrpcRoute.php
│   └── protobuf
│       └── proto
│           └── example
│               ├── message.proto
│               └── user.proto
└── vendor

```

### 服务端本地开发

1. 创建自己分支，基于prod。
```bash
git fetch origin
git checkout  -b  feature/xxxx prod
```
2. 按规范编写proto文件，并进行评审。
3. 执行composer dump-autoload 查看build.php文件是否符合预期。
4. grpc服务项目修改composer.json，将本包以path方式进行本地软链映射。
```yaml
......
"require": {
  ......
  "riven/hyperf-grpc-sdk": "dev-master"
},
"repositories": {
  "riven/hyperf-grpc-sdk": {"type": "path", "url": "../../package/grpc-sdk"}   //你的本地相对路径
}
......
```
5. grpc服务项目 执行`composer update riven/hyperf-grpc-sdk`，然后开始服务实现对应grpc接口功能。
6. 本地验证proto文件无误后，将sdk代码分支进行提交远程，若proto文件比较固定可申请合并并发布tag。
7. 注意grpc服务项目恢复composer.json修改的path代码,同时需要更新composer.lock再进行提交。






