<?php
require __DIR__ . '/../vendor/autoload.php';
$openapi = OpenApi\Generator::scan([__DIR__ . '/../app']);
if (isset($openapi->info)) {
    echo $openapi->info->title;
} else {
    echo 'no-info';
}
