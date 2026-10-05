<?php

echo "STEP 1";

require __DIR__.'/../vendor/autoload.php';

echo "STEP 2";

$app = require_once __DIR__.'/../bootstrap/app.php';

echo "STEP 3";

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "STEP 4";

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
)->send();

echo "STEP 5";

$kernel->terminate($request, $response);

echo "STEP 6";
