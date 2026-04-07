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

// 定义需要添加的选项
$namespaceOptions = <<<'EOT'
// --- PHP 导出配置 ---
option php_namespace = "GrpcSdk\\\\Proto";
option php_metadata_namespace = "GrpcSdk\\\\GPBMetadata";
EOT;

// 遍历每个 .proto 文件并添加选项
foreach ($protoFilesArray as $file) {
    if (!empty($file)) {
        // 读取文件内容
        $content = file_get_contents($file);

        // 检查是否已经包含选项
        if (strpos($content, 'option php_namespace') === false) {
            // 在 package 之后插入选项
            $content = preg_replace('/(package\s+[^;]+;)/', '$1' . "\n" . $namespaceOptions, $content);

            // 写回文件
            file_put_contents($file, $content);
        }
    }
}

// 构建 protoc 命令
$protocCommand = "protoc -I {$protoDir} --php_out={$outputDir}";
foreach ($protoFilesArray as $file) {
    if (!empty($file)) {
        $protocCommand .= " {$file}";
    }
}

// 执行 protoc 命令
exec($protocCommand);

echo 'Build Protobuf Success' . PHP_EOL;
