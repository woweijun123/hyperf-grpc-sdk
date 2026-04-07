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
$baseDir = __DIR__;
$protoDir = "{$baseDir}/src/protobuf";
$outputDir = "{$baseDir}/src";

// 构建所有匹配的.proto文件的通配符表达式
$protoFilesPattern = "{$protoDir}/**/*.proto";

// 使用单个shell_exec调用来处理所有的.proto文件
exec("protoc -I {$protoDir} --php_out={$outputDir} {$protoFilesPattern}");
// 执行额外的配置文件格式化
exec("php {$baseDir}/format_config.php");
echo 'Build Protobuf Success' . PHP_EOL;
