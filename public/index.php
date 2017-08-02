<?php

require __DIR__ . '/../bin/bootstrap.php';
require __DIR__ . '/../bin/routes.php';

$routeInfo = $dispatcher->dispatch(
    $request->getMethod(),
    $request->getRequestUri()
);

if (is_callable($routeInfo[1])) {
    $params = $routeInfo[2] ?? [];
    $routeInfo[1]($params);
}
