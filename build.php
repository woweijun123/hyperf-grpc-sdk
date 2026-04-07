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
    $opt = "\noption php_namespace = \"GrpcSdk\\\\\\\\Proto$ns\";\noption php_metadata_namespace = \"GrpcSdk\\\\\\\\GPBMetadata$ns\";";
    $content = file_get_contents($path);
    if (!str_contains($content, 'option php_namespace')) {
        file_put_contents($path, preg_replace('/^(package|syntax).+;/m', "$0$opt", $content, 1));
    }
    $files[] = escapeshellarg($path);
}

// 3. 编译：输出到根目录（会生成 ./GrpcSdk/...）
if ($files ?? []) {
    exec("protoc -I $src --php_out=" . __DIR__ . " " . implode(' ', $files));

    // 4. 自动归位：抹掉 GrpcSdk 这一层，直接放进 src
    if (is_dir($gen = __DIR__ . '/GrpcSdk')) {
        // 将 GrpcSdk/Proto 移至 src/Proto，GrpcSdk/GPBMetadata 移至 src/GPBMetadata
        exec("cp -R $gen/* $src && rm -rf $gen");
    }
}

echo "Build Success\n";
