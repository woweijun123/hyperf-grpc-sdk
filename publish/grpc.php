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
    "example" => [
        'host' => env("EXAMPLE_GRPC_HOST",'localhost:81'),
        'timeout' => env("EXAMPLE_GRPC_TIMEOUT", 10)
    ],
];
