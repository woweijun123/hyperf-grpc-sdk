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

// 使用 find 命令递归查找所有的 .proto 文件
$protoFiles = shell_exec("find {$protoDir} -type f -name '*.proto'");

// 将找到的 .proto 文件路径转换为数组
$protoFilesArray = explode("\n", trim($protoFiles));

// 构建 protoc 命令
$protocCommand = "protoc -I {$protoDir} --php_out={$outputDir}";
foreach ($protoFilesArray as $file) {
    if (!empty($file)) {
        $protocCommand .= " {$file}";
    }
}

// 执行 protoc 命令
exec($protocCommand);
// 执行额外的配置文件格式化
exec("php {$baseDir}/format_config.php");
echo 'Build Protobuf Success' . PHP_EOL;
