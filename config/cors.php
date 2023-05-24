<?php

return function ($app) {
    $app->add(new Tuupola\Middleware\CorsMiddleware([
        'origin' => ['http://localhost:3000'],
        'methods' => ['GET', 'POST', 'PUT', 'DELETE'],
        'headers.allow' => ['Content-Type', 'Authorization'],
        'headers.expose' => [],
        'credentials' => true,
        'cache' => 0,
    ]));
};