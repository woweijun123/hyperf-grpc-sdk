<?php

//获取目录
function getDirListName($path)
{
    if (!is_dir($path)) {
        return false;
    }
    $arr = [];
    $data = scandir($path);
    foreach ($data as $value) {
        if ($value != '.' && $value != '..' && is_dir($path.'/'.$value)) {
            $arr[] = $value;
        }
    }
    return $arr;
}


//转换文件
function ABtoa_b($string)
{
    $arr = preg_split("/(?=[A-Z])/", $string);
    foreach ($arr as $k => $item) {
        if (!$item) {
            unset($arr[$k]);
            continue;
        }
        $arr[$k] = strtolower($item);
    }
    return implode('_', $arr);
}

//换行输出
function p($i = 0, $line = '')
{
    echo str_pad('', $i, ' '), $line, "\n";
}


function getDefaultHost($serverKey, $default = '')
{

    switch ($serverKey) {
        case 'example':
            $default = "'localhost:81'";
            break;
            //common修改了服务名称，单独处理
        case 'common':
            $default = "'common-v2-service:81'";
            break;
            //common修改了服务名称，单独处理
        case 'riskcontrol':
            $default = "'risk-control-service:81'";
            break;
        default:
            $default = "'" . implode('-', explode('_', $serverKey)) . "-service:81'";
    }

    return $default;
}


function putConfig()
{

    $path = __DIR__ . '/src/protobuf/proto';
    $dirNameList = getDirListName($path);

    ob_start();
    $title = <<<eof
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
return [
eof;
    p(0, $title);
    foreach ($dirNameList as $item) {
        $str = ABtoa_b($item);
        $strUp = strtoupper($str);
        $defaultHost = getDefaultHost($str);
        $p = <<<eof
"$str" => [
        'host' => env("{$strUp}_GRPC_HOST",{$defaultHost}),
        'timeout' => env("{$strUp}_GRPC_TIMEOUT", 10)
    ],
eof;
        p(4, $p);
        p();
    }
    p(0, '];');
    $content = ob_get_clean();

    $configFile = __DIR__ . '/publish/grpc.php';
    file_put_contents($configFile, $content);
}


putConfig();



