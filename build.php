<?php

$src = __DIR__ . '/src';
$protoDir = $src . '/protobuf';
$files = [];

foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($protoDir)) as $file) {
    if ($file->getExtension() !== 'proto')
        continue;
    $path = $file->getRealPath();

    // 1. 大驼峰 Namespace 处理：example/sub_module -> Example/SubModule
    $rel = ltrim(str_replace($protoDir, '', dirname($path)), DIRECTORY_SEPARATOR);
    $ns = '';
    if ($rel) {
        $parts = explode(DIRECTORY_SEPARATOR, $rel);
        $pascalParts = array_map(fn($p) => str_replace(' ', '', ucwords(str_replace('_', ' ', $p))), $parts);
        $ns = '\\\\\\\\' . implode('\\\\\\\\', $pascalParts);
    }

    // 2. 注入配置 (Namespace 必须包含 GrpcSdk 才能匹配 PSR-4)
    $opt = "\noption php_namespace = \"Proto$ns\";\noption php_metadata_namespace = \"GPBMetadata$ns\";";
    $content = file_get_contents($path);
    if (!str_contains($content, 'option php_namespace')) {
        file_put_contents($path, preg_replace('/^(package|syntax).+;/m', "$0$opt", $content, 1));
    }
    $files[] = escapeshellarg($path);
}

// 3. 编译并自动归位
exec("protoc -I $src --php_out=$src " . implode(' ', $files));

echo "Build Success\n";
