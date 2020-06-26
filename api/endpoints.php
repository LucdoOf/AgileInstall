<?php

use AgileAPI\Controllers\AuthController;
use AgileAPI\Controllers\BasketsController;
use AgileAPI\Controllers\CommandsController;
use AgileAPI\Controllers\ProductsController;
use AgileAPI\Controllers\StatsController;
use AgileAPI\Controllers\UsersController;

return FastRoute\cachedDispatcher(function (FastRoute\RouteCollector $r) {

    $r->addRoute('GET', '/products', ProductsController::class . '.getProducts');

    $r->addRoute('POST', '/products/{id:\d+}/update', ProductsController::class . ".updateProduct");

    $r->addRoute('POST', '/products/{id:\d+}/commands/page/{page:\d+}', ProductsController::class . ".getProductLinkedCommands");

    $r->addRoute('PUT', '/products/create', ProductsController::class . ".createProduct");

    $r->addRoute('GET', '/baskets', BasketsController::class . ".getBaskets");

    $r->addRoute('GET', '/baskets/{id:\d+}/entries', BasketsController::class . ".getBasketBasketEntries");

    $r->addRoute('GET', '/baskets-entries', BasketsController::class . ".getBasketEntries");

    $r->addRoute('GET', '/commands', CommandsController::class . ".getCommands");

    $r->addRoute('POST', '/commands/page/{page:\d+}', CommandsController::class . ".getCommandsPage");

    $r->addRoute('GET', '/commands/{id:\d+}/basket', CommandsController::class . ".getCommandBasket");

    $r->addRoute('POST', '/commands/{id:\d+}/status/update', CommandsController::class . ".updateCommandStatus");

    $r->addRoute('GET', '/stats/commands/today', StatsController::class . ".todayCommands");

    $r->addRoute('POST', '/users/page/{page:\d+}', UsersController::class . ".getUsersPage");

    $r->addRoute('GET', '/users', UsersController::class . ".getUsers");

    $r->addRoute('POST', '/auth', AuthController::class . ".auth");

    $r->addRoute('GET', '/commands/{id:\d+}/history', CommandsController::class . ".getCommandHistory");

    $r->addRoute('PUT', '/commands/create', CommandsController::class . ".createCommand");

    $r->addRoute('DELETE', '/commands/{id:\d+}/delete', CommandsController::class . '.deleteCommand');

    $r->addRoute('GET', '/stats/commands/month', StatsController::class . '.monthCommands');

}, [
    'cacheFile'     => SHARE_ROOT . '/data/cache/endpoints.cache',
    'cacheDisabled' => true,
]);
