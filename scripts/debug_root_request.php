<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::create('/', 'GET');
$response = $kernel->handle($request);

http_response_code($response->getStatusCode());
echo "Status: " . $response->getStatusCode() . "\n";
$content = $response->getContent();
$snippet = is_string($content) ? substr($content, 0, 400) : '(non-string response)';
echo "Content snippet: \n" . $snippet . "\n";

$kernel->terminate($request, $response);
