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
$path =__DIR__ ;
shell_exec("protoc -I {$path}/src/protobuf/  --php_out={$path}/src/ --hyperf_out={$path}/src/    {$path}/src/protobuf/**/**/*.proto");
shell_exec("protoc -I {$path}/src/protobuf/  --php_out={$path}/src/ --hyperf_out={$path}/src/    {$path}/src/protobuf/**/**/**/*.proto");
shell_exec("protoc -I {$path}/src/protobuf/  --php_out={$path}/src/ --hyperf_out={$path}/src/    {$path}/src/protobuf/**/*.proto");
shell_exec("php {$path}/format_config.php");
echo 'Build Protobuf Success' . PHP_EOL;
