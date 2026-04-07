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
$path = __DIR__ . '/src';
$protoDir = $path . '/protobuf';

// 使用 find 命令递归查找所有的 .proto 文件
$protoFiles = shell_exec("find {$path} -type f -name '*.proto'");

// 将找到的 .proto 文件路径转换为数组
$protoFilesArray = explode("\n", trim($protoFiles));

// 定义需要添加的选项
$namespaceOptionsTemplate = <<<'EOT'
option php_namespace = "GrpcSdk\\\\Proto%s";
option php_metadata_namespace = "GrpcSdk\\\\GPBMetadata%s";
EOT;

// 辅助函数：将字符串转换为驼峰命名法
function camel_case(string $str): string
{
    return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $str))));
}

// 辅助函数：将路径转换为大驼峰命名法
function convertToPascalCase(string $path): string
{
    $parts = explode('/', $path);
    $pascalCaseParts = array_map(function ($part) {
        return ucfirst(camel_case($part));
    }, $parts);
    return implode('\\', $pascalCaseParts);
}

// 遍历每个 .proto 文件并添加选项
foreach ($protoFilesArray as $file) {
    if (!empty($file)) {
        // 读取文件内容
        $content = file_get_contents($file);

        // 获取文件相对于 protoDir 的相对路径
        $relativePath = substr($file, strlen($protoDir) + 1);
        $directoryPath = dirname($relativePath);

        // 如果是根目录下的文件，则设置命名空间为 "GrpcSdk\Proto"
        if ($directoryPath === '.') {
            $namespace = '';
        } else {
            // 将目录路径转换为大驼峰命名法
            $namespace = '\\\\' . convertToPascalCase($directoryPath);
        }

        // 生成具体的选项
        $namespaceOptions = sprintf($namespaceOptionsTemplate, $namespace, $namespace);

        // 检查是否已经包含选项
        if (strpos($content, 'option php_namespace') === false) {
            // 在 package 之后插入选项
            $content = preg_replace('/^package\s+[^;]+;$/m', '$0' . "\n" . $namespaceOptions, $content);
            // 写回文件
            file_put_contents($file, $content);
        }
    }
}

// 构建 protoc 命令
$protocCommand = "protoc -I {$path} --php_out={$path}";
foreach ($protoFilesArray as $file) {
    if (!empty($file)) {
        $protocCommand .= " {$file}";
    }
}

// 执行 protoc 命令
exec($protocCommand);

echo 'Build Protobuf Success' . PHP_EOL;
